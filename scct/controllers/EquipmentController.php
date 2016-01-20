<?php

namespace app\controllers;

use Yii;
use app\models\equipment;
use app\models\EquipmentSearch;
use app\controllers\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use linslin\yii2\curl;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\web\ForbiddenHttpException;

/**
 * EquipmentController implements the CRUD actions for equipment model.
 */
class EquipmentController extends BaseController
{
	
    /**
     * Lists all equipment models.
     * @return mixed
     */
    public function actionIndex()
    {
		//guest redirect
		if (Yii::$app->user->isGuest)
		{
			return $this->redirect(['login/login']);
		}
		//RBAC permissions check
		if (Yii::$app->user->can('viewEquipmentIndex'))
		{
			// Reading the response from the the api and filling the GridView
			$url = 'http://api.southerncrossinc.com/index.php?r=equipment%2Fget-all';
			$response = Parent::executeGetRequest($url);
			
			//Passing data to the dataProvider and formating it in an associative array
			$dataProvider = new ArrayDataProvider
			([
				'allModels' => json_decode($response, true),
				'pagination' => [
					'pageSize' => 100,
				],
			]);
			GridView::widget
			([
				'dataProvider' => $dataProvider,
			]);
			
			return $this -> render('index', ['dataProvider' => $dataProvider]);
		}
		else
		{
			throw new ForbiddenHttpException('You do not have adequate permissions to perform this action.');
		}
    }

    /**
     * Displays a single equipment model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
		//guest redirect
		if (Yii::$app->user->isGuest)
		{
			return $this->redirect(['login/login']);
		}
		//RBAC permissions check
		if (Yii::$app->user->can('viewEquipment'))
		{
			$url = 'http://api.southerncrossinc.com/index.php?r=equipment%2Fview&id='.$id;
			$response = Parent::executeGetRequest($url);
			
			return $this -> render('view', ['model' => json_decode($response, true)]);
		}
		else
		{
			throw new ForbiddenHttpException('You do not have adequate permissions to perform this action.');
		}
    }

    /**
     * Creates a new equipment model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
		//guest redirect
		if (Yii::$app->user->isGuest)
		{
			return $this->redirect(['login/login']);
		}
		//RBAC permissions check
		if (Yii::$app->user->can('createEquipment'))
		{
			$model = new \yii\base\DynamicModel([
				'EquipmentName', 'EquipmentSerialNumber', 'EquipmentDetails', 'EquipmentType', 'EquipmentManufacturer', 'EquipmentManufactureYear',
				'EquipmentCondition', 'EquipmentMACID', 'EquipmentModel', 'EquipmentColor', 'EquipmentWarrantyDetail', 'EquipmentComment',
				'EquipmentClientID', 'EquipmentProjectID', 'EquipmentAnnualCalibrationDate', 'EquipmentAnnualCalibrationStatus', 'EquipmentAssignedUserID',
				'EquipmentCreatedByUser', 'EquipmentCreateDate', 'EquipmentModifiedBy', 'EquipmentModifiedDate', 'isNewRecord'
			]);
			
			$model->addRule('EquipmentName', 'string')			  
				  ->addRule('EquipmentSerialNumber', 'string')
				  ->addRule('EquipmentDetails', 'string')
				  ->addRule('EquipmentType', 'string')
				  ->addRule('EquipmentManufacturer', 'string')
				  ->addRule('EquipmentManufactureYear', 'string')
				  ->addRule('EquipmentCondition', 'string')
				  ->addRule('EquipmentMACID', 'string')
				  ->addRule('EquipmentModel', 'string')
				  ->addRule('EquipmentColor', 'string')
				  ->addRule('EquipmentWarrantyDetail', 'string')
				  ->addRule('EquipmentComment', 'string')
				  ->addRule('EquipmentClientID', 'integer')
				  ->addRule('EquipmentProjectID', 'integer')
				  ->addRule('EquipmentAnnualCalibrationDate', 'safe')
				  ->addRule('EquipmentAnnualCalibrationStatus', 'integer')
				  ->addRule('EquipmentAssignedUserID', 'string')
				  ->addRule('EquipmentCreatedByUser', 'string')
				  ->addRule('EquipmentCreateDate', 'safe')
				  ->addRule('EquipmentModifiedBy', 'string')
				  ->addRule('EquipmentModifiedDate', 'safe');
				  
			//get clients for form dropdown
			$clientUrl = "http://api.southerncrossinc.com/index.php?r=client%2Fget-client-dropdowns";
			$clientResponse = Parent::executeGetRequest($clientUrl);
			$clients = json_decode($clientResponse, true);	  
				  
			if ($model->load(Yii::$app->request->post())){
				
				$data =array(
					'EquipmentName' => $model->EquipmentName,
					'EquipmentSerialNumber' => $model->EquipmentSerialNumber,
					'EquipmentDetails' => $model->EquipmentDetails,
					'EquipmentType' => $model->EquipmentType,
					'EquipmentManufacturer' => $model->EquipmentManufacturer,
					'EquipmentManufactureYear' => $model->EquipmentManufactureYear,
					'EquipmentCondition' => $model->EquipmentCondition,
					'EquipmentMACID' => $model->EquipmentMACID,
					'EquipmentModel' => $model->EquipmentModel,
					'EquipmentColor' => $model->EquipmentColor,
					'EquipmentWarrantyDetail' => $model->EquipmentWarrantyDetail,
					'EquipmentComment' => $model->EquipmentComment,
					'EquipmentClientID' => $model->EquipmentClientID,
					'EquipmentProjectID' => $model->EquipmentProjectID,
					'EquipmentAnnualCalibrationDate' => $model->EquipmentAnnualCalibrationDate,
					'EquipmentAnnualCalibrationStatus' => $model->EquipmentAnnualCalibrationStatus,
					'EquipmentAssignedUserID' => $model->EquipmentAssignedUserID,
					'EquipmentCreatedByUser' => $model->EquipmentCreatedByUser,
					'EquipmentCreateDate' => $model->EquipmentCreateDate,
					'EquipmentModifiedBy' => $model->EquipmentModifiedBy,
					'EquipmentModifiedDate' => $model->EquipmentModifiedDate,
					);

				$json_data = json_encode($data);

				// post url
				$url= "http://api.southerncrossinc.com/index.php?r=equipment%2Fcreate";			
				$response = Parent::executePostRequest($url, $json_data);
				
				$obj = json_decode($response, true);

				return $this->redirect(['view', 'id' => $obj["EquipmentID"]]);
			}else {
				return $this->render('create',[
					'model' => $model,
					'clients' => $clients,
					]);
			}
		}
		else
		{
			throw new ForbiddenHttpException('You do not have adequate permissions to perform this action.');
		}
    }

    /**
     * Updates an existing equipment model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
		//guest redirect
		if (Yii::$app->user->isGuest)
		{
			return $this->redirect(['login/login']);
		}
		//RBAC permissions check
		if (Yii::$app->user->can('updateEquipment'))
		{
			$getUrl = 'http://api.southerncrossinc.com/index.php?r=equipment%2Fview&id='.$id;
			$getResponse = json_decode(Parent::executeGetRequest($getUrl), true);

			$model = new \yii\base\DynamicModel($getResponse);
			
			$model->addRule('EquipmentName', 'string')			  
				  ->addRule('EquipmentSerialNumber', 'string')
				  ->addRule('EquipmentDetails', 'string')
				  ->addRule('EquipmentType', 'string')
				  ->addRule('EquipmentManufacturer', 'string')
				  ->addRule('EquipmentManufactureYear', 'string')
				  ->addRule('EquipmentCondition', 'string')
				  ->addRule('EquipmentMACID', 'string')
				  ->addRule('EquipmentModel', 'string')
				  ->addRule('EquipmentColor', 'string')
				  ->addRule('EquipmentWarrantyDetail', 'string')
				  ->addRule('EquipmentComment', 'string')
				  ->addRule('EquipmentClientID', 'integer')
				  ->addRule('EquipmentProjectID', 'integer')
				  ->addRule('EquipmentAnnualCalibrationDate', 'safe')
				  ->addRule('EquipmentAnnualCalibrationStatus', 'integer')
				  ->addRule('EquipmentAssignedUserID', 'string')
				  ->addRule('EquipmentCreatedByUser', 'string')
				  ->addRule('EquipmentCreateDate', 'safe')
				  ->addRule('EquipmentModifiedBy', 'string')
				  ->addRule('EquipmentModifiedDate', 'safe');
				  
			//get clients for form dropdown
			$clientUrl = "http://api.southerncrossinc.com/index.php?r=client%2Fget-client-dropdowns";
			$clientResponse = Parent::executeGetRequest($clientUrl);
			$clients = json_decode($clientResponse, true);	  
				  
			if ($model->load(Yii::$app->request->post()))
			{
				$data =array(
					'EquipmentName' => $model->EquipmentName,
					'EquipmentSerialNumber' => $model->EquipmentSerialNumber,
					'EquipmentDetails' => $model->EquipmentDetails,
					'EquipmentType' => $model->EquipmentType,
					'EquipmentManufacturer' => $model->EquipmentManufacturer,
					'EquipmentManufactureYear' => $model->EquipmentManufactureYear,
					'EquipmentCondition' => $model->EquipmentCondition,
					'EquipmentMACID' => $model->EquipmentMACID,
					'EquipmentModel' => $model->EquipmentModel,
					'EquipmentColor' => $model->EquipmentColor,
					'EquipmentWarrantyDetail' => $model->EquipmentWarrantyDetail,
					'EquipmentComment' => $model->EquipmentComment,
					'EquipmentClientID' => $model->EquipmentClientID,
					'EquipmentProjectID' => $model->EquipmentProjectID,
					'EquipmentAnnualCalibrationDate' => $model->EquipmentAnnualCalibrationDate,
					'EquipmentAnnualCalibrationStatus' => $model->EquipmentAnnualCalibrationStatus,
					'EquipmentAssignedUserID' => $model->EquipmentAssignedUserID,
					'EquipmentCreatedByUser' => $model->EquipmentCreatedByUser,
					'EquipmentCreateDate' => $model->EquipmentCreateDate,
					'EquipmentModifiedBy' => $model->EquipmentModifiedBy,
					'EquipmentModifiedDate' => $model->EquipmentModifiedDate,
					);

				$json_data = json_encode($data);
				
				$putUrl = 'http://api.southerncrossinc.com/index.php?r=equipment%2Fupdate&id='.$id;
				$putResponse = Parent::executePutRequest($putUrl, $json_data);
				
				$obj = json_decode($putResponse, true);
				
				return $this->redirect(['view', 'id' => $model["EquipmentID"]]);
			} else {
				return $this->render('update', [
					'model' => $model,
					'clients' => $clients,
				]);
			} 
		}
		else
		{
			throw new ForbiddenHttpException('You do not have adequate permissions to perform this action.');
		}
    }

    /**
     * Deletes an existing equipment model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
		//guest redirect
		if (Yii::$app->user->isGuest)
		{
			return $this->redirect(['login/login']);
		}
		//RBAC permissions check
		if (Yii::$app->user->can('deleteEquipment'))
		{
			$url = 'http://api.southerncrossinc.com/index.php?r=equipment%2Fdelete&id='.$id;
			Parent::executeDeleteRequest($url);
			$this->redirect('/index.php?r=equipment%2Findex');
		}
		else
		{
			throw new ForbiddenHttpException('You do not have adequate permissions to perform this action.');
		}
    }
}
