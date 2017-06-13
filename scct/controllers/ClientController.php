<?php

namespace app\controllers;

use app\models\Client;
use Yii;
use yii\data\Pagination;
use yii\data\ArrayDataProvider;

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
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['/login']);
        }
        $model = new \yii\base\DynamicModel([
            'filter', 'pagesize'
        ]);
        $model->addRule('filter', 'string', ['max' => 32])
            ->addRule('pagesize', 'string', ['max' => 32]);//get page number and records per page

        $filterParam = "";
        $userPageSizeParams = 50;
        // check if type was get, if so, get value from $model
        if ($model->load(Yii::$app->request->get())) {
            //$userPageSizeParams = $model->pagesize;
            $filterParam = $model->filter;
            $userPageSizeParams = $model->pagesize;
        }
        //Determine which page to show
        if (isset($_GET['userPage'])) {
            $page = $_GET['userPage'];
        } else {
            $page = 1;
        }

        $nameFilterParam = Yii::$app->request->getQueryParam('filterclientname', '');
        $cityFilterParam = Yii::$app->request->getQueryParam('filtercityname', '');
        $stateFilterParam = Yii::$app->request->getQueryParam('filterstatename', '');

        $url = "client%2Fget-all&filter=" . urlencode($filterParam) . "&listPerPage=" . urlencode($userPageSizeParams)
            . "&page=" . urlencode($page) . "&filterclientname=" . urlencode($nameFilterParam)
            . "&filtercityname=" . urlencode($cityFilterParam) . "&filterstatename=" . urlencode($stateFilterParam);
        $response = Parent::executeGetRequest($url, BaseController::API_VERSION_2); // RBAC permissions checked indirectly via this call
        $response = json_decode($response, true);
        $assets = $response['assets'];
        $searchModel = [
            'ClientName' => $nameFilterParam,
            'City' => $cityFilterParam,
            'State' => $stateFilterParam
        ];
        //Passing data to the dataProvider and formating it in an associative array
        $dataProvider = new ArrayDataProvider
        ([
            'allModels' => $assets,
            'pagination' => [
                'pageSize' => 100,
            ],
        ]);

        $searchModel = [
            'ClientName' => Yii::$app->request->getQueryParam('filterclientname', ''),
            'ClientCity' => Yii::$app->request->getQueryParam('filtercity', ''),
            'ClientState' => Yii::$app->request->getQueryParam('filterstate', ''),
        ];
        //Passing data to the dataProvider and formatting it in an associative array
        $dataProvider = new ArrayDataProvider
        ([
            'allModels' => $assets,
            'pagination' => [
                'pageSize' => 100,
            ],
        ]);

        //set pages
        $pages = new Pagination($response['pages']);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'model' => $model,
            'pages' => $pages,
            'filter' => $filterParam,
            'userPageSizeParams' => $userPageSizeParams
        ]);

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
			return $this->redirect(['/login']);
		}
		$url = 'client%2Fview&joinNames=true&id='.$id;
		$response = Parent::executeGetRequest($url); // indirect rbac

		return $this -> render('view', ['model' => json_decode($response), true]);
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
		$clientAccountsUrl = "client-accounts%2Fget-client-account-dropdowns";
		$clientAccountsResponse = Parent::executeGetRequest($clientAccountsUrl);
		$clientAccounts = json_decode($clientAccountsResponse, true);
		
		//get states for form dropdown
		$stateUrl = "state-code%2Fget-code-dropdowns";
		$stateResponse = Parent::executeGetRequest($stateUrl);
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
			$url= "client%2Fcreate";
			
			$response = Parent::executePostRequest($url, $json_data);
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
			return $this->redirect(['/login']);
		}
		self::requirePermission("clientUpdate");
		$getUrl = 'client%2Fview&id='.$id;
		$getResponse = json_decode(Parent::executeGetRequest($getUrl), true);

		$model = new Client();
		$model->attributes = $getResponse;
			  
		//generate array for Active Flag dropdown
		$flag = 
		[
			1 => "Active",
			0 => "Inactive",
		];
		
		//get clients for form dropdown
		$clientAccountsUrl = "client-accounts%2Fget-client-account-dropdowns";
		$clientAccountsResponse = Parent::executeGetRequest($clientAccountsUrl);
		$clientAccounts = json_decode($clientAccountsResponse, true);
		
		//get states for form dropdown
		$stateUrl = "state-code%2Fget-code-dropdowns";
		$stateResponse = Parent::executeGetRequest($stateUrl);
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
			
			$putUrl = 'client%2Fupdate&id='.$id;
			$putResponse = Parent::executePutRequest($putUrl, $json_data);
			
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
    }

    /**
     * Deletes an existing client model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDeactivate($id)
    {
		//guest redirect
		if (Yii::$app->user->isGuest)
		{
			return $this->redirect(['/login']);
		}
		$url = 'client%2Fdeactivate&id='.$id;
		Parent::executePostRequest($url, ""); //indirect RBAC
		$this->redirect(['client/index']);
    }
}
