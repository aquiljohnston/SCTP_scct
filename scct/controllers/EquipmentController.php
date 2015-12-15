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
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new equipment model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
		 //$request = Yii::$app->request;
		$model = new \yii\base\DynamicModel([
			'EquipmentName', 'EquipmentSerialNumber', 'EquipmentDetails', 'EquipmentType', 'EquipmentManufacturer', 'EquipmentManufactureYear',
			'EquipmentCondition', 'EquipmentMACID', 'EquipmentModel', 'EquipmentColor', 'EquipmentWarrantyDetail', 'EquipmentComment',
			'EquipmentClientID', 'EquipmentProjectID', 'EquipmentAnnualCalibrationDate', 'EquipmentAnnualCalibrationStatus', 'EquipmentAssignedUserID',
			'EquipmentCreatedByUser', 'EquipmentCreateDate', 'EquipmentModifiedBy', 'EquipmentModifiedDate', 'isNewRecord', 'EquipmentID'
		]);
		
		$model->addRule('EquipmentName', 'string')
			  ->addRule('EquipmentID', 'integer')
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
			  
		 //$model = new equipment();
		// Reading the response from the the api and filling the GridView
		$curl = new curl\Curl();

        //if ($model->load(Yii::$app->request->post()) && $model->save()) {
		if ($model->load(Yii::$app->request->post())){
			$response = $curl->setOption(
				CURLOPT_POSTFIELDS, 
				http_build_query(array(
					'EquipmentName' => $model->EquipmentName,
					// 'EquipmentSerialNumber' => $EquipmentSerialNumber,
					// 'EquipmentDetails' => $EquipmentDetails,
					// 'EquipmentType' => $EquipmentType,
					// 'EquipmentManufacturer' => $EquipmentManufacturer,
					// 'EquipmentManufactureYear' => $EquipmentManufactureYear,
					// 'EquipmentCondition' => $EquipmentCondition,
					// 'EquipmentMACID' => $EquipmentMACID,
					// 'EquipmentModel' => $EquipmentModel,
					// 'EquipmentColor' => $EquipmentColor,
					// 'EquipmentWarrantyDetail' => $EquipmentWarrantyDetail,
					// 'EquipmentComment' => $EquipmentComment,
					// 'EquipmentClientID' => $EquipmentClientID,
					// 'EquipmentProjectID' => $EquipmentProjectID,
					// 'EquipmentAnnualCalibrationDate' => $EquipmentAnnualCalibrationDate,
					// 'EquipmentAnnualCalibrationStatus' => $EquipmentAnnualCalibrationStatus,
					// 'EquipmentAssignedUserID' => $EquipmentAssignedUserID,
					// 'EquipmentCreatedByUser' => $EquipmentCreatedByUser,
					// 'EquipmentCreateDate' => $EquipmentCreateDate,
					// 'EquipmentModifiedBy' => $EquipmentModifiedBy,
					// 'EquipmentModifiedDate' => $EquipmentModifiedDate,
				)
			))
			->post('http://api.southerncrossinc.com/index.php?r=equipment%2Fcreate');
			var_dump($model->EquipmentID);
            return $this->redirect(['view', 'id' => $model->EquipmentID]);
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
