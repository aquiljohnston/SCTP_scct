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
use yii\grid\GridView;
use yii\web\ForbiddenHttpException;

/**
 * UserController implements the CRUD actions for user model.
 */
class UserController extends BaseController
{
    /**
     * Lists all user models.
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
		$url = "user%2Fget-active";
		$response = Parent::executeGetRequest($url); // indirect rbac
		$filteredResultData = $this->filterColumnMultiple(json_decode($response, true), 'UserName', 'filterusername');
		$filteredResultData = $this->filterColumnMultiple($filteredResultData, 'UserFirstName', 'filterfirstname');
		$filteredResultData = $this->filterColumnMultiple($filteredResultData, 'UserLastName', 'filterlastname');
		$filteredResultData = $this->filterColumnMultiple($filteredResultData, 'UserAppRoleType', 'filterroletype');
		$usernameFilterParam = Yii::$app->request->getQueryParam('filterusername', '');
		$firstNameFilterParam = Yii::$app->request->getQueryParam('filterfirstname', '');
		$lastNameFilterParam = Yii::$app->request->getQueryParam('filterlastname', '');
		$roleTypeFilterParam = Yii::$app->request->getQueryParam('filterroletype', '');
		$searchModel = [
			'UserName' => $usernameFilterParam,
			'UserFirstName' => $firstNameFilterParam,
			'UserLastName' => $lastNameFilterParam,
			'UserAppRoleType' => $roleTypeFilterParam
		];
		//Passing data to the dataProvider and formatting it in an associative array
		$dataProvider = new ArrayDataProvider
		([
			'allModels' => $filteredResultData,
			'pagination' => [
				'pageSize' => 100,
			],
		]);

		return $this -> render('index', ['dataProvider' => $dataProvider, 'searchModel' => $searchModel]);
    }

    /**
     * Displays a single user model.
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
		$url = 'user%2Fview&id='.$id;
		$response = Parent::executeGetRequest($url); // indirect rbac

		return $this -> render('view', ['model' => json_decode($response), true]);
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
			return $this->redirect(['login/login']);
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
				'UserKey' => $model->UserKey,
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
			$encryptedKey = openssl_encrypt($data['UserKey'], 'AES-128-CBC', $secretKey, OPENSSL_RAW_DATA, $iv);
			$encodedKey = base64_encode($encryptedKey);

			$data['UserKey'] = $encodedKey;

			$json_data = json_encode($data);

			try {
				// post url
				$url = "user%2Fcreate";
				$response = Parent::executePostRequest($url, $json_data);

				$obj = json_decode($response, true);

				return $this->redirect(['view', 'id' => $obj["UserID"]]);
			} catch (\Exception $e) {

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
		if (Yii::$app->user->isGuest)
		{
			return $this->redirect(['login/login']);
		}
		self::requirePermission("userUpdate");
		$getUrl = 'user%2Fview&id='.$id;
		$getResponse = json_decode(Parent::executeGetRequest($getUrl), true);

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

		if ($model->load(Yii::$app->request->post()))
		{
			$data = array(
				'UserName' => $model->UserName,
				'UserFirstName' => $model-> UserFirstName,
				'UserLastName' => $model-> UserLastName,
				'UserEmployeeType' => $model-> UserEmployeeType,
				'UserPhone' => $model-> UserPhone,
				'UserCompanyName' => $model-> UserCompanyName,
				'UserCompanyPhone' => $model-> UserCompanyPhone,
				'UserAppRoleType' => $model-> UserAppRoleType,
				'UserComments' => $model-> UserComments,
				'UserKey' => $model-> UserKey,
				'UserActiveFlag' => $model-> UserActiveFlag,
				'UserCreatedDate' => $model-> UserCreatedDate,
				'UserModifiedDate' => $model-> UserModifiedDate,
				'UserCreateDTLTOffset' => $model-> UserCreateDTLTOffset,
				'UserModifiedDTLTOffset' => $model-> UserModifiedDTLTOffset,
				'UserInactiveDTLTOffset' => $model-> UserInactiveDTLTOffset,
				);

			//iv and secret key of openssl
			$iv = "abcdefghijklmnop";
			$secretKey= "sparusholdings12";

			//encrypt and encode password
			$encryptedKey = openssl_encrypt($data['UserKey'],  'AES-128-CBC', $secretKey, OPENSSL_RAW_DATA, $iv);
			$encodedKey = base64_encode($encryptedKey);

			$data['UserKey'] = $encodedKey;

			$json_data = json_encode($data);

			$putUrl = 'user%2Fupdate&id='.$id;
			$putResponse = Parent::executePutRequest($putUrl, $json_data);

			$obj = json_decode($putResponse, true);

			 return $this->redirect(['view', 'id' => $obj["UserID"]]);
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
		if (Yii::$app->user->isGuest)
		{
			return $this->redirect(['login/login']);
		}
		//calls route to deactivate user account
		$url = 'user%2Fdeactivate&userID='.$id;
		//empty body
		$json_data = "";
		Parent::executePutRequest($url, $json_data); // indirect rbac
		$this->redirect('/index.php?r=user%2Findex');
    }
}
