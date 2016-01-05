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
		// Reading the response from the the api and filling the GridView
		$url = "http://api.southerncrossinc.com/index.php?r=user%2Findex";
		$response = Parent::executeGetRequest($url);

        //Passing data to the dataProvider and formating it in an associative array
		$dataProvider = new ArrayDataProvider
		([
			'allModels' => json_decode($response,true),
		]);
		GridView::widget
		([
			'dataProvider' => $dataProvider,
		]);
		
		return $this -> render('index', ['dataProvider' => $dataProvider]);
    }

    /**
     * Displays a single user model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
		$url = 'http://api.southerncrossinc.com/index.php?r=user%2Fview&id='.$id;
		$response = Parent::executeGetRequest($url);

		return $this -> render('view', ['model' => json_decode($response), true]);
    }

    /**
     * Creates a new user model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
		$model = new \yii\base\DynamicModel([
			'UserName', 'UserFirstName', 'UserLastName', 'UserLoginID', 'UserEmployeeType',
			'UserPhone', 'UserCompanyName', 'UserCompanyPhone', 'UserAppRoleType', 'UserComments', 'UserKey',
			'UserActiveFlag', 'UserCreatedDate', 'UserModifiedDate', 'UserCreatedBy', 'UserModifiedBy',
			'UserCreateDTLTOffset', 'UserModifiedDTLTOffset', 'UserInactiveDTLTOffset', 'isNewRecord'
		]);
		
		$model->addRule('UserName', 'string')
			  ->addRule('UserFirstName', 'string')
			  ->addRule('UserLastName', 'string')
			  ->addRule('UserLoginID', 'string')
			  ->addRule('UserEmployeeType', 'string')
			  ->addRule('UserPhone', 'string')
			  ->addRule('UserCompanyName', 'string')
			  ->addRule('UserCompanyPhone', 'string')
			  ->addRule('UserAppRoleType', 'string')
			  ->addRule('UserComments', 'string')
			  ->addRule('UserCreatedBy', 'string')
			  ->addRule('UserModifiedBy', 'string')
			  ->addRule('UserCreateDTLTOffset', 'string')
			  ->addRule('UserKey', 'string')
			  ->addRule('UserActiveFlag', 'integer')
			  ->addRule('UserModifiedDTLTOffset', 'integer')
			  ->addRule('UserInactiveDTLTOffset', 'integer')
			  ->addRule('UserCreatedDate', 'safe')
			  ->addRule('UserModifiedDate', 'safe');
		
		if ($model->load(Yii::$app->request->post()))
		{
			$data = array(
				'UserName' => $model->UserName,
				'UserFirstName' => $model-> UserFirstName,
				'UserLastName' => $model-> UserLastName,
				'UserLoginID' => $model-> UserLoginID,
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
				'UserCreatedBy' => $model-> UserCreatedBy,
				'UserModifiedBy' => $model-> UserModifiedBy,
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
			
			// post url
			$url = "http://api.southerncrossinc.com/index.php?r=user%2Fcreate";	
			$response = Parent::executePostRequest($url, $json_data);
		
		
			$obj = json_decode($response, true);
	
			return $this->redirect(['view', 'id' => $obj["UserID"]]);
		}else{
			return $this->render('create', [
                'model' => $model,
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
		$getUrl = 'http://api.southerncrossinc.com/index.php?r=user%2Fview&id='.$id;
		$getResponse = json_decode(Parent::executeGetRequest($getUrl), true);
		// $keys = array_values(array_keys($response));

		// $arrKey = array();
		// for($i=0; $i<count($keys); $i++)
		// {
			// $arrKey[$i] = $keys[$i];
		// }
		
		// $model = new \yii\base\DynamicModel($arrKey);
		
		$model = new \yii\base\DynamicModel($getResponse);
		
		$model->addRule('UserName', 'string')
			  ->addRule('UserFirstName', 'string')
			  ->addRule('UserLastName', 'string')
			  ->addRule('UserLoginID', 'string')
			  ->addRule('UserEmployeeType', 'string')
			  ->addRule('UserPhone', 'string')
			  ->addRule('UserCompanyName', 'string')
			  ->addRule('UserCompanyPhone', 'string')
			  ->addRule('UserAppRoleType', 'string')
			  ->addRule('UserComments', 'string')
			  ->addRule('UserCreatedBy', 'string')
			  ->addRule('UserModifiedBy', 'string')
			  ->addRule('UserCreateDTLTOffset', 'string')
			  ->addRule('UserKey', 'string')
			  ->addRule('UserActiveFlag', 'integer')
			  ->addRule('UserModifiedDTLTOffset', 'integer')
			  ->addRule('UserInactiveDTLTOffset', 'integer')
			  ->addRule('UserCreatedDate', 'safe')
			  ->addRule('UserModifiedDate', 'safe');
		
		if ($model->load(Yii::$app->request->post()))
		{
			$data = array(
				'UserName' => $model->UserName,
				'UserFirstName' => $model-> UserFirstName,
				'UserLastName' => $model-> UserLastName,
				'UserLoginID' => $model-> UserLoginID,
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
				'UserCreatedBy' => $model-> UserCreatedBy,
				'UserModifiedBy' => $model-> UserModifiedBy,
				'UserCreateDTLTOffset' => $model-> UserCreateDTLTOffset,
				'UserModifiedDTLTOffset' => $model-> UserModifiedDTLTOffset,
				'UserInactiveDTLTOffset' => $model-> UserInactiveDTLTOffset,
				);
		
			$json_data = json_encode($data);
			
			$putUrl = 'http://api.southerncrossinc.com/index.php?r=user%2Fupdate&id='.$id;
			$putResponse = Parent::executePutRequest($putUrl, $json_data);
			
			$obj = json_decode($putResponse, true);
			
			 return $this->redirect(['view', 'id' => $obj["UserID"]]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        } 
    }

    /**
     * Deletes an existing user model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
		$url = 'http://api.southerncrossinc.com/index.php?r=user%2Fdelete&id='.$id;
		Parent::executeDeleteRequest($url);
		$this->redirect('/index.php?r=user%2Findex');
    }

    /**
     * Finds the user model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return user the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
         if (($model = user::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        } 
		
    }
}
