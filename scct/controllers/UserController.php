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
use yii\web\ServerErrorHttpException;
use yii\web\UnauthorizedHttpException;
use app\constants\Constants;

/**
 * UserController implements the CRUD actions for user model.
 */
class UserController extends BaseController
{
	//user request types for drodpowns
	const USER_CREATE_REQUEST = 'Create';
    const USER_UPDATE_REQUEST = 'Update';
	
    /**
     * Lists all user models.
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws \yii\web\HttpException
     */
    public function actionIndex()
    {
        try {
			 //guest redirect
			if (Yii::$app->user->isGuest) {
				return $this->redirect(['/login']);
			}
            //Check if user has permission to view user page
            self::requirePermission("viewUserMgmt");

            $model = new \yii\base\DynamicModel([
                'filter', 'pageSize', 'projectID'
            ]);
            $model->addRule('filter', 'string', ['max' => 32])
                ->addRule('pageSize', 'string', ['max' => 32])//get page number and records per page
				->addRule('projectID', 'string', ['max' => 32]);//set to string to accommodate all options

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
			
			//"sort":"-UserLastName"
            //get sort data
            if (isset($_GET['sort'])){
                $sort = $_GET['sort'];
                //parse sort data
                $sortField = str_replace('-', '', $sort, $sortCount);
                $sortOrder = $sortCount > 0 ? 'DESC' : 'ASC';
            } else {
					//default sort values
					$sortField = 'UserLastName';
					$sortOrder = 'ASC';
            }

            //build url with params
			$userQueryParams = http_build_query([
				'filter' => $model->filter,
				'listPerPage' => $model->pageSize,
				'page' => $page,
				'projectID' => $model->projectID,
				'sortField' => $sortField,
				'sortOrder' => $sortOrder,
			]);
            $userGetUrl = 'user%2Fget-active&' . $userQueryParams;
            $userGetResponse = Parent::executeGetRequest($userGetUrl, Constants::API_VERSION_2);
            $userGetResponse = json_decode($userGetResponse, true);
            $assets = $userGetResponse['assets'];
			$showProjectDropdown = $userGetResponse['showProjectDropdown'];
			$projectDropdown = $userGetResponse['projects'];			
			
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
			
			$dataProvider->sort = [
				'defaultOrder' => [$sortField => ($sortOrder == 'ASC') ? SORT_ASC : SORT_DESC],
				'attributes' => [
					'UserName',
					'UserFirstName',
					'UserLastName',
					'UserAppRoleType',
				]
			];
			
            //populate add/remove modal options
            $addRemoveProjects = array();
			$tmpProjectArray = $projectDropdown;
			//remove generic options from data set
			if(array_key_exists('all', $tmpProjectArray)) unset($tmpProjectArray['all']);
			if(array_key_exists('unassigned', $tmpProjectArray)) unset($tmpProjectArray['unassigned']);
            foreach($tmpProjectArray as $projectID => $projectName){
                $tmpProjectObj = array('ProjectID'=> $projectID, 'ProjectName'=> $projectName);
                array_push($addRemoveProjects, $tmpProjectObj);
            }
			
			//check user create/deactivate permission to hide buttons
			$canDeactivate = self::can('userDeactivate');
			$canCreate = self::can('userCreate');

            return $this->render('index', [
                'dataProvider' => $dataProvider,
                'model' => $model,
                'pages' => $pages,
                'page' => $page,
                'addRemoveProjects' => $addRemoveProjects,
				'projectDropdown' => $projectDropdown,
				'showProjectDropdown' => $showProjectDropdown,
				'canDeactivate' => $canDeactivate,
				'canCreate' => $canCreate,
            ]);
        } catch (UnauthorizedHttpException $e){
            Yii::$app->response->redirect(['login/index']);
        } catch(ForbiddenHttpException $e) {
            throw $e;
        } catch(ErrorException $e) {
            throw new \yii\web\HttpException(400);
        } catch(Exception $e) {
            throw new ServerErrorHttpException();
        }
    }

    /**
     * Displays a single user model.
     * @param string $username
     * @return mixed
     */
    public function actionView($username)
    {
		try{
			//guest redirect
			if (Yii::$app->user->isGuest) {
				return $this->redirect(['/login']);
			}
			
			//Check if user has permissions
			self::requirePermission("userView");
			
			$url = 'user%2Fview&' . http_build_query([
					'username' => $username,
				]);
			$response = Parent::executeGetRequest($url, Constants::API_VERSION_2); // indirect rbac

			// Generate User Permission Table
			$userPermissionTable = SELF::getUserPermissionTable();

			return $this->render('view', [
				'model' => json_decode($response, true),
				'userPermissionTable' => $userPermissionTable,
			]);
		} catch (UnauthorizedHttpException $e){
            Yii::$app->response->redirect(['login/index']);
        } catch(ForbiddenHttpException $e) {
            throw $e;
        } catch(ErrorException $e) {
            throw new \yii\web\HttpException(400);
        } catch(Exception $e) {
            throw new ServerErrorHttpException();
        }
    }

    /**
     * Creates a new user model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
		try{
			//guest redirect
			if (Yii::$app->user->isGuest) {
				return $this->redirect(['/login']);
			}

			self::requirePermission('userCreate');
			$model = new User();

			//get App Roles for form dropdown
			$rolesUrl = 'dropdown%2Fget-roles-dropdowns&' . http_build_query([
					'type' => self::USER_CREATE_REQUEST,
				]);
			$rolesResponse = Parent::executeGetRequest($rolesUrl, Constants::API_VERSION_3);
			$roles = json_decode($rolesResponse, true);

			//get types for form dropdown
			$typeUrl = 'dropdown%2Fget-employee-type-dropdown';
			$typeResponse = Parent::executeGetRequest($typeUrl);
			$types = json_decode($typeResponse, true);
			
			$yesNo = [
				0 => 'No',
				1 => 'Yes'
			];

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
					$url = 'user%2Fcreate';
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
						'yesNo' => $yesNo,
						'duplicateFlag' => 1,
					]);
				}

			} else {
				return $this->render('create', [
					'model' => $model,
					'roles' => $roles,
					'types' => $types,
					'yesNo' => $yesNo,
					'duplicateFlag' => 0,
				]);
			}
		} catch (UnauthorizedHttpException $e){
            Yii::$app->response->redirect(['login/index']);
        } catch(ForbiddenHttpException $e) {
            throw $e;
        } catch(ErrorException $e) {
            throw new \yii\web\HttpException(400);
        } catch(Exception $e) {
            throw new ServerErrorHttpException();
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
		try{
			//guest redirect
			if (Yii::$app->user->isGuest) {
				return $this->redirect(['/login']);
			}
			self::requirePermission("userUpdate");
			$getUrl = 'user%2Fview&' . http_build_query([
				'username' => $username,
			]);
			$getResponse = json_decode(Parent::executeGetRequest($getUrl, Constants::API_VERSION_2), true);

			$model = new User();
			$model->attributes = $getResponse;

			//get App Roles for form dropdown
			$rolesUrl = 'dropdown%2Fget-roles-dropdowns&' . http_build_query([
					'type' => self::USER_UPDATE_REQUEST,
				]);
			$rolesResponse = Parent::executeGetRequest($rolesUrl, Constants::API_VERSION_3);
			$roles = json_decode($rolesResponse, true);

			//get types for form dropdown
			$typeUrl = 'dropdown%2Fget-employee-type-dropdown';
			$typeResponse = Parent::executeGetRequest($typeUrl);
			$types = json_decode($typeResponse, true);
			
			$yesNo = [
				0 => 'No',
				1 => 'Yes'
			];

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

				$putUrl = 'user%2Fupdate&' . http_build_query([
					'username' => $username,
				]);
				$putResponse = Parent::executePutRequest($putUrl, $json_data, Constants::API_VERSION_2);
				$obj = json_decode($putResponse, true);

				return $this->redirect(['user/index']);
			} else {
				return $this->render('update', [
					'model' => $model,
					'roles' => $roles,
					'types' => $types,
					'yesNo' => $yesNo,
				]);
			}
		} catch (UnauthorizedHttpException $e){
            Yii::$app->response->redirect(['login/index']);
        } catch(ForbiddenHttpException $e) {
            throw $e;
        } catch(ErrorException $e) {
            throw new \yii\web\HttpException(400);
        } catch(Exception $e) {
            throw new ServerErrorHttpException();
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
		try{
			//guest redirect
			if (Yii::$app->user->isGuest) {
				return $this->redirect(['/login']);
			}
			//calls route to deactivate user account
			$url = 'user%2Fdeactivate&' . http_build_query([
				'username' => $username,
			]);
			//empty body
			$json_data = "";
			Parent::executePutRequest($url, $json_data, Constants::API_VERSION_2); // indirect rbac
			$this->redirect('/user/');
		} catch (UnauthorizedHttpException $e){
            Yii::$app->response->redirect(['login/index']);
        } catch(ForbiddenHttpException $e) {
            throw $e;
        } catch(ErrorException $e) {
            throw new \yii\web\HttpException(400);
        } catch(Exception $e) {
            throw new ServerErrorHttpException();
        }
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
        } catch (UnauthorizedHttpException $e){
            Yii::$app->response->redirect(['login/index']);
        } catch(ForbiddenHttpException $e) {
            throw $e;
        } catch(ErrorException $e) {
            throw new \yii\web\HttpException(400);
        } catch(Exception $e) {
            throw new ServerErrorHttpException();
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
        } catch (UnauthorizedHttpException $e){
            Yii::$app->response->redirect(['login/index']);
        } catch(ForbiddenHttpException $e) {
            throw $e;
        } catch(ErrorException $e) {
            throw new \yii\web\HttpException(400);
        } catch(Exception $e) {
            throw new ServerErrorHttpException();
        }
    }

    /**
     * Generate userPermissionTable
	 * TODO look into another way to handle this should not be derived from hardcoded web values
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
