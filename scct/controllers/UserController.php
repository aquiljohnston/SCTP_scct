<?php

namespace app\controllers;

use Yii;
use app\models\user;
use app\models\UserSearch;
use app\controllers\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use linslin\yii2\curl;
use yii\data\ArrayDataProvider;
use yii\data\Pagination;
use yii\grid\GridView;
use yii\base\Exception;
use yii\web\ForbiddenHttpException;
use app\constants\Constants;

/**
 * UserController implements the CRUD actions for user model.
 */
class UserController extends BaseController
{
    /**
     * Lists all user models.
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws \yii\web\HttpException
     */
    public function actionIndex()
    {
        //guest redirect
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['/login']);
        }
        try {
            //Check if user has permission to view user page
            self::requirePermission("viewUserMgmt");

            $model = new \yii\base\DynamicModel([
                'filter', 'pageSize', 'projectID'
            ]);
            $model->addRule('filter', 'string', ['max' => 32])
                ->addRule('pageSize', 'string', ['max' => 32])//get page number and records per page
				->addRule('projectID', 'integer');

            if (!$model->load(Yii::$app->request->get())) {
                $model->pageSize = 50;
                $model->filter = "";
				$model->projectID = null;
            }

            if (isset(Yii::$app->request->queryParams['UserManagementPageNumber'])) {
                $page = intval(Yii::$app->request->queryParams['UserManagementPageNumber']);
            } else {
                $page = 1;
            }

            //build url with params
			$userQueryParams = http_build_query([
				'filter' => $model->filter,
				'listPerPage' => $model->pageSize,
				'page' => $page,
				'projectID' => $model->projectID,
			]);
            $userGetUrl = 'user%2Fget-active&' . $userQueryParams;
            $userGetResponse = Parent::executeGetRequest($userGetUrl, Constants::API_VERSION_2);
            $userGetResponse = json_decode($userGetResponse, true);
            $assets = $userGetResponse['assets'];
            //Passing data to the dataProvider and formatting it in an associative array
            $dataProvider = new ArrayDataProvider
            ([
                'allModels' => $assets,
                'pagination' => [
                    'pageSize' => $model->pageSize,
                ],
            ]);

            // set pages to dispatch table
            $pages = new Pagination($userGetResponse['pages']);

            // Sorting User table
            $dataProvider->sort = [
                'attributes' => [
                    'UserName' => [
                        'asc' => ['UserName' => SORT_ASC],
                        'desc' => ['UserName' => SORT_DESC]
                    ],
                    'UserFirstName' => [
                        'asc' => ['UserFirstName' => SORT_ASC],
                        'desc' => ['UserFirstName' => SORT_DESC]
                    ],
                    'UserLastName' => [
                        'asc' => ['UserLastName' => SORT_ASC],
                        'desc' => ['UserLastName' => SORT_DESC]
                    ],
                    'UserAppRoleType' => [
                        'asc' => ['UserAppRoleType' => SORT_ASC],
                        'desc' => ['UserAppRoleType' => SORT_DESC]
                    ]
                ]
            ];

            // Generate User Permission Table
            $userPermissionTable = SELF::getUserPermissionTable();
            
            //todo: create new route instead of using actionGetMe 
            $url = "user%2Fget-me";
            $response = Parent::executeGetRequest($url, Constants::API_VERSION_2);
            $response = json_decode($response, true);
            $tmpArray = $response['Projects'];
            $projects = array();
            foreach($tmpArray as $project){
                $tmp = array('ProjectID'=>$project['ProjectID'], 'ProjectName'=>$project['ProjectName']);
                array_push($projects, $tmp);
            }
			
			//get project dropdown
			$projectDropdownUrl = 'dropdown%2Fget-user-projects';
			$projectDropdownResponse = Parent::executeGetRequest($projectDropdownUrl, Constants::API_VERSION_2);
			$projectDropdownResponse = json_decode($projectDropdownResponse, true);
			$showProjectDropdown = $projectDropdownResponse['showProjectDropdown'];
			$projectDropdown = $projectDropdownResponse['projects'];
			
            return $this->render('index', [
                'dataProvider' => $dataProvider,
                'model' => $model,
                'pages' => $pages,
                'page' => $page,
                'userPermissionTable' => $userPermissionTable,
				//look into combining these
                'projects' => $projects,
				'projectDropdown' => $projectDropdown,
				'showProjectDropdown' => $showProjectDropdown,
            ]);
        } catch (UnauthorizedHttpException $e){
            Yii::$app->response->redirect(['login/index']);
        } catch (ForbiddenHttpException $e) {
            throw $e;
        } catch (Exception $e) {
            throw new \yii\web\HttpException(400);
        }
    }

    /**
     * Displays a single user model.
     * @param string $username
     * @return mixed
     */
    public function actionView($username)
    {
        //guest redirect
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['/login']);
        }
		
		//Check if user has permissions
		self::requirePermission("userView");
		
        $url = 'user%2Fview&username=' . $username;
        $response = Parent::executeGetRequest($url, Constants::API_VERSION_2); // indirect rbac

        // Generate User Permission Table
        $userPermissionTable = SELF::getUserPermissionTable();

        return $this->render('view', [
            'model' => json_decode($response, true),
            'userPermissionTable' => $userPermissionTable,
        ]);
    }

    /**
     * Creates a new user model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        //guest redirect
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['/login']);
        }
        Yii::Trace("user id: " . Yii::$app->user->getId());

        self::requirePermission('userCreate');
        $model = new User();

        //get App Roles for form dropdown
        $rolesUrl = "dropdown%2Fget-roles-dropdowns";
        $rolesResponse = Parent::executeGetRequest($rolesUrl);
        $roles = json_decode($rolesResponse, true);

        //get types for form dropdown
        $typeUrl = "dropdown%2Fget-employee-type-dropdown";
        $typeResponse = Parent::executeGetRequest($typeUrl);
        $types = json_decode($typeResponse, true);

        if ($model->load(Yii::$app->request->post())) {
			$model->UserActiveFlag = 1;
			$data = $model->attributes;

            //iv and secret key of openssl
            $iv = "abcdefghijklmnop";
            $secretKey = "sparusholdings12";

            //encrypt and encode password
            $encryptedPassword = openssl_encrypt($data['UserPassword'], 'AES-128-CBC', $secretKey, OPENSSL_RAW_DATA, $iv);
            $encodedPassword = base64_encode($encryptedPassword);

            $data['UserPassword'] = $encodedPassword;

            $json_data = json_encode($data);

            try {
                // post url
                $url = "user%2Fcreate";
                $response = Parent::executePostRequest($url, $json_data, Constants::API_VERSION_2);
                $obj = json_decode($response, true);

                return $this->redirect(['user/index']);
            } catch (Exception $e) {
                // duplicationflag:
                // 1: yes 0: no
                // set duplicateFlag to 1, which means duplication happened.
                return $this->render('create', [
                    'model' => $model,
                    'roles' => $roles,
                    'types' => $types,
                    'duplicateFlag' => 1,
                ]);
            }

        } else {
            return $this->render('create', [
                'model' => $model,
                'roles' => $roles,
                'types' => $types,
                'duplicateFlag' => 0,
            ]);
        }
    }

    /**
     * Updates an existing user model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $username
     * @return mixed
     */
    public function actionUpdate($username)
    {
        //guest redirect
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['/login']);
        }
        self::requirePermission("userUpdate");
        $getUrl = 'user%2Fview&username=' . $username;
        $getResponse = json_decode(Parent::executeGetRequest($getUrl, Constants::API_VERSION_2), true);

        $model = new User();
        $model->attributes = $getResponse;

        //get App Roles for form dropdown
        $rolesUrl = "dropdown%2Fget-roles-dropdowns";
        $rolesResponse = Parent::executeGetRequest($rolesUrl);
        $roles = json_decode($rolesResponse, true);

        //get types for form dropdown
        $typeUrl = "dropdown%2Fget-employee-type-dropdown";
        $typeResponse = Parent::executeGetRequest($typeUrl);
        $types = json_decode($typeResponse, true);

        if ($model->load(Yii::$app->request->post())) {
			$data = $model->attributes;

            //iv and secret key of openssl
            $iv = "abcdefghijklmnop";
            $secretKey = "sparusholdings12";

            //encrypt and encode password
            $encryptedKey = openssl_encrypt($data['UserPassword'], 'AES-128-CBC', $secretKey, OPENSSL_RAW_DATA, $iv);
            $encodedKey = base64_encode($encryptedKey);

            $data['UserPassword'] = $encodedKey;

            $json_data = json_encode($data);

            $putUrl = 'user%2Fupdate&username=' . $username;
            $putResponse = Parent::executePutRequest($putUrl, $json_data, Constants::API_VERSION_2);
            $obj = json_decode($putResponse, true);

            return $this->redirect(['user/index']);
        } else {
            return $this->render('update', [
                'model' => $model,
                'roles' => $roles,
                'types' => $types,
            ]);
        }

    }

    /**
     * Deletes an existing user model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $username
     * @return mixed
     */
    public function actionDeactivate($username)
    {
        //guest redirect
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['/login']);
        }
        //calls route to deactivate user account
        $url = 'user%2Fdeactivate&username=' . urlencode($username);
        //empty body
        $json_data = "";
        Parent::executePutRequest($url, $json_data, Constants::API_VERSION_2); // indirect rbac
        $this->redirect('/user/');
    }
    
    /**
     * Modal view for reactivating users
     * @return string|\yii\web\Response
     * @throws ForbiddenHttpException
     */
    public function actionReactivateUserModal()
    {
        try {
            if (Yii::$app->user->isGuest) {
                return $this->redirect(['/login']);
            }

            $model = new \yii\base\DynamicModel([
                'modalSearch'
            ]);
            $model->addRule('modalSearch', 'string', ['max' => 32]);

                $searchFilterVal = "";
            if (Yii::$app->request->post()){
                $data = Yii::$app->request->post();

                if (!empty($data["searchFilterVal"])) {
                    $searchFilterVal = $data["searchFilterVal"];
                }
            }

            // Reading the response from the the api and filling the surveyorGridView
            $getUrl = 'user%2Fget-inactive&' . http_build_query([
                    'filter' => $searchFilterVal,
                ]);
            $usersResponse = json_decode(Parent::executeGetRequest($getUrl, Constants::API_VERSION_2), true); // indirect rbac

            $dataProvider = new ArrayDataProvider
            ([
                'allModels' => $usersResponse['users'],
                'pagination' => false,
            ]);

            $dataProvider->key = 'UserName';

            if (Yii::$app->request->isAjax) {
                return $this->renderAjax('reactivate_user_modal', [
                    'reactivateUserDataProvider' => $dataProvider,
                    'model' => $model,
                    'searchFilterVal' => $searchFilterVal,
                ]);
            }else{
                return $this->render('reactivate_user_modal', [
                    'reactivateUserDataProvider' => $dataProvider,
                    'model' => $model,
                    'searchFilterVal' => $searchFilterVal,
                ]);
            }
        }catch(ForbiddenHttpException $e)
        {
            throw new ForbiddenHttpException;
        }
        catch(\Exception $e)
        {
            yii::trace('Exception' . json_encode($e));
            Yii::$app->runAction('login/user-logout');
        }
    }
    
    /**
     * Reactivate Function
     * @throws ForbiddenHttpException
     */
    public function actionReactivate()
    {
        try
        {
            if(Yii::$app->request->isAjax)
            {
                //get user data for put requesta
                $data = Yii::$app->request->post();
                //add url prefix to put body
                $data['ProjectUrlPrefix'] = BaseController::getXClient();
                //json encode put body
                $json_data = json_encode($data);
                
                // post url
                $putUrl = 'user%2Freactivate';
                $putResponse = Parent::executePutRequest($putUrl, $json_data, Constants::API_VERSION_2);
            }
        } catch (ForbiddenHttpException $e) {
            throw new ForbiddenHttpException;
        } catch (Exception $e) {
            //TODO implement alternative to logging out when a bad request is returned.
            Yii::$app->runAction('login/user-logout');
        }
    }

    /**
     * Generate userPermissionTable
     * @return array $userPermissionTable
     */
    private function getUserPermissionTable(){
        $userPermissionTable = array(
            '5' => 'Technician',
            '4' => 'Engineer',
            '3' => 'Supervisor',
            '2' => 'ProjectManager',
            '1' => 'Admin'
        );
        return $userPermissionTable;
    }
}
