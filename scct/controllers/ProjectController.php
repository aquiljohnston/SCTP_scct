<?php

namespace app\controllers;

use Yii;
use app\models\project;
use app\models\ProjectSearch;
use app\controllers\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\ForbiddenHttpException;
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

		// Reading the response from the the api and filling the GridView
		$url = "http://api.southerncrossinc.com/index.php?r=project%2Fget-all";
		$response = Parent::executeGetRequest($url); // indirect rbac


		$filteredResultData = $this->filterColumnMultiple(json_decode($response, true), 'ProjectName', 'filtername');
		$filteredResultData = $this->filterColumnMultiple($filteredResultData, 'ProjectType', 'filtertype');

		$searchModel = [
			'ProjectName' => Yii::$app->request->getQueryParam('filtername', ''),
			'ProjectType' => Yii::$app->request->getQueryParam('filtertype', '')
		];
		//Passing data to the dataProvider and formating it in an associative array
		$dataProvider = new ArrayDataProvider([
			'allModels' => $filteredResultData,
			'pagination' => [
				'pageSize' => 100,
			],
		]);

		return $this -> render('index', ['dataProvider' => $dataProvider, 'searchModel' => $searchModel]);

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
		$url = 'http://api.southerncrossinc.com/index.php?r=project%2Fview&id='.$id;
		$response = Parent::executeGetRequest($url); // indirect rbac

		return $this -> render('view', ['model' => json_decode($response), true]);
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
		self::requirePermission("projectCreate");
	
		$model  = new Project();
			  
		//get clients for form dropdown
		$clientUrl = "http://api.southerncrossinc.com/index.php?r=client%2Fget-client-dropdowns";
		$clientResponse = Parent::executeGetRequest($clientUrl);
		$clients = json_decode($clientResponse, true);
		
		//get states for form dropdown
		$stateUrl = "http://api.southerncrossinc.com/index.php?r=state-code%2Fget-code-dropdowns";
		$stateResponse = Parent::executeGetRequest($stateUrl);
		$states = json_decode($stateResponse, true);
		
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
				'ProjectState' => $model->ProjectState,
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
				'states' => $states,
				]);
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
		self::requirePermission("projectUpdate");
		$getUrl = 'http://api.southerncrossinc.com/index.php?r=project%2Fview&id='.$id;
		$getResponse = json_decode(Parent::executeGetRequest($getUrl), true);

		$model  = new Project();
		$model->attributes = $getResponse;
			  
		//get clients for form dropdown
		$clientUrl = "http://api.southerncrossinc.com/index.php?r=client%2Fget-client-dropdowns";
		$clientResponse = Parent::executeGetRequest($clientUrl);
		$clients = json_decode($clientResponse, true);
		
		//get states for form dropdown
		$stateUrl = "http://api.southerncrossinc.com/index.php?r=state-code%2Fget-code-dropdowns";
		$stateResponse = Parent::executeGetRequest($stateUrl);
		$states = json_decode($stateResponse, true);
		
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
				'ProjectState' => $model->ProjectState,
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
				'states' => $states,
			]);
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
		$url = 'http://api.southerncrossinc.com/index.php?r=project%2Fdelete&id='.$id;
		Parent::executeDeleteRequest($url); // indirect rbac
		$this->redirect('/index.php?r=project%2Findex');
    }

	/**
	 * Get all projects associate with the specific user based on the userID
	 * It will return all projects in json format
	 * @return mixed
	 * @throws ForbiddenHttpException
	 * @internal param string $userID
	 */
    public function actionGetAllProjects()
    {

		if (Yii::$app->request->isAjax) {
			$data = Yii::$app->request->post();
			
			$projectDropdownUrl = "http://api.southerncrossinc.com/index.php?r=project%2Fget-all";
			//get projects by calling API route
			$projectDropdownResponse = Parent::executeGetRequest($projectDropdownUrl); // indirect rbac
			//set up response data type
			Yii::$app->response->format = 'json';

			return ['projects' => $projectDropdownResponse];
		}
	}
}
