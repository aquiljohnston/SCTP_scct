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

/**
 * UserController implements the CRUD actions for user model.
 */
class UserController extends BaseController
{
    const USER_EXIST = "UserName already exist.";
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
                'filter', 'pagesize'
            ]);
            $model->addRule('filter', 'string', ['max' => 32])
                ->addRule('pagesize', 'string', ['max' => 32]);//get page number and records per page

            if ($model->load(Yii::$app->request->get())) {

                $listPerPageParam = $model->pagesize;
                $filterParam = $model->filter;
            } else {
                $listPerPageParam = 50;
                $filterParam = "";
            }

            if (isset(Yii::$app->request->queryParams['UserManagementPageNumber'])) {
                $page = intval(Yii::$app->request->queryParams['UserManagementPageNumber']);
            } else {
                $page = 1;
            }

            //todo: http_build_query()
            //build url with params
            $url = "user%2Fget-active&filter=" . urlencode($filterParam) . "&listPerPage=" . urlencode($listPerPageParam)
                . "&page=" . urlencode($page);
            Yii::trace("User index url: $url");
            $response = Parent::executeGetRequest($url, BaseController::API_VERSION_2);
            $response = json_decode($response, true);
            $assets = $response['assets'];
            //Passing data to the dataProvider and formatting it in an associative array
            $dataProvider = new ArrayDataProvider
            ([
                'allModels' => $assets,
                'pagination' => [
                    'pageSize' => 100,
                ],
            ]);

            // set pages to dispatch table
            $pages = new Pagination($response['pages']);

            return $this->render('index', [
                'dataProvider' => $dataProvider,
                'model' => $model,
                'pages' => $pages,
                'filter' => $filterParam,
                'userPageSizeParams' => $listPerPageParam,
                'page' => $page
            ]);

        } catch (ForbiddenHttpException $e) {
            throw $e;
        } catch (ErrorException $e) {
            throw new \yii\web\HttpException(400);
        } catch (Exception $e) {
            throw new ServerErrorHttpException;
        }
    }

    /**
     * Displays a single user model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        //guest redirect
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['/login']);
        }
        $url = 'user%2Fview&id=' . $id;
        $response = Parent::executeGetRequest($url, BaseController::API_VERSION_2); // indirect rbac

        return $this->render('view', ['model' => json_decode($response), true]);
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
        $rolesUrl = "app-roles%2Fget-roles-dropdowns";
        $rolesResponse = Parent::executeGetRequest($rolesUrl);
        $roles = json_decode($rolesResponse, true);

        //get types for form dropdown
        $typeUrl = "dropdown%2Fget-employee-type-dropdown";
        $typeResponse = Parent::executeGetRequest($typeUrl);
        $types = json_decode($typeResponse, true);

        if ($model->load(Yii::$app->request->post())) {
            $data = array(
                'UserName' => $model->UserName,
                'UserFirstName' => $model->UserFirstName,
                'UserLastName' => $model->UserLastName,
                'UserEmployeeType' => $model->UserEmployeeType,
                'UserPhone' => $model->UserPhone,
                'UserCompanyName' => $model->UserCompanyName,
                'UserCompanyPhone' => $model->UserCompanyPhone,
                'UserAppRoleType' => $model->UserAppRoleType,
                'UserComments' => $model->UserComments,
                'UserPassword' => $model->UserPassword,
                'UserActiveFlag' => 1,
                //'UserCreatedDate' => $model-> UserCreatedDate, Database auto populates this field on the HTTP post call
                //'UserModifiedDate' => $model-> UserModifiedDate, Database auto populates this field on the HTTP post call
                //'UserCreatedBy' => Yii::$app->session['userID'],
                //'UserModifiedBy' => $model->UserModifiedBy,
                'UserCreateDTLTOffset' => $model->UserCreateDTLTOffset,
                'UserModifiedDTLTOffset' => $model->UserModifiedDTLTOffset,
                'UserInactiveDTLTOffset' => $model->UserInactiveDTLTOffset,
            );

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
                $response = Parent::executePostRequest($url, $json_data, BaseController::API_VERSION_2);
                $obj = json_decode($response, true);
                if ($obj == self::USER_EXIST){
                    return $this->render('create', [
                        'model' => $model,
                        'roles' => $roles,
                        'types' => $types,
                        'duplicateFlag' => 1,
                    ]);
                }else{
                    return $this->redirect(['user/index']);
                }
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
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        //guest redirect
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['/login']);
        }
        self::requirePermission("userUpdate");
        $getUrl = 'user%2Fview&id=' . $id;
        $getResponse = json_decode(Parent::executeGetRequest($getUrl, BaseController::API_VERSION_2), true);

        $model = new User();
        $model->attributes = $getResponse;

        //get App Roles for form dropdown
        $rolesUrl = "app-roles%2Fget-roles-dropdowns";
        $rolesResponse = Parent::executeGetRequest($rolesUrl);
        $roles = json_decode($rolesResponse, true);

        //get types for form dropdown
        $typeUrl = "dropdown%2Fget-employee-type-dropdown";
        $typeResponse = Parent::executeGetRequest($typeUrl);
        $types = json_decode($typeResponse, true);

        //generate array for Active Flag dropdown
        $flag =
            [
                1 => "Active",
                0 => "Inactive",
            ];

        if ($model->load(Yii::$app->request->post())) {
            $data = array(
                'UserName' => $model->UserName,
                'UserFirstName' => $model->UserFirstName,
                'UserLastName' => $model->UserLastName,
                'UserEmployeeType' => $model->UserEmployeeType,
                'UserPhone' => $model->UserPhone,
                'UserCompanyName' => $model->UserCompanyName,
                'UserCompanyPhone' => $model->UserCompanyPhone,
                'UserAppRoleType' => $model->UserAppRoleType,
                'UserComments' => $model->UserComments,
                'UserPassword' => $model->UserPassword,
                'UserActiveFlag' => $model->UserActiveFlag,
                'UserCreatedDate' => $model->UserCreatedDate,
                'UserModifiedDate' => $model->UserModifiedDate,
                'UserCreateDTLTOffset' => $model->UserCreateDTLTOffset,
                'UserModifiedDTLTOffset' => $model->UserModifiedDTLTOffset,
                'UserInactiveDTLTOffset' => $model->UserInactiveDTLTOffset,
            );

            //iv and secret key of openssl
            $iv = "abcdefghijklmnop";
            $secretKey = "sparusholdings12";

            //encrypt and encode password
            $encryptedKey = openssl_encrypt($data['UserPassword'], 'AES-128-CBC', $secretKey, OPENSSL_RAW_DATA, $iv);
            $encodedKey = base64_encode($encryptedKey);

            $data['UserPassword'] = $encodedKey;

            $json_data = json_encode($data);

            $putUrl = 'user%2Fupdate&id=' . $id;
            $putResponse = Parent::executePutRequest($putUrl, $json_data, BaseController::API_VERSION_2);
            $obj = json_decode($putResponse, true);

            return $this->redirect(['view', 'id' => $id]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'roles' => $roles,
                'types' => $types,
                'flag' => $flag,
            ]);
        }

    }

    /**
     * Deletes an existing user model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDeactivate($id)
    {
        //guest redirect
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['/login']);
        }
        //calls route to deactivate user account
        $url = 'user%2Fdeactivate&userID=' . urlencode($id);
        //empty body
        $json_data = "";
        Parent::executePutRequest($url, $json_data, BaseController::API_VERSION_2); // indirect rbac
        $this->redirect('/user/');
    }
}
