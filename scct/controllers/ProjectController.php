<?php

namespace app\controllers;

use Yii;
use app\models\project;
use app\models\ProjectSearch;
use app\controllers\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\ForbiddenHttpException;
use yii\grid\GridView;
use yii\data\ArrayDataProvider;
use linslin\yii2\curl;
/**
 * ProjectController implements the CRUD actions for project model.
 */
class ProjectController extends BaseController
{

    /**
     * Lists all project models.
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
		if (Yii::$app->user->can('viewProjectIndex'))
		{
			// Reading the response from the the api and filling the GridView
			$url = "http://api.southerncrossinc.com/index.php?r=project%2Fget-all";
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
     * Displays a single project model.
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
		if (Yii::$app->user->can('viewProject'))
		{
			$url = 'http://api.southerncrossinc.com/index.php?r=project%2Fview&id='.$id;
			$response = Parent::executeGetRequest($url);

			return $this -> render('view', ['model' => json_decode($response), true]);
		}
		else
		{
			throw new ForbiddenHttpException('You do not have adequate permissions to perform this action.');
		}
    }

    /**
     * Creates a new project model.
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
		if (Yii::$app->user->can('createProject'))
		{
			$model = new \yii\base\DynamicModel([
				'ProjectName', 'ProjectDescription', 'ProjectNotes', 'ProjectType', 'ProjectStatus', 'ProjectClientID',
				'ProjectStartDate', 'ProjectEndDate', 'ProjectCreateDate', 'ProjectCreatedBy', 'ProjectModifiedDate',
				'ProjectModifiedBy','isNewRecord'
			]);
			
			$model->addRule('ProjectName', 'string')			  
				  ->addRule('ProjectDescription', 'string')
				  ->addRule('ProjectNotes', 'string')
				  ->addRule('ProjectType', 'string')
				  ->addRule('ProjectStatus', 'integer')
				  ->addRule('ProjectClientID', 'integer')
				  ->addRule('ProjectStartDate', 'safe')
				  ->addRule('ProjectEndDate', 'safe')
				  ->addRule('ProjectCreateDate', 'safe')
				  ->addRule('ProjectCreatedBy', 'string')
				  ->addRule('ProjectModifiedDate', 'safe')
				  ->addRule('ProjectModifiedBy', 'string');
				  
			//get clients for form dropdown
			$clientUrl = "http://api.southerncrossinc.com/index.php?r=client%2Fget-client-dropdowns";
			$clientResponse = Parent::executeGetRequest($clientUrl);
			$clients = json_decode($clientResponse, true);
			
			//generate array for Active Flag dropdown
			$flag = 
			[
				1 => "Active",
				0 => "Inactive",
			];
			
			if ($model->load(Yii::$app->request->post())){
				
				$data =array(
					'ProjectName' => $model->ProjectName,
					'ProjectDescription' => $model->ProjectDescription,
					'ProjectNotes' => $model->ProjectNotes,
					'ProjectType' => $model->ProjectType,
					'ProjectStatus' => $model->ProjectStatus,
					'ProjectClientID' => $model->ProjectClientID,
					'ProjectStartDate' => $model->ProjectStartDate,
					'ProjectEndDate' => $model->ProjectEndDate,
					'ProjectCreateDate' => $model->ProjectCreateDate,
					'ProjectCreatedBy' => Yii::$app->session['userID'],
					'ProjectModifiedDate' => $model->ProjectModifiedDate,
					'ProjectModifiedBy' => $model->ProjectModifiedBy,
					);

				$json_data = json_encode($data);

				// post url
				$url= "http://api.southerncrossinc.com/index.php?r=project%2Fcreate";			
				$response = Parent::executePostRequest($url, $json_data);
				
				$obj = json_decode($response, true);

				return $this->redirect(['view', 'id' => $obj["ProjectID"]]);
			}else {
				return $this->render('create',[
					'model' => $model,
					'clients' => $clients,
					'flag' => $flag,
					]);
			}
		}
		else
		{
			throw new ForbiddenHttpException('You do not have adequate permissions to perform this action.');
		}
    }

    /**
     * Updates an existing project model.
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
		if (Yii::$app->user->can('updateProject'))
		{
			$getUrl = 'http://api.southerncrossinc.com/index.php?r=project%2Fview&id='.$id;
			$getResponse = json_decode(Parent::executeGetRequest($getUrl), true);

			$model = new \yii\base\DynamicModel($getResponse);
			
			$model->addRule('ProjectName', 'string')			  
				  ->addRule('ProjectDescription', 'string')
				  ->addRule('ProjectNotes', 'string')
				  ->addRule('ProjectType', 'string')
				  ->addRule('ProjectStatus', 'integer')
				  ->addRule('ProjectClientID', 'integer')
				  ->addRule('ProjectStartDate', 'safe')
				  ->addRule('ProjectEndDate', 'safe')
				  ->addRule('ProjectCreateDate', 'safe')
				  ->addRule('ProjectCreatedBy', 'string')
				  ->addRule('ProjectModifiedDate', 'safe')
				  ->addRule('ProjectModifiedBy', 'string');
				  
			//get clients for form dropdown
			$clientUrl = "http://api.southerncrossinc.com/index.php?r=client%2Fget-client-dropdowns";
			$clientResponse = Parent::executeGetRequest($clientUrl);
			$clients = json_decode($clientResponse, true);
			
			//generate array for Active Flag dropdown
			$flag = 
			[
				1 => "Active",
				0 => "Inactive",
			];
				  
			if ($model->load(Yii::$app->request->post()))
			{
				$data =array(
					'ProjectName' => $model->ProjectName,
					'ProjectDescription' => $model->ProjectDescription,
					'ProjectNotes' => $model->ProjectNotes,
					'ProjectType' => $model->ProjectType,
					'ProjectStatus' => $model->ProjectStatus,
					'ProjectClientID' => $model->ProjectClientID,
					'ProjectStartDate' => $model->ProjectStartDate,
					'ProjectEndDate' => $model->ProjectEndDate,
					'ProjectCreateDate' => $model->ProjectCreateDate,
					'ProjectCreatedBy' => $model->ProjectCreatedBy,
					'ProjectModifiedDate' => $model->ProjectModifiedDate,
					'ProjectModifiedBy' => Yii::$app->session['userID'],
					);

				$json_data = json_encode($data);
				
				$putUrl = 'http://api.southerncrossinc.com/index.php?r=project%2Fupdate&id='.$id;
				$putResponse = Parent::executePutRequest($putUrl, $json_data);
				
				$obj = json_decode($putResponse, true);
				
				return $this->redirect(['view', 'id' => $model["ProjectID"]]);
			} else {
				return $this->render('update', [
					'model' => $model,
					'clients' => $clients,
					'flag' => $flag,
				]);
			} 
		}
		else
		{
			throw new ForbiddenHttpException('You do not have adequate permissions to perform this action.');
		}
    }

    /**
     * Deletes an existing project model.
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
		if (Yii::$app->user->can('deleteProject'))
		{
			$url = 'http://api.southerncrossinc.com/index.php?r=project%2Fdelete&id='.$id;
			Parent::executeDeleteRequest($url);
			$this->redirect('/index.php?r=project%2Findex');
		}
		else
		{
			throw new ForbiddenHttpException('You do not have adequate permissions to perform this action.');
		}
    }
}
