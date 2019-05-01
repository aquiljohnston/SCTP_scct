<?php

namespace app\controllers;

use app\models\Client;
use Yii;
use yii\data\Pagination;
use yii\data\ArrayDataProvider;
use app\constants\Constants;
use yii\web\ForbiddenHttpException;
use yii\web\ServerErrorHttpException;
use yii\web\UnauthorizedHttpException;

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
		try{
			//guest redirect
			if (Yii::$app->user->isGuest) {
				return $this->redirect(['/login']);
			}
			//Check if user has permission to view client page
			self::requirePermission("viewClientMgmt");
			$model = new \yii\base\DynamicModel([
				'filter', 'pagesize', 'page'
			]);
			$model->addRule('filter', 'string', ['max' => 32])
				->addRule('pagesize', 'integer')
				->addRule('page', 'integer');

			// check if type was get, if so, get value from $model
			if (!$model->load(Yii::$app->request->get())) {
				$model->page = 1;
				$model->pagesize = 100;
				$model->filter = "";
			}

			$searchModel = [
				'ClientName' =>  Yii::$app->request->getQueryParam('filterclientname', ''),
				'ClientCity' => Yii::$app->request->getQueryParam('filtercityname', ''),
				'ClientState' => Yii::$app->request->getQueryParam('filterstatename', '')
			];

			$url = "client%2Fget-all&" . http_build_query([
				'filter' => $model->filter,
				'listPerPage' => $model->pagesize,
				'page' => $model->page,
				'filterclientname' => $searchModel['ClientName'],
				'filtercityname' => $searchModel['ClientCity'],
				'filterstatename' => $searchModel['ClientState']
			]);
			$response = Parent::executeGetRequest($url, Constants::API_VERSION_2); // RBAC permissions checked indirectly via this call
			$response = json_decode($response, true);
			$assets = $response['assets'];
			
			//Passing data to the dataProvider and formating it in an associative array
			$dataProvider = new ArrayDataProvider
			([
				'allModels' => $assets,
				'pagination' => [
					'pageSize' => $model->pagesize,
				],
			]);
			//set pages
			$pages = new Pagination($response['pages']);
			
			// Sorting Client table
			$dataProvider->sort = [
				'attributes' => [
					'ClientAccountID' => [
						'asc' => ['ClientAccountID' => SORT_ASC],
						'desc' => ['ClientAccountID' => SORT_DESC]
					],
					'ClientName' => [
						'asc' => ['ClientName' => SORT_ASC],
						'desc' => ['ClientName' => SORT_DESC]
					],
					'ClientCity' => [
						'asc' => ['ClientCity' => SORT_ASC],
						'desc' => ['ClientCity' => SORT_DESC]
					],
					'ClientState' => [
						'asc' => ['ClientState' => SORT_ASC],
						'desc' => ['ClientState' => SORT_DESC]
					]
				]
			];

			return $this->render('index', [
				'dataProvider' => $dataProvider,
				'searchModel' => $searchModel,
				'model' => $model,
				'pages' => $pages
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
     * Displays a single client model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
		try{
			//guest redirect
			if (Yii::$app->user->isGuest)
			{
				return $this->redirect(['/login']);
			}
			
			//Check if user has permissions
			self::requirePermission("clientView");
			
			$url = 'client%2Fview&' . http_build_query([
				'joinNames' => 'true',
				'id' => $id,
			]);
			$response = Parent::executeGetRequest($url, Constants::API_VERSION_2); // indirect rbac

			return $this -> render('view', ['model' => json_decode($response), true]);
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
     * Creates a new client model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
		try{
			//guest redirect
			if (Yii::$app->user->isGuest)
			{
				return $this->redirect(['/login']);
			}
			
			self::requirePermission("clientCreate");
			$model = new Client();
				  
			//generate array for Active Flag dropdown
			$flag = 
			[
				1 => "Active",
				0 => "Inactive",
			];
			
			//get clients for form dropdown
			//Route is no longer in place may be re added in the future
			// $clientAccountsUrl = "client-accounts%2Fget-client-account-dropdowns";
			// $clientAccountsResponse = Parent::executeGetRequest($clientAccountsUrl);
			// $clientAccounts = json_decode($clientAccountsResponse, true);
			$clientAccounts = ['' => ''];
			
			//get states for form dropdown
			$stateUrl = 'dropdown%2Fget-state-codes-dropdown';
			$stateResponse = Parent::executeGetRequest($stateUrl, Constants::API_VERSION_2);
			$states = json_decode($stateResponse, true);
				  
			if ($model->load(Yii::$app->request->post())) {
				
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
					'ClientComment' => $model->ClientComment
					);

				$json_data = json_encode($data);

				// post url
				$url= 'client%2Fcreate';
				
				$response = Parent::executePostRequest($url, $json_data, Constants::API_VERSION_2);
				$obj = json_decode($response, true);
				if(array_key_exists("ClientID", $obj)) {
					return $this->redirect(['view', 'id' => $obj["ClientID"]]);
				} else {
					return $this->render('create',[
						'model' => $model,
						'flag' => $flag,
						'clientAccounts' => $clientAccounts,
						'states' => $states,
						'createFailed' => true
					]);
				}
			} else {
				return $this->render('create',[
					'model' => $model,
					'flag' => $flag,
					'clientAccounts' => $clientAccounts,
					'states' => $states,
					'createFailed' => false
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
     * Updates an existing client model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
		try{
			//guest redirect
			if (Yii::$app->user->isGuest)
			{
				return $this->redirect(['/login']);
			}
			self::requirePermission("clientUpdate");
			$getUrl = 'client%2Fview&' . http_build_query([
				'id' => $id,
			]);
			$getResponse = json_decode(Parent::executeGetRequest($getUrl, Constants::API_VERSION_2), true);

			$model = new Client();
			$model->attributes = $getResponse;
				  
			//generate array for Active Flag dropdown
			$flag = 
			[
				1 => "Active",
				0 => "Inactive",
			];
			
			//get clients for form dropdown
			//Route is no longer in place may be re added in the future
			// $clientAccountsUrl = "client-accounts%2Fget-client-account-dropdowns";
			// $clientAccountsResponse = Parent::executeGetRequest($clientAccountsUrl);
			// $clientAccounts = json_decode($clientAccountsResponse, true);
			$clientAccounts = ['' => ''];
			
			//get states for form dropdown
			$stateUrl = 'dropdown%2Fget-state-codes-dropdown';
			$stateResponse = Parent::executeGetRequest($stateUrl, Constants::API_VERSION_2);
			$states = json_decode($stateResponse, true);
				  
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
				
				$putUrl = 'client%2Fupdate&' . http_build_query([
					'id' => $id,
				]);
				$putResponse = Parent::executePutRequest($putUrl, $json_data, Constants::API_VERSION_2);
				
				$obj = json_decode($putResponse, true);
				
				return $this->redirect(['view', 'id' => $model["ClientID"]]);
			} else {
				return $this->render('update', [
					'model' => $model,
					'flag' => $flag,
					'clientAccounts' => $clientAccounts,
					'states' => $states,
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
     * Deletes an existing client model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDeactivate($id)
    {
		try{
			//guest redirect
			if (Yii::$app->user->isGuest)
			{
				return $this->redirect(['/login']);
			}
			$url = 'client%2Fdeactivate&' . http_build_query([
				'id' => $id,
			]);
			Parent::executePostRequest($url, ""); //indirect RBAC
			$this->redirect(['client/index']);
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
}
