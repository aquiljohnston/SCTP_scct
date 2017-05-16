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
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['login/login']);
        }

        $model = new \yii\base\DynamicModel([
            'filter', 'pagesize'
        ]);
        $model->addRule('filter', 'string', ['max' => 32])
            ->addRule('pagesize', 'string', ['max' => 32]);//get page number and records per page

        // check if type was post, if so, get value from $model
        if ($model->load(Yii::$app->request->get())) {

            $listPerPageParam = $model->pagesize;
            $filterParam = $model->filter;
        } else {
            $listPerPageParam = 10;
            $filterParam = "";
        }


        $pageParam = Yii::$app->request->getQueryParam('page', '');
        $projectNameFilterParam = Yii::$app->request->getQueryParam('filtername', '');
        $stateFilterParam = Yii::$app->request->getQueryParam('filterstate', '');
        $typeFilterParam = Yii::$app->request->getQueryParam('filtertype', '');

		// Reading the response from the the api and filling the GridView
		$url = "project%2Fget-all&filter=" . urlencode($filterParam) . "&listPerPage=" . urlencode($listPerPageParam)
                . "&page=" . urlencode($pageParam) . "&filterprojectname=" . urlencode($projectNameFilterParam)
                . "&filtertype=" . urlencode($typeFilterParam) . "&filterstate=" . urlencode($stateFilterParam);
		$response = Parent::executeGetRequest($url, BaseController::API_VERSION_2); // indirect rbac


		$resultData = json_decode($response, true);

		$searchModel = [
			'ProjectName' => $projectNameFilterParam,
			'ProjectType' => $typeFilterParam,
			'ProjectState' => $stateFilterParam
		];
		//Passing data to the dataProvider and formating it in an associative array
		$dataProvider = new ArrayDataProvider([
			'allModels' => $resultData,
			'pagination' => [
				'pageSize' => 100,
			],
		]);

		return $this -> render('index', [
			'dataProvider' => $dataProvider,
			'searchModel' => $searchModel,
			'canCreateProjects' => self::can("projectCreate"),
            'model' => $model
		]);

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
		$url = "project%2Fview&joinNames=true&id=$id";
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
		$clientUrl = "client%2Fget-client-dropdowns";
		$clientResponse = Parent::executeGetRequest($clientUrl);
		$clients = json_decode($clientResponse, true);
		
		//get states for form dropdown
		$stateUrl = "state-code%2Fget-code-dropdowns";
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
				'ProjectUrlPrefix' => $model->ProjectUrlPrefix,
				'ProjectClientID' => $model->ProjectClientID,
				'ProjectState' => $model->ProjectState,
				'ProjectStartDate' => $model->ProjectStartDate,
				'ProjectEndDate' => $model->ProjectEndDate,
				'ProjectCreateDate' => $model->ProjectCreateDate,
				'ProjectModifiedDate' => $model->ProjectModifiedDate,
				'ProjectModifiedBy' => $model->ProjectModifiedBy,
				);

			$json_data = json_encode($data);
			try{
				// post url
				$url= "project%2Fcreate";
				$response = Parent::executePostRequest($url, $json_data);
				
				$obj = json_decode($response, true);

				return $this->redirect(['view', 'id' => $obj["ProjectID"]]);
			} catch (\Exception $e) {
				return $this->render('create', [
					'model' => $model,
					'clients' => $clients,
					'flag' => $flag,
					'states' => $states,
				]);
			}
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
		$getUrl = 'project%2Fview&id='.$id;
		$getResponse = json_decode(Parent::executeGetRequest($getUrl), true);

		$model  = new Project();
		$model->attributes = $getResponse;
			  
		//get clients for form dropdown
		$clientUrl = "client%2Fget-client-dropdowns";
		$clientResponse = Parent::executeGetRequest($clientUrl);
		$clients = json_decode($clientResponse, true);
		
		//get states for form dropdown
		$stateUrl = "state-code%2Fget-code-dropdowns";
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
				'ProjectUrlPrefix' => $model->ProjectUrlPrefix,
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
			try {
				$putUrl = 'project%2Fupdate&id='.$id;
				$putResponse = Parent::executePutRequest($putUrl, $json_data);
				
				$obj = json_decode($putResponse, true);
                if(isset($obj["status"]) && $obj["status"] == 400) {
                    return $this->render('update', [
                        'model' => $model,
                        'clients' => $clients,
                        'flag' => $flag,
                        'states' => $states,
                        'updateFailed' => true
                    ]);
                } else {
				    return $this->redirect(['view', 'id' => $model["ProjectID"]]);
                }
			} catch (\Exception $e) {
				return $this->render('update', [
					'model' => $model,
					'clients' => $clients,
					'flag' => $flag,
					'states' => $states,
                    'updateFailed' => true
				]);
			}
		} else {
			return $this->render('update', [
				'model' => $model,
				'clients' => $clients,
				'flag' => $flag,
				'states' => $states,
                'updateFailed' => false
			]);
		}
    }

    /**
     * Deactivates an existing project model.
     * If deactivation is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */

    public function actionDeactivate($id)
    {
        //guest redirect
        if (Yii::$app->user->isGuest)
        {
            return $this->redirect(['login/login']);
        }
        $url = 'project%2Fdeactivate&id='.$id;
        Parent::executePostRequest($url, ""); //indirect RBAC
        $this->redirect(['project/index']);
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
			
			$projectDropdownUrl = "project%2Fget-all";
			//get projects by calling API route
			$projectDropdownResponse = Parent::executeGetRequest($projectDropdownUrl); // indirect rbac
			//set up response data type
			Yii::$app->response->format = 'json';

			return ['projects' => $projectDropdownResponse];
		}
	}
	
	public function actionAddUser($id)
	{
		//guest redirect
		if (Yii::$app->user->isGuest)
		{
			return $this->redirect(['login/login']);
		}

		self::requirePermission("projectAddRemoveUsers");
		
		$url = 'project%2Fget-user-relationships&projectID='.$id;
		$projectUrl = 'project%2Fview&id='.$id;

		//indirect rbac
		$response = Parent::executeGetRequest($url);
		$projectResponse = Parent::executeGetRequest($projectUrl);


		$users = json_decode($response,true);
		$project = json_decode($projectResponse);

		//load get data into variables
		$unassignedData = $users["unassignedUsers"];
		$assignedData = $users["assignedUsers"];

		//create model for active form
		$model = new \yii\base\DynamicModel([
			'UnassignedUsers', 'AssignedUsers' ]);
		$model->addRule('UnassignedUsers', 'string')
				 ->addRule('AssignedUsers',  'string');



		if ($model->load(Yii::$app->request->post()))
		{
			//prepare arrays for post request
			//explode strings from active form into arrays
			$unassignedUsersArray = explode(',',$model->UnassignedUsers);
			$assignedUsersArray = explode(',',$model->AssignedUsers);
			//array diff new arrays with arrays previous to submission to get changes
			$usersAdded = array_diff($assignedUsersArray,array_keys($assignedData));
			$usersRemoved = array_diff($unassignedUsersArray,array_keys($unassignedData));
			//load arrays of changes into post data
			$data = [];
			$data["usersRemoved"] = $usersRemoved;
			$data["usersAdded"] = $usersAdded;

			//encode data
			$jsonData = json_encode($data);

			//set post url
			$postUrl = 'project%2Fadd-remove-users&projectID='.$id;
			//execute post request
			$postResponse = Parent::executePostRequest($postUrl, $jsonData);
			//refresh page
			return $this->redirect(['add-user', 'id' => $project->ProjectID]);
		}
		else
		{
		return $this -> render('add_user', [
										'project' => $project,
										'model' => $model,
										'unassignedData' => $unassignedData,
										'assignedData' => $assignedData,
								]);
		}
	}
	
	public function actionAddModule($id)
	{
		//guest redirect
		if (Yii::$app->user->isGuest)
		{
			return $this->redirect(['login/login']);
		}

		self::requirePermission("projectAddRemoveModules");
		
		//TODO change urls
		$moduleUrl = 'project%2Fget-project-modules&projectID='.$id;
		$projectUrl = 'project%2Fview&id='.$id;

		//indirect rbac
		$moduleResponse = Parent::executeGetRequest($moduleUrl);
		$projectResponse = Parent::executeGetRequest($projectUrl);


		$modules = json_decode($moduleResponse,true);
		$project = json_decode($projectResponse);

		//load get data into variables
		//TODO change keys
		$inactiveData = $modules["unassignedModules"];
		$activeData = $modules["assignedModules"];

		//create model for active form
		$model = new \yii\base\DynamicModel([
			'InactiveModules', 'ActiveModules']);
		$model
			->addRule('InactiveModules', 'string')
			->addRule('ActiveModules',  'string');



		if ($model->load(Yii::$app->request->post()))
		{
			//prepare arrays for post request
			//explode strings from active form into arrays
			$deactivatedModulesArray = explode(',',$model->InactiveModules);
			$activatedModulesArray = explode(',',$model->ActiveModules);
			//array diff new arrays with arrays previous to submission to get changes
			$modulesAdded = array_diff($activatedModulesArray,array_keys($activeData));
			$modulesRemoved = array_diff($deactivatedModulesArray,array_keys($inactiveData));
			//load arrays of changes into post data
			$data = [];
			//TODO change json keys
			$data["modulesRemoved"] = $modulesRemoved;
			$data["modulesAdded"] = $modulesAdded;

			//encode data
			$jsonData = json_encode($data);

			//set post url
			//TODO change url
			$postUrl = 'project%2Fadd-remove-module&projectID='.$id;
			//execute post request
			$postResponse = Parent::executePostRequest($postUrl, $jsonData);
			//refresh page
			return $this->redirect(['add-module', 'id' => $project->ProjectID]);
		}
		else
		{
		return $this -> render('add_module', [
										'project' => $project,
										'model' => $model,
										'inactiveData' => $inactiveData,
										'activeData' => $activeData,
								]);
		}
	}


}
