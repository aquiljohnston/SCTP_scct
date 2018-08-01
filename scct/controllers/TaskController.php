<?php
namespace app\controllers;

/**
 * Created by PhpStorm.
 * User: tzhang
 * Date: 12/19/2017
 * Time: 9:29 AM
 */

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
     * Lists all tasks and users
     * @return mixed
     */
    public function actionIndex()
    {
		try{
			//guest redirect
			if (Yii::$app->user->isGuest) {
				return $this->redirect(['/login']);
			}

			//Check if user has permission to view task page
			self::requirePermission("getAllTask");

			$model = new \yii\base\DynamicModel([
				'taskfilter','userfilter', 'pagesize'
			]);
			$model->addRule('taskfilter', 'string', ['max' => 32])
				->addRule('userfilter', 'string', ['max' => 32])
				->addRule('pagesize', 'string', ['max' => 32]);//get page number and records per page

			// check if type was post, if so, get value from $model
			if ($model->load(Yii::$app->request->get())) {
				$listPerPageParam = 50;
				$taskFilterParam = $model->taskfilter;
				$userFilterParam = $model->userfilter;
			} else {
				$listPerPageParam = 50;
				$taskFilterParam = null;
				$userFilterParam = null;
			}

			$pageParam = 1;

			// Reading the response from the the api and filling the Task GridView
			$url = "task%2Fget-all-task&"
				. http_build_query(
					[
						'filter' => $taskFilterParam,
						'listPerPage' => $listPerPageParam,
						'page' => $pageParam
					]);
			$response = Parent::executeGetRequest($url, Constants::API_VERSION_2); // indirect rbac
			Yii::trace("TASK OUT: ".$response);
			$resultData = json_decode($response, true);
			//$taskPages = new Pagination($resultData['pages']);

			//Passing data to the dataProvider and format it in an associative array
			$taskDataProvider = new ArrayDataProvider([
				'allModels' => $resultData['assets'],
				'pagination' => [
					'pageSize' => 100,
				],
			]);

			// Sorting Task table
			$taskDataProvider->sort = [
				'attributes' => [
					'TaskName' => [
						'asc' => ['TaskName' => SORT_ASC],
						'desc' => ['TaskName' => SORT_DESC]
					],
				]
			];

			// Reading the response from the the api and filling the surveyorGridView
			$getUrl = 'dispatch%2Fget-surveyors&' . http_build_query([
					'filter' => $userFilterParam,
				]);
			$surveyorsResponse = json_decode(Parent::executeGetRequest($getUrl, Constants::API_VERSION_2), true); // indirect rbac
			$userDataProvider = new ArrayDataProvider
			([
				'allModels' => $surveyorsResponse['users'],
				'pagination' => false,
			]);

			$userDataProvider->key = 'UserID';

			// Sorting User Table
			$userDataProvider->sort = [
				'attributes' => [
					'UserName' => [
						'asc' => ['UserName' => SORT_ASC],
						'desc' => ['UserName' => SORT_DESC]
					],
				]
			];

			return $this->render('index', [
				'taskDataProvider' => $taskDataProvider,
				'userDataProvider' => $userDataProvider,
				'model' => $model,
				//'taskPages' => $taskPages
			]);
		} catch (UnauthorizedHttpException $e){
            Yii::$app->response->redirect(['login/index']);
        } catch(ForbiddenHttpException $e) {
            throw $e;
        } catch(ErrorException $e) {
            throw new \yii\web\HttpException(400);
        } catch(Exception $e) {
            throw new ServerErrorHttpException($e);
        }
    }
	
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

			// convert SundayDate and SaturdayDate to MM/dd/YYYY format
			$SundayDate = date( "m/d/Y", strtotime(str_replace('-', '/', $SundayDate)));
			$SaturdayDate = date( "m/d/Y", strtotime(str_replace('-', '/', $SaturdayDate)));

			self::requirePermission("createTaskEntry");

			$model = new \yii\base\DynamicModel([
				'TimeCardID',
				'TaskName',
				'Date',
				'StartTime',
				'EndTime',
				'ChargeOfAccountType'
			]);
			$model -> addRule('TimeCardID', 'string', ['max' => 100], 'required');
			$model -> addRule('TaskName', 'string', ['max' => 100], 'required');
			$model -> addRule('Date', 'string', ['max' => 32], 'required');
			$model -> addRule('StartTime', 'string', ['max' => 100], 'required');
			$model -> addRule('EndTime', 'string', ['max' => 100], 'required');
			$model -> addRule('ChargeOfAccountType', 'string', ['max' => 100], 'required');
				
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
				);

				$weekStart = Yii::$app->request->post('weekStart');
				$weekEnd = Yii::$app->request->post('weekEnd');
				$testDate = date( "Y-m-d", strtotime(str_replace('-', '/',$model->Date)));
				
				// make sure date within range
				if (strtotime($testDate) < strtotime($weekStart) || strtotime($testDate) > strtotime($weekEnd)) {
					throw new \yii\web\HttpException(400);
				}
				
				//probably dont need this check here because there is a validation check on the form
				// check difference between startTime and endTime 
				if ($endTime24 >= $startTime24) {
					$json_data = json_encode($task_entry_data);
					try {
						// post url
						$url = 'task%2Fcreate-task-entry';
						$response = Parent::executePostRequest($url, $json_data, Constants::API_VERSION_2);
						return $response;
					} catch (\Exception $e) {
						//return $this->redirect(['show-entries', 'id' => $model->TimeCardID]);
					}
				} else {
					//return $this->redirect(['show-entries', 'id' => $model->TimeCardID]);
				}
			} else {
				$hoursOverview['hoursOverview'] = [];
				if($model->load(Yii::$app->request->queryParams) && $model->Date != null){	
					//format date for query
					$hoursOverviewDate = date("Y-m-d", strtotime($model->Date));
					//make route call with time card id and date params to get filtered overview data
					$getHoursOverviewUrl = 'task%2Fget-hours-overview&timeCardID=' . $model->TimeCardID . '&date=' . $hoursOverviewDate;
					$getHoursOverviewResponse = Parent::executeGetRequest($getHoursOverviewUrl, Constants::API_VERSION_2);
					$hoursOverview = json_decode($getHoursOverviewResponse, true);

					$timeCardProjectID = Yii::$app->request->get('timeCardProjectID');
					$inOvertime = Yii::$app->request->get('inOvertime');
				}
					
				$hoursOverviewDataProvider = new ArrayDataProvider
				([
					'allModels' => $hoursOverview['hoursOverview'],
					'pagination' => false
				]);

				$hoursOverviewDataProvider->key = 'Task';
				
				$getAllTaskUrl = 'task%2Fget-by-project&projectID='.$timeCardProjectID;
				$getAllTaskResponse = Parent::executeGetRequest($getAllTaskUrl, Constants::API_VERSION_2);
				$allTask = json_decode($getAllTaskResponse, true);
				$allTask = $allTask['assets'] != null ? $this->FormatTaskData($allTask['assets']): $allTask['assets'];

				//get chartOfAccountType for form dropdown
				$getAllChartOfAccountTypeUrl = 'task%2Fget-charge-of-account-type&inOvertime=' . $inOvertime;
				$getAllChartOfAccountTypeResponse = Parent::executeGetRequest($getAllChartOfAccountTypeUrl, Constants::API_VERSION_2);
				$chartOfAccountType = json_decode($getAllChartOfAccountTypeResponse, true);
				
				if (Yii::$app->request->isAjax) {
					return $this->renderAjax('create_task_entry', [
						'model' => $model,
						'allTask' => $allTask,
						'chartOfAccountType' => $chartOfAccountType,
						'timeCardID' => $TimeCardID,
						'SundayDate' => $SundayDate,
						'SaturdayDate' => $SaturdayDate,
						'hoursOverviewDataProvider' => $hoursOverviewDataProvider,
					]);
				} else {
					return $this->render('create_task_entry', [
						'model' => $model,
						'allTask' => $allTask,
						'chartOfAccountType' => $chartOfAccountType,
						'timeCardID' => $TimeCardID,
						'SundayDate' => $SundayDate,
						'SaturdayDate' => $SaturdayDate,
						'hoursOverviewDataProvider' => $hoursOverviewDataProvider,
					]);
				}
			}
		} catch (UnauthorizedHttpException $e){
            Yii::$app->response->redirect(['login/index']);
        } catch(ForbiddenHttpException $e) {
            throw $e;
        } catch(ErrorException $e) {
            throw new \yii\web\HttpException(400);
        } catch(Exception $e) {
            throw new ServerErrorHttpException($e);
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
