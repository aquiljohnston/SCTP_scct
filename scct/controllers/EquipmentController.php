<?php

namespace app\controllers;

use Yii;
use app\models\equipment;
use app\models\EquipmentSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use linslin\yii2\curl;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\web\Request;

/**
 * EquipmentController implements the CRUD actions for equipment model.
 */
class EquipmentController extends Controller
{
	private $model;
	
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all equipment models.
     * @return mixed
     */
    public function actionIndex()
    {
		
		// Reading the response from the the api and filling the GridView
		$curl = new curl\Curl();
		
        $response = $curl->get('http://api.southerncrossinc.com/index.php?r=equipment%2Findex');
		//Passing data to the dataProvider and formating it in an associative array
		$dataProvider = new ArrayDataProvider([
        'allModels' => json_decode($response,true),
		]);
		
				GridView::widget([
			'dataProvider' => $dataProvider,
		]);
		
		return $this -> render('index', ['dataProvider' => $dataProvider]);
    }

    /**
     * Displays a single equipment model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
		$curl = new curl\Curl();
        
        //get http://example.com/
        $response = $curl->get('http://api.southerncrossinc.com/index.php?r=equipment%2Fview&id='.$id);
		
		return $this -> render('view', ['model' => json_decode($response, true)]);
        /*return $this->render('view', [
            'model' => $this->find()->where(['EquipmentSerialNumber' => $EquipmentSerialNumber])->one(),//$this->findModel($id),
        ]);*/
    }

    /**
     * Creates a new equipment model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
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
			  
		// create curl object
		$curl = new curl\Curl();
		
		// put data into array
		/*$data =array(
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
				'EquipmentModifiedDate' => $model->EquipmentModifiedDate
		);*/
		
		// post url
		$url_send = "http://api.southerncrossinc.com/index.php?r=equipment%2Fcreate";

		if ($model->load(Yii::$app->request->post())){
			$request = Yii::$app->request;
			
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
			$ch = curl_init($url_send);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"POST");
			curl_setopt($ch, CURLOPT_POSTFIELDS,$json_data);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Content-Type: application/json',
				'Content-Length: ' . strlen($json_data))
			);			
			$result = curl_exec($ch);
			curl_close($ch);
			$obj = (array)json_decode($result);

            return $this->redirect(['view', 'id' => $obj["EquipmentID"]]);
        }else {
            return $this->render('create',[
				'model' => $model,
				]);
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
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->EquipmentID]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
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
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the equipment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return equipment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = equipment::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
