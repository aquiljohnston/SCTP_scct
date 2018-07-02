<?php

namespace app\controllers;

use Yii;
use app\models\project;
use app\models\ProjectSearch;
use app\controllers\BaseController;
use yii\data\Pagination;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\ForbiddenHttpException;
use yii\data\ArrayDataProvider;
use linslin\yii2\curl;
use app\constants\Constants;
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
            return $this->redirect(['/login']);
        }
		
		//Check if user has permission to view project page
		self::requirePermission("viewProjectMgmt");
		
        $model = new \yii\base\DynamicModel([
            'filter', 'pagesize', 'page'
        ]);
        $model->addRule('filter', 'string', ['max' => 32])
            ->addRule('page', 'integer')
            ->addRule('pagesize', 'integer');

        // check if type was post, if so, get value from $model
        if (!$model->load(Yii::$app->request->get())) {
			$model->page = 1;
            $model->pagesize = 100;
            $model->filter = "";
        }

		// Reading the response from the the api and filling the GridView
        $url = "project%2Fget-all&"
            . http_build_query(
                [
                    'filter' => $model->filter,
                    'listPerPage' => $model->pagesize,
                    'page' => $model->page
                ]);
		$response = Parent::executeGetRequest($url, Constants::API_VERSION_2); // indirect rbac

        Yii::trace("Response from ProjectController: $response");
		$resultData = json_decode($response, true);
        $pages = new Pagination($resultData['pages']);
		//Passing data to the dataProvider and formating it in an associative array
		$dataProvider = new ArrayDataProvider([
			'allModels' => $resultData['assets'],
			'pagination' => [
				'pageSize' => $model->pagesize,
			],
		]);

        // Sorting Project table
        $dataProvider->sort = [
            'attributes' => [
                'ProjectName' => [
                    'asc' => ['ProjectName' => SORT_ASC],
                    'desc' => ['ProjectName' => SORT_DESC]
                ],
                'ProjectType' => [
                    'asc' => ['ProjectType' => SORT_ASC],
                    'desc' => ['ProjectType' => SORT_DESC]
                ],
                'ProjectState' => [
                    'asc' => ['ProjectState' => SORT_ASC],
                    'desc' => ['ProjectState' => SORT_DESC]
                ],
                'ProjectStartDate' => [
                    'asc' => ['ProjectStartDate' => SORT_ASC],
                    'desc' => ['ProjectStartDate' => SORT_DESC]
                ],
                'ProjectEndDate' => [
                    'asc' => ['ProjectEndDate' => SORT_ASC],
                    'desc' => ['ProjectEndDate' => SORT_DESC]
                ]
            ]
        ];
		return $this -> render('index', [
			'dataProvider' => $dataProvider,
			'canCreateProjects' => self::can("projectCreate"),
            'model' => $model,
            'pages' => $pages,
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
			return $this->redirect(['/login']);
		}
		
		//Check if user has permissions
		self::requirePermission("projectView");
		
		$url = "project%2Fview&joinNames=true&id=$id";
		$response = Parent::executeGetRequest($url, Constants::API_VERSION_2); // indirect rbac
        Yii::trace("VIEW PROJECT : ".$response);

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
			return $this->redirect(['/login']);
		}
		self::requirePermission("projectCreate");
	
		$model  = new Project();
			  
		//get clients for form dropdown
		$clientUrl = "client%2Fget-client-dropdowns";
		$clientResponse = Parent::executeGetRequest($clientUrl, Constants::API_VERSION_2);
		$clients = json_decode($clientResponse, true);
		
		//get states for form dropdown
		$stateUrl = 'dropdown%2Fget-state-codes-dropdown';
		$stateResponse = Parent::executeGetRequest($stateUrl, Constants::API_VERSION_2);
		$states = json_decode($stateResponse, true);
		
		//generate array for Active Flag dropdown
		$flag = 
		[
			1 => "Active",
			0 => "Inactive",
		];
		
		if(Yii::$app->session->has('webDropDowns') && array_key_exists('ProjectLanding', Yii::$app->session['webDropDowns']))
		{
			$landingPageArray = Yii::$app->session['webDropDowns']['ProjectLanding'];
			foreach($landingPageArray as $page)
			{
				$landingPages[$page['FieldValue']]= $page['FieldDisplay'];
			}
		}
		
		if ($model->load(Yii::$app->request->post())){
			
			$data =array(
				'ProjectName' => $model->ProjectName,
				'ProjectDescription' => $model->ProjectDescription,
				'ProjectNotes' => $model->ProjectNotes,
				'ProjectType' => $model->ProjectType,
				'ProjectStatus' => $model->ProjectStatus,
				'ProjectUrlPrefix' => $model->ProjectUrlPrefix,
				'ProjectLandingPage' => $model->ProjectLandingPage,
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
				$response = Parent::executePostRequest($url, $json_data, Constants::API_VERSION_2);
				
				$obj = json_decode($response, true);

				return $this->redirect(['view', 'id' => $obj["ProjectID"]]);
			} catch (\Exception $e) {
				return $this->render('create', [
					'model' => $model,
					'clients' => $clients,
					'flag' => $flag,
					'states' => $states,
					'landingPages' => $landingPages,
				]);
			}
		}else {
			return $this->render('create',[
				'model' => $model,
				'clients' => $clients,
				'flag' => $flag,
				'states' => $states,
				'landingPages' => $landingPages,
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
			return $this->redirect(['/login']);
		}
		self::requirePermission("projectUpdate");
		$getUrl = 'project%2Fview&id='.$id;
		$getResponse = json_decode(Parent::executeGetRequest($getUrl, Constants::API_VERSION_2), true);

		$model  = new Project();
		$model->attributes = $getResponse;
			  
		//get clients for form dropdown
		$clientUrl = "client%2Fget-client-dropdowns";
		$clientResponse = Parent::executeGetRequest($clientUrl, Constants::API_VERSION_2);
		$clients = json_decode($clientResponse, true);
		
		//get states for form dropdown
		$stateUrl = "dropdown%2Fget-state-codes-dropdown";
        $stateResponse = Parent::executeGetRequest($stateUrl, Constants::API_VERSION_2);
		$states = json_decode($stateResponse, true);
		
		//generate array for Active Flag dropdown
		$flag = 
		[
			1 => "Active",
			0 => "Inactive",
		];
		
		if(Yii::$app->session->has('webDropDowns') && array_key_exists('ProjectLanding', Yii::$app->session['webDropDowns']))
		{
			$landingPageArray = Yii::$app->session['webDropDowns']['ProjectLanding'];
			foreach($landingPageArray as $page)
			{
				$landingPages[$page['FieldValue']]= $page['FieldDisplay'];
			}
		}
			  
		if ($model->load(Yii::$app->request->post()))
		{
			$data =array(
				'ProjectName' => $model->ProjectName,
				'ProjectDescription' => $model->ProjectDescription,
				'ProjectNotes' => $model->ProjectNotes,
				'ProjectType' => $model->ProjectType,
				'ProjectStatus' => $model->ProjectStatus,
				'ProjectUrlPrefix' => $model->ProjectUrlPrefix,
				'ProjectLandingPage' => $model->ProjectLandingPage,
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
                $putResponse = Parent::executePutRequest($putUrl, $json_data, Constants::API_VERSION_2);
				
				$obj = json_decode($putResponse, true);
                if(isset($obj["status"]) && $obj["status"] == 400) {
                    return $this->render('update', [
                        'model' => $model,
                        'clients' => $clients,
                        'flag' => $flag,
                        'states' => $states,
                        'updateFailed' => true,
						'landingPages' => $landingPages,
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
                    'updateFailed' => true,
					'landingPages' => $landingPages,
				]);
			}
		} else {
			return $this->render('update', [
				'model' => $model,
				'clients' => $clients,
				'flag' => $flag,
				'states' => $states,
                'updateFailed' => false,
				'landingPages' => $landingPages,
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
            return $this->redirect(['/login']);
        }
        $url = 'project%2Fdeactivate&id='.$id;
        Parent::executePostRequest($url, "", Constants::API_VERSION_2); //indirect RBAC
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
			$projectDropdownResponse = Parent::executeGetRequest($projectDropdownUrl, Constants::API_VERSION_2); // indirect rbac
			//set up response data type
			Yii::$app->response->format = 'json';

			return ['projects' => $projectDropdownResponse];
		}
	}

	public function actionAddUser($id = null)
	{
		//guest redirect
		if (Yii::$app->user->isGuest)
		{
			return $this->redirect(['/login']);
		}
		$uaFilterParam = null;
		$aFilterParam = null;
		
		self::requirePermission('projectAddRemoveUsers');

		//create model for active form
		$model = new \yii\base\DynamicModel([
			'uaFilter',
			'aFilter'
		]);
		$model->addRule('uaFilter', 'string', ['max' => 32])
              ->addRule('aFilter', 'string', ['max' => 32]);

        // receive get request to filter user list
        if (Yii::$app->request->get()) 
		{
            if (isset($_GET['projectID']))
                $id = $_GET['projectID'];
			
            $model->load(Yii::$app->request->queryParams);

            $uaFilterParam = $model->uaFilter;
            $aFilterParam = $model->aFilter;

            $url = 'project%2Fget-user-relationships&projectID='.$id.'&uaFilter='.$uaFilterParam.'&aFilter='.$aFilterParam;
            $projectUrl = 'project%2Fview&id='.$id;

            $response = Parent::executeGetRequest($url, Constants::API_VERSION_2);
            $projectResponse = Parent::executeGetRequest($projectUrl, Constants::API_VERSION_2);

            $users = json_decode($response,true);
            $project = json_decode($projectResponse);
            //load get data into variables
            $unassignedData = $users['unassignedUsers'];
            $assignedData = $users['assignedUsers'];

            $unAssignedDataProvider = new ArrayDataProvider
            ([
                'allModels' => $unassignedData,
               	'pagination' => [
					'pageSize' => 200
            	]
            ]);
			$unAssignedDataProvider->key = 'userID';

        	$assignedDataProvider = new ArrayDataProvider
            ([
                'allModels' => $assignedData,
               	'pagination' => [   
					'pageParam' => 'pages',
					'pageSize' =>  200 
            	]
            ]);
            $assignedDataProvider->key = 'userID';


        	$unassignedPagination = $unAssignedDataProvider->getPagination();
        	$assignedPagination = $assignedDataProvider->getPagination();
			
            return $this -> render('add_user', [
                'project' 								=> $project,
                'model' 								=> $model,
                'dataProviderUnassigned'				=> $unAssignedDataProvider,
                'dataProviderAssigned'					=> $assignedDataProvider,
                'unAssignedPages'						=> $unAssignedDataProvider,
                'assignedPages' 						=> $assignedDataProvider,
                'unassignedFilterParams'				=> $uaFilterParam,
                'assignedFilterParams' 					=> $aFilterParam, 
                'unassignedPagination'					=> $unassignedPagination,
				'assignedPagination' 					=> $assignedPagination,
				'isAdmin'								=> Yii::$app->session['UserAppRoleType'] == 'Admin'
            ]);
        } elseif(Yii::$app->request->post()) {
			if (isset($_POST['projectID']))
                $id = $_POST['projectID'];

            $url = 'project%2Fget-user-relationships&projectID='.$id;

            //indirect rbac
            $response = Parent::executeGetRequest($url, Constants::API_VERSION_2);
            $users = json_decode($response,true);

            //load get data into variables
            $unassignedData = $users['unassignedUsers'];
            $assignedData = $users['assignedUsers'];
			
			$request = Yii::$app->request->post();

			//convert assigned data values to string
			$func = function($element) {return (string)$element;};

			//prepare arrays for post request
			//explode strings from active form into arrays
			$unassignedUsersArray = explode(',',$request["unassignedUsers"]);
			$assignedUsersArray = explode(',',$request["assignedUsers"]);
			//array diff new arrays with arrays previous to submission to get changes
			$usersAdded = array_values(array_diff($assignedUsersArray,array_map($func,array_keys($assignedData))));
			$usersRemoved = array_values(array_diff($unassignedUsersArray,array_map($func,array_keys($unassignedData))));
			//load arrays of changes into post data
			$data = [];
			$data['usersRemoved'] = $usersRemoved;
			$data['usersAdded'] = $usersAdded;

			//encode data
			$jsonData = json_encode($data);
			//set post url
			$postUrl = 'project%2Fadd-remove-users&projectID='.$id;
			//execute post request
			$postResponse = Parent::executePostRequest($postUrl, $jsonData, Constants::API_VERSION_2);
		}
	}
		
	public function actionAddModule($id)
	{
		//guest redirect
		if (Yii::$app->user->isGuest)
		{
			return $this->redirect(['/login']);
		}

		self::requirePermission("projectAddRemoveModules");
		
		//TODO change urls
		$moduleUrl = 'project%2Fget-project-modules&projectID='.$id;
		$projectUrl = 'project%2Fview&id='.$id;

		//indirect rbac
		$moduleResponse = Parent::executeGetRequest($moduleUrl, Constants::API_VERSION_2);
		$projectResponse = Parent::executeGetRequest($projectUrl, Constants::API_VERSION_2);


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
			$postResponse = Parent::executePostRequest($postUrl, $jsonData, Constants::API_VERSION_2);
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
