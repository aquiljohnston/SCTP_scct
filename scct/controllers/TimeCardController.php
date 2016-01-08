<?php

namespace app\controllers;

use Yii;
use app\models\TimeCard;
use app\models\TimeCardSearch;
use app\controllers\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\grid\GridView;
use yii\data\ArrayDataProvider;
use linslin\yii2\curl;
use yii\web\Request;
use yii\web\ForbiddenHttpException;

/**
 * TimeCardController implements the CRUD actions for TimeCard model.
 */
class TimeCardController extends BaseController
{
    /**
     * Lists all TimeCard models.
     * @return mixed
     */
    public function actionIndex()
    {
		//RBAC permissions check
		if (Yii::$app->user->can('viewTimeCardIndex'))
		{
			// create curl for restful call.		
			// get response from api 		
			$url = "http://api.southerncrossinc.com/index.php?r=time-card%2Findex";
			$response = Parent::executeGetRequest($url);
			
			// passing decode data into dataProvider
			$dataProvider = new ArrayDataProvider([
			'allModels' => json_decode($response, true),]);

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
     * Displays a single TimeCard model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {		
	//RBAC permissions check
		if (Yii::$app->user->can('viewTimeCard'))
		{
			$url = 'http://api.southerncrossinc.com/index.php?r=time-card%2Fview&id='.$id;
			$response = Parent::executeGetRequest($url);
			
			return $this -> render('view', ['model' => json_decode($response, true)]);
		}
		else
		{
			throw new ForbiddenHttpException('You do not have adequate permissions to perform this action.');
		}
    }

    /**
     * Creates a new TimeCard model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
		//RBAC permissions check
		if (Yii::$app->user->can('createTimeCard'))
		{
			$model = new \yii\base\DynamicModel([
				'TimeCardStartDate', 'TimeCardEndDate', 'TimeCardCreateDate', 'TimeCardModifiedDate', 'TimeCardHoursWorked', 'TimeCardProjectID', 
				'TimeCardTechID', 'TimeCardApproved', 'TimeCardSupervisorName', 'TimeCardComment', 'TimeCardCreatedBy', 'TimeCardModifiedBy', 'isNewRecord'
			]);
			
			$model->addRule('TimeCardStartDate', 'safe')
				  ->addRule('TimeCardEndDate', 'safe')
				  ->addRule('TimeCardCreateDate', 'safe')
				  ->addRule('TimeCardModifiedDate', 'safe')
				  ->addRule('TimeCardHoursWorked', 'number')
				  ->addRule('TimeCardProjectID', 'integer')
				  ->addRule('TimeCardTechID', 'integer')
				  ->addRule('TimeCardApproved', 'integer')
				  ->addRule('TimeCardSupervisorName', 'string')
				  ->addRule('TimeCardComment', 'string')
				  ->addRule('TimeCardCreatedBy', 'string')
				  ->addRule('TimeCardModifiedBy', 'string');
			
			// create curl object
			$curl = new curl\Curl();
			
			// post url
			$url_send = "http://api.southerncrossinc.com/index.php?r=time-card%2Fcreate";
			
			if ($model->load(Yii::$app->request->post())) {
				
				$data = array(
					'TimeCardStartDate' => $model->TimeCardStartDate,
					'TimeCardEndDate' => $model->TimeCardEndDate,
					'TimeCardHoursWorked' => $model->TimeCardHoursWorked,
					'TimeCardProjectID' => $model->TimeCardProjectID,
					'TimeCardTechID' => $model->TimeCardTechID,
					'TimeCardApproved' => $model->TimeCardApproved,
					'TimeCardSupervisorName' => $model->TimeCardSupervisorName,
					'TimeCardComment' => $model->TimeCardComment,
					'TimeCardCreateDate' => $model->TimeCardCreateDate,
					'TimeCardCreatedBy' => $model->TimeCardCreatedBy,
					'TimeCardModifiedDate' => $model->TimeCardModifiedDate,
					'TimeCardModifiedBy' => $model->TimeCardModifiedBy,
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
				$obj = (array)json_decode($result);
				//var_dump($obj["TimeCardID"]);
				return $this->redirect(['view', 'id' => $obj["TimeCardID"]]);
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
     * Updates an existing TimeCard model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
		//RBAC permissions check
		if (Yii::$app->user->can('updateTimeCard'))
		{
			$getUrl = 'http://api.southerncrossinc.com/index.php?r=time-card%2Fview&id='.$id;
			$getResponse = json_decode(Parent::executeGetRequest($getUrl), true);
			
			$model = new \yii\base\DynamicModel($getResponse);
			
			$model->addRule('TimeCardStartDate', 'safe')
				  ->addRule('TimeCardEndDate', 'safe')
				  ->addRule('TimeCardCreateDate', 'safe')
				  ->addRule('TimeCardModifiedDate', 'safe')
				  ->addRule('TimeCardHoursWorked', 'number')
				  ->addRule('TimeCardProjectID', 'integer')
				  ->addRule('TimeCardTechID', 'integer')
				  ->addRule('TimeCardApproved', 'integer')
				  ->addRule('TimeCardSupervisorName', 'string')
				  ->addRule('TimeCardComment', 'string')
				  ->addRule('TimeCardCreatedBy', 'string')
				  ->addRule('TimeCardModifiedBy', 'string');
			
			if ($model->load(Yii::$app->request->post()))
			{
				$data = array(
					'TimeCardStartDate' => $model->TimeCardStartDate,
					'TimeCardEndDate' => $model->TimeCardEndDate,
					'TimeCardHoursWorked' => $model->TimeCardHoursWorked,
					'TimeCardProjectID' => $model->TimeCardProjectID,
					'TimeCardTechID' => $model->TimeCardTechID,
					'TimeCardApproved' => $model->TimeCardApproved,
					'TimeCardSupervisorName' => $model->TimeCardSupervisorName,
					'TimeCardComment' => $model->TimeCardComment,
					'TimeCardCreateDate' => $model->TimeCardCreateDate,
					'TimeCardCreatedBy' => $model->TimeCardCreatedBy,
					'TimeCardModifiedDate' => $model->TimeCardModifiedDate,
					'TimeCardModifiedBy' => $model->TimeCardModifiedBy,
				);
				
				$json_data = json_encode($data);
				
				$putUrl = 'http://api.southerncrossinc.com/index.php?r=time-card%2Fupdate&id='.$id;
				$putResponse = Parent::executePutRequest($putUrl, $json_data);
				
				$obj = json_decode($putResponse, true);
				
				return $this->redirect(['view', 'id' => $obj["TimeCardID"]]);
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
     * Deletes an existing TimeCard model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
		//RBAC permissions check
		if (Yii::$app->user->can('deleteTimeCard'))
		{
			$url = 'http://api.southerncrossinc.com/index.php?r=time-card%2Fdelete&id='.$id;
			Parent::executeDeleteRequest($url);
			$this->redirect('/index.php?r=time-card%2Findex');
		}
		else
		{
			throw new ForbiddenHttpException('You do not have adequate permissions to perform this action.');
		}
    }
}
