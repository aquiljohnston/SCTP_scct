<?php

namespace app\controllers;

use Yii;
use app\models\user;
use app\models\UserSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use linslin\yii2\curl;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;

/**
 * UserController implements the CRUD actions for user model.
 */
class UserController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all user models.
     * @return mixed
     */
    public function actionIndex()
    {
		// Reading the response from the the api and filling the GridView
		//$curl = new curl\Curl();
 		
 		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL,"http://api.southerncrossinc.com/index.php?r=user%2Findex");
		// not a post
		//curl_setopt($ch, CURLOPT_POST, 1);
		//curl_setopt($ch, CURLOPT_POSTFIELDS,$vars);  //Post Fields
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$headers = array(
			'Content-Type:application/json',
    		'Authorization: Basic '. base64_encode("user:password")
			);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec ($curl);
		curl_close ($curl);

        //$response = $curl->get('http://api.southerncrossinc.com/index.php?r=user%2Findex');
		
		//Passing data to the dataProvider and formating it in an associative array
		$dataProvider = new ArrayDataProvider([
        'allModels' => json_decode($response,true),
		]);
		
				GridView::widget([
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
		$curl = new curl\Curl();
        
        //get http://example.com/
        $response = $curl->get('http://api.southerncrossinc.com/index.php?r=user%2Fview&id='.$id);

		return $this -> render('view', ['model' => json_decode($response)]);
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
			  ->addRule('UserKey', 'integer')
			  ->addRule('UserActiveFlag', 'integer')
			  ->addRule('UserModifiedDTLTOffset', 'integer')
			  ->addRule('UserInactiveDTLTOffset', 'integer')
			  ->addRule('UserCreatedDate', 'safe')
			  ->addRule('UserModifiedDate', 'safe');
		
		//$model = new user();	
		$curl = new curl\Curl();
		
		// post url
		$url_send = "http://api.southerncrossinc.com/index.php?r=user%2Fcreate";
		
		if ($model->load(Yii::$app->request->post())){
			
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
				$obj = (array)json_decode($result);
				
			/*$response = $curl->setOption(
				CURLOPT_POSTFIELDS, 
				http_build_query(array(
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
				)
			))
			->post('http://api.southerncrossinc.com/index.php?r=user%2Fcreate'); 
			$data = json_decode($response,true);*/
			
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
		$model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {	
			 return $this->redirect(['view', 'id' => $model["UserID"]]);
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
		$this->findModel($id)->delete();

        return $this->redirect(['index']);
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
