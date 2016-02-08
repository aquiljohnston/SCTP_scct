<?php

namespace app\controllers;

use Yii;
use app\models\MileageCard;
use app\models\MileageCardSearch;
use app\controllers\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\grid\GridView;
use yii\data\ArrayDataProvider;
use linslin\yii2\curl;
use yii\web\Request;
use yii\web\ForbiddenHttpException;


/**
 * MileageCardController implements the CRUD actions for MileageCard model.
 */
class MileageCardController extends BaseController
{

    /**
     * Lists all MileageCard models.
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
		if (Yii::$app->user->can('viewMileageCardIndex'))
		{
			$url = "http://api.southerncrossinc.com/index.php?r=mileage-card%2Fview-all-mileage-cards-current-week";
			$response = Parent::executeGetRequest($url);
			
			// passing decode data into dataProvider
			$dataProvider = new ArrayDataProvider
			([
				'allModels' => json_decode($response, true),
				'pagination' => [
					'pageSize' => 100,
				],
			]);
			// fill gridview by applying data provider
			GridView::widget([
				'dataProvider' => $dataProvider,
				]);
				
			//calling index page to pass dataProvider.
			return $this->render('index', [
				'dataProvider' => $dataProvider
			]);
		}
		else
		{
			throw new ForbiddenHttpException('You do not have adequate permissions to perform this action.');
		}
    }

    /**
     * Displays a single MileageCard model.
     * @param integer $id
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
		if (Yii::$app->user->can('viewMileageCard'))
		{
			$url = 'http://api.southerncrossinc.com/index.php?r=mileage-card%2Fview-mileage-entries&id='.$id;
			$mileage_entries_url = 'http://api.southerncrossinc.com/index.php?r=mileage-card%2Fview-mileage-entries&id='.$id;
			
			$response = Parent::executeGetRequest($url);
			$mileage_entries_response = Parent::executeGetRequest($mileage_entries_url);
			$dateProvider = json_decode($response, true);
			$Sundaydata = $dateProvider["MileageEntries"][0]["Sunday"];
			$SundayProvider = new ArrayDataProvider([
				'allModels' => $Sundaydata,
				'pagination' => [
					'pageSize' => 10,
				],
				// 'sort' => [
					// 'attributes' => ['id', 'name'],
				// ],
			]);
			
			$Mondaydata = $dateProvider["MileageEntries"][0]["Monday"];
			$MondayProvider = new ArrayDataProvider([
				'allModels' => $Mondaydata,
				'pagination' => [
					'pageSize' => 10,
				],
				// 'sort' => [
					// 'attributes' => ['id', 'name'],
				// ],
			]);
			
			$Tuesdaydata = $dateProvider["MileageEntries"][0]["Tuesday"];
			$TuesdayProvider = new ArrayDataProvider([
				'allModels' => $Tuesdaydata,
				'pagination' => [
					'pageSize' => 10,
				],
				// 'sort' => [
					// 'attributes' => ['id', 'name'],
				// ],
			]);
			
			$Wednesdaydata = $dateProvider["MileageEntries"][0]["Wednesday"];
			$WednesdayProvider = new ArrayDataProvider([
				'allModels' => $Wednesdaydata,
				'pagination' => [
					'pageSize' => 10,
				],
				// 'sort' => [
					// 'attributes' => ['id', 'name'],
				// ],
			]);
			
			$Thursdaydata = $dateProvider["MileageEntries"][0]["Thursday"];
			$ThursdayProvider = new ArrayDataProvider([
				'allModels' => $Thursdaydata,
				'pagination' => [
					'pageSize' => 10,
				],
				// 'sort' => [
					// 'attributes' => ['id', 'name'],
				// ],
			]);
			
			$Fridaydata = $dateProvider["MileageEntries"][0]["Friday"];
			$FridayProvider = new ArrayDataProvider([
				'allModels' => $Fridaydata,
				'pagination' => [
					'pageSize' => 10,
				],
				// 'sort' => [
					// 'attributes' => ['id', 'name'],
				// ],
			]);
			
			$Saturdaydata = $dateProvider["MileageEntries"][0]["Saturday"];
			$SaturdayProvider = new ArrayDataProvider([
				'allModels' => $Saturdaydata,
				'pagination' => [
					'pageSize' => 10,
				],
				// 'sort' => [
					// 'attributes' => ['id', 'name'],
				// ],
			]);
			return $this -> render('view', [
											'model' => json_decode($mileage_entries_response, true),
											'dateProvider' => $dateProvider,
												'SundayProvider' => $SundayProvider,
												'MondayProvider' => $MondayProvider,
												'TuesdayProvider' => $TuesdayProvider,
												'WednesdayProvider' => $WednesdayProvider,
												'ThursdayProvider' => $ThursdayProvider,
												'FridayProvider' => $FridayProvider,
												'SaturdayProvider' => $SaturdayProvider,
									]);
		}
		else
		{
			throw new ForbiddenHttpException('You do not have adequate permissions to perform this action.');
		}
    }

    /**
     * Creates a new MileageCard model.
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
		if (Yii::$app->user->can('createMileageCard'))
		{
			$model = new \yii\base\DynamicModel([
				'MileageCardEmpID', 'MileageCardTechID', 'MileageCardProjectID', 'MileageCardApprovedBy', 
				'MileageCardCreateDate', 'MileageCardCreatedBy', 'MileageCardModifiedDate', 'MileageCardModifiedBy', 
				'MileageCardBusinessMiles', 'MileageCardPersonalMiles', 'MileageCardApprovedFlag', 'isNewRecord'
			]);
			
			$model->addRule('MileageCardEmpID', 'integer')
				  ->addRule('MileageCardTechID', 'integer')
				  ->addRule('MileageCardProjectID', 'integer')
				  ->addRule('MileageCardBusinessMiles', 'integer')
				  ->addRule('MileageCardPersonalMiles', 'integer')
				  ->addRule('MileageCardApprovedFlag', 'integer')
				  ->addRule('MileageCardApprovedBy', 'string')
				  ->addRule('MileageCardCreatedBy', 'string')
				  ->addRule('MileageCardModifiedBy', 'string')
				  ->addRule('MileageCardCreateDate', 'safe')
				  ->addRule('MileageCardModifiedDate', 'safe');

			// create curl object
			$curl = new curl\Curl();
			
			// post url
			$url_send = "http://api.southerncrossinc.com/index.php?r=mileage-card%2Fcreate";
			
			if ($model->load(Yii::$app->request->post())) {
				
				$data = array(
					'MileageCardEmpID' => $model->MileageCardEmpID,
					'MileageCardTechID' => $model->MileageCardTechID,
					'MileageCardProjectID' => $model->MileageCardProjectID,
					'MileageCardBusinessMiles' => $model->MileageCardBusinessMiles,
					'MileageCardPersonalMiles' => $model->MileageCardPersonalMiles,
					'MileageCardApprovedFlag' => $model->MileageCardApprovedFlag,
					'MileageCardApprovedBy' => $model->MileageCardApprovedBy,
					'MileageCardCreatedBy' => $model->MileageCardCreatedBy,
					'MileageCardModifiedBy' => $model->MileageCardModifiedBy,
					'MileageCardCreateDate' => $model->MileageCardCreateDate,
					'MileageCardModifiedDate' => $model->MileageCardModifiedDate,
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
				//var_dump($result);
				$obj = (array)json_decode($result,true);
				//var_dump($obj["MileageCardID"]);
				return $this->redirect(['view', 'id' => $obj["MileageCardID"]]);
			} else {
				return $this->render('create', [
					'model' => $model,
				]);
			}
		}
		else
		{
			throw new ForbiddenHttpException('You do not have adequate permissions to perform this action.');
		}
    }

    /**
     * Updates an existing MileageCard model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
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
		if (Yii::$app->user->can('updateMileageCard'))
		{
			$getUrl = 'http://api.southerncrossinc.com/index.php?r=mileage-card%2Fview&id='.$id;
			$getResponse = json_decode(Parent::executeGetRequest($getUrl), true);

			$model = new \yii\base\DynamicModel($getResponse);
			
			$model->addRule('MileageCardEmpID', 'integer')
				  ->addRule('MileageCardTechID', 'integer')
				  ->addRule('MileageCardProjectID', 'integer')
				  ->addRule('MileageCardBusinessMiles', 'integer')
				  ->addRule('MileageCardPersonalMiles', 'integer')
				  ->addRule('MileageCardApprovedFlag', 'integer')
				  ->addRule('MileageCardApprovedBy', 'string')
				  ->addRule('MileageCardCreatedBy', 'string')
				  ->addRule('MileageCardModifiedBy', 'string')
				  ->addRule('MileageCardCreateDate', 'safe')
				  ->addRule('MileageCardModifiedDate', 'safe');
			
			if ($model->load(Yii::$app->request->post()))
			{
				$data = array(
					'MileageCardEmpID' => $model->MileageCardEmpID,
					'MileageCardTechID' => $model->MileageCardTechID,
					'MileageCardProjectID' => $model->MileageCardProjectID,
					'MileageCardBusinessMiles' => $model->MileageCardBusinessMiles,
					'MileageCardPersonalMiles' => $model->MileageCardPersonalMiles,
					'MileageCardApprovedFlag' => $model->MileageCardApprovedFlag,
					'MileageCardApprovedBy' => $model->MileageCardApprovedBy,
					'MileageCardCreatedBy' => $model->MileageCardCreatedBy,
					'MileageCardModifiedBy' => $model->MileageCardModifiedBy,
					'MileageCardCreateDate' => $model->MileageCardCreateDate,
					'MileageCardModifiedDate' => $model->MileageCardModifiedDate,
				);
			
				$json_data = json_encode($data);
				
				$putUrl = 'http://api.southerncrossinc.com/index.php?r=mileage-card%2Fupdate&id='.$id;
				$putResponse = Parent::executePutRequest($putUrl, $json_data);
				
				$obj = json_decode($putResponse, true);
				
			return $this->redirect(['view', 'id' => $obj["MileageCardID"]]);
			} else {
				return $this->render('update', [
					'model' => $model,
				]);
			}
		}
		else
		{
			throw new ForbiddenHttpException('You do not have adequate permissions to perform this action.');
		}
    }

    /**
     * Deletes an existing MileageCard model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
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
		if (Yii::$app->user->can('deleteMileageCard'))
		{
			$url = 'http://api.southerncrossinc.com/index.php?r=mileage-card%2Fdelete&id='.$id;
			Parent::executeDeleteRequest($url);
			$this->redirect('/index.php?r=mileage-card%2Findex');
		}
		else
		{
			throw new ForbiddenHttpException('You do not have adequate permissions to perform this action.');
		}
    }
}
