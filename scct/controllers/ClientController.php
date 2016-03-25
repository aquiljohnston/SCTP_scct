<?php

namespace app\controllers;

use Yii;
use app\models\client;
use app\models\ClientSearch;
use app\controllers\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\ForbiddenHttpException;
use yii\grid\GridView;
use yii\data\ArrayDataProvider;
use linslin\yii2\curl;

/**
 * ClientController implements the CRUD actions for client model.
 */
class ClientController extends BaseController
{

    /**
     * Lists all client models.
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
		if (Yii::$app->user->can('viewClientIndex'))
		{
			// Reading the response from the the api and filling the GridView
			$url = "http://api.southerncrossinc.com/index.php?r=client%2Fget-all";
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
     * Displays a single client model.
     * @param integer $id
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
		if (Yii::$app->user->can('viewClient'))
		{
			$url = 'http://api.southerncrossinc.com/index.php?r=client%2Fview&id='.$id;
			$response = Parent::executeGetRequest($url);

			return $this -> render('view', ['model' => json_decode($response), true]);
		}
		else
		{
			throw new ForbiddenHttpException('You do not have adequate permissions to perform this action.');
		}
    }

    /**
     * Creates a new client model.
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
		if (Yii::$app->user->can('createClient'))
		{
			$model = new client();
				  
			//generate array for Active Flag dropdown
			$flag = 
			[
				1 => "Active",
				0 => "Inactive",
			];
			
			//get clients for form dropdown
			$clientAccountsUrl = "http://api.southerncrossinc.com/index.php?r=client-accounts%2Fget-client-account-dropdowns";
			$clientAccountsResponse = Parent::executeGetRequest($clientAccountsUrl);
			$clientAccounts = json_decode($clientAccountsResponse, true);
				  
			if ($model->load(Yii::$app->request->post())){
				
				$data =array(
					'ClientAccountID' => $model->ClientAccountID,
					'ClientName' => $model->ClientName,
					'ClientContactTitle' => $model->ClientContactTitle,
					'ClientContactFName' => $model->ClientContactFName,
					'ClientContactMI' => $model->ClientContactMI,
					'ClientContactLName' => $model->ClientContactLName,
					'ClientPhone' => $model->ClientPhone,
					'ClientEmail' => $model->ClientEmail,
					'ClientAddr1' => $model->ClientAddr1,
					'ClientAddr2' => $model->ClientAddr2,
					'ClientCity' => $model->ClientCity,
					'ClientState' => $model->ClientState,
					'ClientZip4' => $model->ClientZip4,
					'ClientTerritory' => $model->ClientTerritory,
					'ClientActiveFlag' => $model->ClientActiveFlag,
					'ClientDivisionsFlag' => $model->ClientDivisionsFlag,
					'ClientComment' => $model->ClientComment,
					'ClientCreateDate' => $model->ClientCreateDate,
					'ClientCreatorUserID' => Yii::$app->session['userID'],
					'ClientModifiedDate' => $model->ClientModifiedDate,
					'ClientModifiedBy' => $model->ClientModifiedBy,
					);

				$json_data = json_encode($data);

				// post url
				$url= "http://api.southerncrossinc.com/index.php?r=client%2Fcreate";			
				$response = Parent::executePostRequest($url, $json_data);
				
				$obj = json_decode($response, true);

				return $this->redirect(['view', 'id' => $obj["ClientID"]]);
			}else {
				return $this->render('create',[
					'model' => $model,
					'flag' => $flag,
					'clientAccounts' => $clientAccounts,
					]);
			}
		}
		else
		{
			throw new ForbiddenHttpException('You do not have adequate permissions to perform this action.');
		}
    }

    /**
     * Updates an existing client model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
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
		if (Yii::$app->user->can('updateClient'))
		{
			$getUrl = 'http://api.southerncrossinc.com/index.php?r=client%2Fview&id='.$id;
			$getResponse = json_decode(Parent::executeGetRequest($getUrl), true);

			$model = new client();
			$model->attributes = $getResponse;
				  
			//generate array for Active Flag dropdown
			$flag = 
			[
				1 => "Active",
				0 => "Inactive",
			];
			
			//get clients for form dropdown
			$clientAccountsUrl = "http://api.southerncrossinc.com/index.php?r=client-accounts%2Fget-client-account-dropdowns";
			$clientAccountsResponse = Parent::executeGetRequest($clientAccountsUrl);
			$clientAccounts = json_decode($clientAccountsResponse, true);
				  
			if ($model->load(Yii::$app->request->post()))
			{
				$data =array(
					'ClientAccountID' => $model->ClientAccountID,
					'ClientName' => $model->ClientName,
					'ClientContactTitle' => $model->ClientContactTitle,
					'ClientContactFName' => $model->ClientContactFName,
					'ClientContactMI' => $model->ClientContactMI,
					'ClientContactLName' => $model->ClientContactLName,
					'ClientPhone' => $model->ClientPhone,
					'ClientEmail' => $model->ClientEmail,
					'ClientAddr1' => $model->ClientAddr1,
					'ClientAddr2' => $model->ClientAddr2,
					'ClientCity' => $model->ClientCity,
					'ClientState' => $model->ClientState,
					'ClientZip4' => $model->ClientZip4,
					'ClientTerritory' => $model->ClientTerritory,
					'ClientActiveFlag' => $model->ClientActiveFlag,
					'ClientDivisionsFlag' => $model->ClientDivisionsFlag,
					'ClientComment' => $model->ClientComment,
					'ClientCreateDate' => $model->ClientCreateDate,
					'ClientCreatorUserID' => $model->ClientCreatorUserID,
					'ClientModifiedDate' => $model->ClientModifiedDate,
					'ClientModifiedBy' => Yii::$app->session['userID'],
					);

				$json_data = json_encode($data);
				
				$putUrl = 'http://api.southerncrossinc.com/index.php?r=client%2Fupdate&id='.$id;
				$putResponse = Parent::executePutRequest($putUrl, $json_data);
				
				$obj = json_decode($putResponse, true);
				
				return $this->redirect(['view', 'id' => $model["ClientID"]]);
			} else {
				return $this->render('update', [
					'model' => $model,
					'flag' => $flag,
					'clientAccounts' => $clientAccounts,
				]);
			} 
		}
		else
		{
			throw new ForbiddenHttpException('You do not have adequate permissions to perform this action.');
		}
    }

    /**
     * Deletes an existing client model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
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
		if (Yii::$app->user->can('deleteUser'))
		{
			$url = 'http://api.southerncrossinc.com/index.php?r=client%2Fdelete&id='.$id;
			Parent::executeDeleteRequest($url);
			$this->redirect('/index.php?r=client%2Findex');
		}
		else
		{
			throw new ForbiddenHttpException('You do not have adequate permissions to perform this action.');
		}
    }
}
