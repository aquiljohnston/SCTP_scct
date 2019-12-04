<?php
namespace app\controllers;

use Yii;
use app\controllers\BaseController;
use app\models\MileageEntryTask;
use yii\data\Pagination;
use yii\data\ArrayDataProvider;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\ServerErrorHttpException;
use yii\web\UnauthorizedHttpException;
use linslin\yii2\curl;
use app\constants\Constants;

/**
 * TaskController implements the CRUD actions for task model.
 */
class MileageTaskController extends BaseController
{	
	/**
     * Add New Task Entry.
     * If creation is successful, the browser will be redirected to the 'show-entries' page.
     * @param $mileageCardID
     * @param $sundayDate
     * @param $saturdayDate
     * @param $mileageCardProjectID
     * @return render
     * @throws \yii\web\HttpException
     */

    public function actionAddMileageEntryTask()
    {
		try{
			//guest redirect
			if (Yii::$app->user->isGuest) {
				return $this->redirect(['/login']);
			}
			
			self::requirePermission("createTaskEntry");

			//create model object
			$model = new MileageEntryTask;
				
			if ($model->load(Yii::$app->request->post()) && $model->validate()) {
				$mileage_task_data = array(
					'MileageCardID' => $model->CardID,
					'Date' => $model->Date,
					'CreatedByUserName' => Yii::$app->session['UserName'],
					'TotalMiles' => $model->AdminMiles,
					'MileageType' => 'AdminDriveMeter',
					'MileageRate' => $model->MileageRate,
				);
				
				$json_data = json_encode($mileage_task_data);
				// post url
				$url = 'mileage-entry%2Fcreate-task';
				$response = Parent::executePostRequest($url, $json_data, Constants::API_VERSION_3);
				return $response;
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
	
	public function actionViewMileageEntryTaskByDay($mileageCardID, $date, $readOnly){
		try{
			//guest redirect
			if (Yii::$app->user->isGuest) {
				return $this->redirect(['/login']);
			}
			
			self::requirePermission("mileageEntryView");
			
			//create model form update form
			$model = new MileageEntryTask;
			
			$model->Date = $date;
			$model->CardID = $mileageCardID;
			
			//get entries for grid view
			$getUrl = 'mileage-entry%2Fview-entries&' . http_build_query([
				'cardID' => $mileageCardID,
				'date' => $date
			]);
			$getResponseData = json_decode(Parent::executeGetRequest($getUrl, Constants::API_VERSION_3), true); //indirect RBAC
			$entries = $getResponseData['entries'];
			$rates = $getResponseData['rates'];
			
			$mileageEntryDataProvider = new ArrayDataProvider([
				'allModels' => $entries,
				'key' => 'EntryID',
				'pagination' => false
			]);
			
			$dataArray = [
				'mileageEntryDataProvider' => $mileageEntryDataProvider,
				'model' => $model,
				'rates' => $rates,
				'readOnly' => $readOnly,
			];	
			
			if (Yii::$app->request->isAjax) {
				return $this->renderAjax('mileage_entry_view_modal', $dataArray);
			} else {
				return $this->render('mileage_entry_view_modal', $dataArray);
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
	
	public function actionUpdate(){
		try{
			//guest redirect
			if (Yii::$app->user->isGuest) {
				return $this->redirect(['/login']);
			}
			
			self::requirePermission('mileageEntryUpdate');
			
			//create model object
			$model = new MileageEntryTask;
			
			//load and validate data
			if ($model->load(Yii::$app->request->post()) && $model->validate()) {
				// convert time to 24 hour format
				$startTime24 = date('H:i', strtotime($model->StartTime));
				$endTime24 = date('H:i', strtotime($model->EndTime));
				//combine date with time
				$startDate = date('Y-m-d H:i:s', strtotime($model->Date . ' ' . $startTime24));
				$endDate = date('Y-m-d H:i:s', strtotime($model->Date . ' ' . $endTime24));
				
				//build body object
				$mileageEntryUpdateData = [
					'MileageEntryID' => $model->EntryID,
					'MileageEntryStartingMileage' => $model->StartingMileage,
					'MileageEntryEndingMileage' => $model->EndingMileage,
					'MileageEntryStartDate' => $startDate,
					'MileageEntryEndDate' => $endDate,
					'MileageEntryPersonalMiles' => $model->PersonalMiles,
					'MileageEntryTotalMiles' => $model->AdminMiles,
					'MileageRate' => $model->MileageRate,
				];
				
				$jsonData = json_encode($mileageEntryUpdateData);
			
				//post url
				$putUrl = 'mileage-entry%2Fupdate';
				$putResponse = Parent::executePutRequest($putUrl, $jsonData,Constants::API_VERSION_3); // indirect rbac
				$obj = json_decode($putResponse, true);	
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
	* deactivate mileage entry by id
	* Pjax reload show entries table and modal table.
	*/
    public function actionDeactivate($entryID)
    {
        try {
			//put url
			$putUrl = 'mileage-entry%2Fdeactivate&' . http_build_query([
				'entryID' => $entryID,
			]);
			$putResponse = Parent::executePutRequest($putUrl, '',Constants::API_VERSION_3); // indirect rbac
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
}
