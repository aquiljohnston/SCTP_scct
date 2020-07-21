<?php
namespace app\controllers;

use Yii;
use app\models\task;
use app\controllers\BaseController;
use yii\data\Pagination;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\ServerErrorHttpException;
use yii\web\UnauthorizedHttpException;
use yii\data\ArrayDataProvider;
use linslin\yii2\curl;
use app\constants\Constants;

/**
 * TaskController implements the CRUD actions for task model.
 */
class TaskController extends BaseController
{
	/**
     * Add New Task Entry.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param $TimeCardID
     * @param $SundayDate
     * @param null $SaturdayDate
     * @param $timeCardProjectID
     * @return string
     * @throws \yii\web\HttpException
     * @internal param $SatudayDate
     */

    public function actionAddTaskEntry($weekStart = null, $weekEnd = null, $TimeCardID = null, $SundayDate = null, $SaturdayDate = null, $timeCardProjectID = null, $inOvertime = 'false')
    {
		try{
			//guest redirect
			if (Yii::$app->user->isGuest) {
				return $this->redirect(['/login']);
			}

			//convert SundayDate and SaturdayDate to MM/dd/YYYY format
			$SundayDate = date( "m/d/Y", strtotime(str_replace('-', '/', $SundayDate)));
			$SaturdayDate = date( "m/d/Y", strtotime(str_replace('-', '/', $SaturdayDate)));

			self::requirePermission("createTaskEntry");

			$model = new \yii\base\DynamicModel([
				'TimeCardID',
				'TaskName',
				'Date',
				'StartTime',
				'EndTime',
				'ChargeOfAccountType',
				'TimeReason',
				'Comments',
				'WeekStart',
				'WeekEnd'
			]);
			$model-> addRule('TimeCardID', 'string', ['max' => 100], 'required');
			$model-> addRule('TaskName', 'string', ['max' => 100], 'required');
			$model-> addRule('Date', 'string', ['max' => 32], 'required');
			$model-> addRule('StartTime', 'string', ['max' => 100], 'required');
			$model-> addRule('EndTime', 'string', ['max' => 100], 'required');
			$model-> addRule('ChargeOfAccountType', 'string', ['max' => 100], 'required');
			$model-> addRule('TimeReason', 'string', ['max' => 100], 'required');
			$model-> addRule('Comments', 'string', ['max' => 225], 'required');
			$model-> addRule('WeekStart', 'string', ['max' => 32], 'required');
			$model-> addRule('WeekEnd', 'string', ['max' => 32], 'required');
			
			//set default hidden form values
			$model->TimeCardID = $TimeCardID;
			$model->WeekStart = $weekStart;
			$model->WeekEnd = $weekEnd;
				
			if ($model->load(Yii::$app->request->post()) && $model->validate()) {
				// convert to 24 hour format
				$startTime24 = date("H:i", strtotime($model->StartTime));
				$endTime24 = date("H:i", strtotime($model->EndTime));

				$task_entry_data = array(
					'TimeCardID' => $model->TimeCardID,
					'TaskName' => 'Task ' . $model->TaskName,
					'Date' => $model->Date,
					'StartTime' => $startTime24, 
					'EndTime' => $endTime24,
					'CreatedByUserName' => Yii::$app->session['UserName'],
					'ChargeOfAccountType' => $model->ChargeOfAccountType,
					'TimeReason' => ($model->Comments == null) ? $model->TimeReason : $model->TimeReason . ' - ' . $model->Comments,
				);
				
				$testDate = date( "Y-m-d", strtotime(str_replace('-', '/',$model->Date)));
				
				// make sure date within range
				if (strtotime($testDate) < strtotime($model->WeekStart) || strtotime($testDate) > strtotime($model->WeekEnd)) {
					throw new \yii\web\HttpException(400);
				}

				$json_data = json_encode($task_entry_data);
				// post url
				$url = 'task%2Fcreate-task-entry';
				$response = Parent::executePostRequest($url, $json_data, Constants::API_VERSION_3);
				return $response;
			} else {
				$hoursOverview['hoursOverview'] = [];
				if($model->load(Yii::$app->request->queryParams) && $model->Date != null){	
					//format date for query
					$hoursOverviewDate = date("Y-m-d", strtotime($model->Date));
					//make route call with time card id and date params to get filtered overview data
					$getHoursOverviewUrl = 'task%2Fget-hours-overview&' . http_build_query([
						'timeCardID' => $model->TimeCardID,
						'date' => $hoursOverviewDate,
					]);
					$getHoursOverviewResponse = Parent::executeGetRequest($getHoursOverviewUrl, Constants::API_VERSION_3);
					$hoursOverview = json_decode($getHoursOverviewResponse, true);

					$timeCardProjectID = Yii::$app->request->get('timeCardProjectID');
					$inOvertime = Yii::$app->request->get('inOvertime');
				}
					
				$hoursOverviewDataProvider = new ArrayDataProvider
				([
					'allModels' => $hoursOverview['hoursOverview'],
					'key' => 'TimeEntryID',
					'pagination' => false
				]);
				
				$getAllTaskUrl = 'task%2Fget-by-project&' . http_build_query([
					'projectID' => $timeCardProjectID,
				]);
				$getAllTaskResponse = Parent::executeGetRequest($getAllTaskUrl, Constants::API_VERSION_3);
				$allTask = json_decode($getAllTaskResponse, true);
				$allTask = $allTask['assets'] != null ? $this->FormatTaskData($allTask['assets']): $allTask['assets'];

				//get chartOfAccountType for form dropdown
				$getAllChartOfAccountTypeUrl = 'task%2Fget-charge-of-account-type&' . http_build_query([
					'inOvertime' => $inOvertime,
				]);
				$getAllChartOfAccountTypeResponse = Parent::executeGetRequest($getAllChartOfAccountTypeUrl, Constants::API_VERSION_3);
				$chartOfAccountType = json_decode($getAllChartOfAccountTypeResponse, true);
				
				//get edit reasons
				$timeReasonData = Yii::$app->session['webDropDowns']['TimeReason'];
				//format reasons for dropdown
				$timeReasonDropdown = [];
				foreach($timeReasonData as $r){
					$timeReasonDropdown[$r['FieldValue']] = $r['FieldDisplay'];
				}
				
				$dataArray = [
					'model' => $model,
					'allTask' => $allTask,
					'chartOfAccountType' => $chartOfAccountType,
					'timeReasonDropdown' => $timeReasonDropdown,
					'SundayDate' => $SundayDate,
					'SaturdayDate' => $SaturdayDate,
					'hoursOverviewDataProvider' => $hoursOverviewDataProvider,
				];
				
				if (Yii::$app->request->isAjax) {
					return $this->renderAjax('create_task_entry', $dataArray);
				} else {
					return $this->render('create_task_entry', $dataArray);
				}
			}
		} catch (UnauthorizedHttpException $e){
            Yii::$app->response->redirect(['login/index']);
        } catch(ForbiddenHttpException $e) {
            throw $e;
        } catch(ErrorException $e) {
            throw new \yii\web\HttpException(400);
        } catch(Exception $e) {
            throw new ServerErrorHttpException();
        }
    }

	/**
     * Open modal to set reason for deactivate
     * Form submission will pass data to deactivate function
     * @param $action
     * @param null $data
     * @return string
     * @throws \yii\web\HttpException
     */
    public function actionDeactivateTimeReason($projectID = null)
    {
		try{
			//guest redirect
			if (Yii::$app->user->isGuest) {
				return $this->redirect(['/login']);
			}

			$model = new \yii\base\DynamicModel([
				'DeactivateAction',
				'DeactivateData',
				'DeactivateTimeReason',
				'DeactivateComments',
			]);
			$model->addRule('DeactivateAction', 'string', ['max' => 50], 'required');
			$model->addRule('DeactivateData', 'string', ['max' => 200]);
			$model->addRule('DeactivateTimeReason', 'string', ['max' => 100], 'required');
			$model->addRule('DeactivateComments', 'string', ['max' => 225]);
			if ($data = Yii::$app->request->post()){
				//set default hidden form values
				$model->DeactivateAction = $data['Action'];
				$model->DeactivateData = $data['Data'];
				
				//get edit reasons
				$timeReasonData = Yii::$app->session['webDropDowns']['TimeReason'];
				//format reasons for dropdown
				$timeReasonDropdown = [];
				foreach($timeReasonData as $r){
					$timeReasonDropdown[$r['FieldValue']] = $r['FieldDisplay'];
				}
				
				$dataArray = [
					'model' => $model,
					'projectID' => $projectID,
					'timeReasonDropdown' => $timeReasonDropdown,
				];
				
				if (Yii::$app->request->isAjax) {
					return $this->renderAjax('deactivate_time_reason', $dataArray);
				} else {
					return $this->render('deactivate_time_reason', $dataArray);
				}
			}else{
				yii::trace($model->errors);
				throw new \yii\web\HttpException(400);
			}
		} catch (UnauthorizedHttpException $e){
            Yii::$app->response->redirect(['login/index']);
        } catch(ForbiddenHttpException $e) {
            throw $e;
        } catch(ErrorException $e) {
            throw new \yii\web\HttpException(400);
        } catch(Exception $e) {
            throw new ServerErrorHttpException();
        }
    }

	/**
	* deactivate time entry by id
	* Pjax reload show entries table and modal table.
	*/
    public function actionDeactivate()
    {
        try {
			$data = Yii::$app->request->post();	
			$jsonData = json_encode($data);
			
			//put url
			$putUrl = 'time-entry%2Fdeactivate';
			$putResponse = Parent::executePutRequest($putUrl, $jsonData, Constants::API_VERSION_3); // indirect rbac
			$obj = json_decode($putResponse, true);	
        } catch (UnauthorizedHttpException $e){
            Yii::$app->response->redirect(['login/index']);
        } catch(ForbiddenHttpException $e) {
            throw $e;
        } catch(ErrorException $e) {
            throw new \yii\web\HttpException(400);
        } catch(Exception $e) {
            throw new ServerErrorHttpException();
        }
    }

    /**Format Task Array
     * @param $data
     * @return array
     */
    private function FormatTaskData($data){
        $namePairs = [];

        if ($data != null) {
            $codesSize = count($data);

            for ($i = 0; $i < $codesSize; $i++) {
                $namePairs[$data[$i]['TaskName']] = $data[$i]['TaskName'];
            }
        }
        return $namePairs;
    }
}
