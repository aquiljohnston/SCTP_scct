<?php
namespace app\controllers;

use Yii;
use app\controllers\BaseController;
use app\models\Expense;
use yii\base\Exception;
use yii\data\Pagination;
use yii\data\ArrayDataProvider;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\ServerErrorHttpException;
use yii\web\UnauthorizedHttpException;
use yii\web\Request;
use yii\web\Response;
use app\constants\Constants;

class ExpenseController extends BaseCardController {
    /**
     * Default controller action
     * @return the index view for controller
     * @throws ForbiddenHttpException
     * @throws ServerErrorHttpException
     * @throws \yii\web\HttpException
     */
    public function actionIndex($projectID = null, $projectFilterString = null,  $activeWeek = null, $dateRange = null){
		try {
			//guest redirect
			if (Yii::$app->user->isGuest)
			{
				return $this->redirect(['/login']);
			}

			//Check if user has permissions
			self::requirePermission("viewExpenseMgmt");

			//if request is not coming from expense reset session variables. 
			$referrer = Yii::$app->request->referrer;
			if(!strpos($referrer,'expense')){
				unset(Yii::$app->session['expenseFormData']);
				unset(Yii::$app->session['expenseSort']);
			}
			
            //check user role
            $isAccountant = Yii::$app->session['UserAppRoleType'] == 'Accountant';
			$accountingSubmitReady = FALSE;
			$pmSubmitReady = FALSE;
			$isProjectManager = true;//self::can("expenseSubmit");
            // Store start/end date data
            $dateData = [];
            $startDate = null;
            $endDate = null;

            $model = new \yii\base\DynamicModel([
                'pageSize',
				'page',
                'filter',
                'dateRangeValue',
                'dateRangePicker',
                'projectID',
				'employeeID'
            ]);
            $model->addRule('pageSize', 'string', ['max' => 32]);//get page number and records per page
			$model->addRule('page', 'integer');
            $model->addRule('filter', 'string', ['max' => 100]); // Don't want overflow but we can have a relatively high max
            $model->addRule('dateRangePicker', 'string', ['max' => 32]);
            $model->addRule('dateRangeValue', 'string', ['max' => 100]); 
            $model->addRule('projectID', 'integer');
            $model->addRule('employeeID', 'integer');

            //get current and prior weeks date range
            $today = BaseController::getDate();
            $priorWeek = BaseController::getWeekBeginEnd("$today -1 week");
            $currentWeek = BaseController::getWeekBeginEnd($today);
            $other = "other";

            //create default prior/current week values
            $dateRangeDD = [
                $priorWeek => 'Prior Week',
                $currentWeek => 'Current Week',
                $other => 'Other'
            ];
			
			//"sort":"-UserName"
            //get sort data
            if (isset($_GET['sort'])){
                $sort = $_GET['sort'];
                //parse sort data
                $sortField = str_replace('-', '', $sort, $sortCount);
                $sortOrder = $sortCount > 0 ? 'DESC' : 'ASC';
				Yii::$app->session['expenseSort'] = [
					'sortField' => $sortField,
					'sortOrder' => $sortOrder
				];
            } else {
				if(Yii::$app->session['expenseSort']){
					$sortField = Yii::$app->session['expenseSort']['sortField'];
					$sortOrder = Yii::$app->session['expenseSort']['sortOrder'];
				}else{
					//default sort values
					$sortField = ($isAccountant) ? 'ProjectName' : 'UserName';
					$sortOrder = 'ASC';
				}
            }

            // check if type was post, if so, get value from $model
            if ($model->load(Yii::$app->request->queryParams)){
				Yii::$app->session['expenseFormData'] = $model;
			}else{
				//set defaults to session data if avaliable
				if(Yii::$app->session['expenseFormData'])
				{
					$model = Yii::$app->session['expenseFormData'];
				}else{
					//set default values
					$model->pageSize = 50;
					$model->page = 1;
					$model->employeeID = '';
					$model->dateRangePicker	= null;
					$model->dateRangeValue = $currentWeek;
					//set filters if data passed from home screen
					$model->filter = $projectFilterString != null ? urldecode($projectFilterString): '';
					$model->projectID = $projectID != null ? $projectID : '';
					if($activeWeek == Constants::PRIOR_WEEK){
						$model->dateRangeValue = $priorWeek;
					}elseif($activeWeek == Constants::CURRENT_WEEK){ //not necessary since default is current, but in place for clarity
						$model->dateRangeValue = $currentWeek;
					}elseif($dateRange != null){
						$model->dateRangePicker	= $dateRange;
						$model->dateRangeValue = 'other';
					}
				}
            }
			
			//get start/end date based on dateRangeValue
            if ($model->dateRangeValue == 'other') {
                if ($model->dateRangePicker == null){
                    $endDate = $startDate = date('Y-m-d');
                }else {
                    $dateData 	= SELF::dateRangeProcessor($model->dateRangePicker);
                    $startDate 	= $dateData[0];
                    $endDate 	= $dateData[1];
                }
            }else{
                $dateRangeArray = BaseController::splitDateRange($model->dateRangeValue);
                $startDate = $dateRangeArray['startDate'];
                $endDate =  $dateRangeArray['endDate'];
            }

			//url encode filter
			$encodedFilter = urlencode($model->filter);
			//build params
			$httpQuery = http_build_query([
				'startDate' => $startDate,
				'endDate' => $endDate,
				'listPerPage' => $model->pageSize,
				'page' => $model->page,
				'filter' => $encodedFilter,
				'projectID' => $model->projectID,
				'employeeID' => $model->employeeID,
				'sortField' => $sortField,
				'sortOrder' => $sortOrder,
			]);
			// set url
			if($isAccountant)
				$url = 'expense%2Fget-accountant-view&' . $httpQuery;
			else
				$url = 'expense%2Fget&' . $httpQuery;

			//execute request
			$response = Parent::executeGetRequest($url, Constants::API_VERSION_3);
            $response = json_decode($response, true);
            $assets = $response['assets'];
			
			//extract format indicators from response data
            $unapprovedExpenseInProject = array_key_exists('unapprovedExpenseInProject', $response) ? $response['unapprovedExpenseInProject'] : false;
            $unapprovedExpenseVisible = array_key_exists('unapprovedExpenseVisible', $response) ? $response['unapprovedExpenseVisible'] : false;
            $showFilter = $response['showProjectDropDown'];
            $projectWasSubmitted = $response['projectSubmitted'];
			$projectDropDown = $response['projectDropDown'];
			$isAccountant ? $employeeDropDown = [] : $employeeDropDown = $response['employeeDropDown'];

			//set submit button status
			if($isAccountant)
				$accountingSubmitReady = self::getSubmitButtonStatus($model->projectID, $projectDropDown, $startDate, $endDate, $isAccountant);
			
			//check if user can approve cards
			$canApprove = self::can('expenseApprove');

			if($isAccountant) {
				// passing decode data into dataProvider
				$dataProvider = new ArrayDataProvider
				([
					'allModels' => $assets,
					'pagination' => false,
					'key' => function ($assets) {
						return array(
							'ProjectID' => $assets['ProjectID'],
							'StartDate' => $assets['StartDate'],
							'EndDate' => $assets['EndDate'],
						);
					}
				]);
				
				// Sorting TimeCard table
				$dataProvider->sort = [
					'defaultOrder' => [$sortField => ($sortOrder == 'ASC') ? SORT_ASC : SORT_DESC],
					'attributes' => [
						'ProjectName',
						'IsSubmitted',
					]
				];
			} else {
				// passing decode data into dataProvider
				$dataProvider = new ArrayDataProvider
				([
					'allModels' => $assets,
					'pagination' => false,
				]);
				// Sorting TimeCard table
				$dataProvider->sort = [
					'defaultOrder' => [$sortField => ($sortOrder == 'ASC') ? SORT_ASC : SORT_DESC],
					'attributes' => [
						'UserName',
						'ProjectName',
						'ChargeAccount',
						'StartDate',
						'EndDate',
						'IsSubmitted',
						'Quantity',
						'IsApproved',
					]
				];
				
				//set expense ID as id
                $dataProvider->key = 'ExpenseID';
			}

            // set pages to dispatch table
            $pages = new Pagination($response['pages']);

			$dataArray = [
				'dataProvider' => $dataProvider,
				'dateRangeDD' => $dateRangeDD,
				'model' => $model,
				'pages' => $pages,
				'projectDropDown' => $projectDropDown,
				'employeeDropDown' => $employeeDropDown,
				'showFilter' => $showFilter,
				'unapprovedExpenseInProject' => $unapprovedExpenseInProject,
				'unapprovedExpenseVisible' => $unapprovedExpenseVisible,
				'accountingSubmitReady' => $accountingSubmitReady,
				'projectSubmitted' => $projectWasSubmitted,
				'isProjectManager' => $isProjectManager,
				'isAccountant' => $isAccountant,
				'canApprove' => $canApprove
			];
			//calling index page to pass dataProvider.
			if(Yii::$app->request->isAjax) {
				return $this->renderAjax('index', $dataArray);
			}else{
				return $this->render('index', $dataArray);
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
	
	public function actionViewAccountantDetail(){
		try{
			// Verify logged in
			if (Yii::$app->user->isGuest) {
				return $this->redirect(['/login']);
			}

			// get the key to generate section table
			if (isset($_POST['expandRowKey']))
			{
				$projectID = $_POST['expandRowKey']['ProjectID'];
				$startDate = $_POST['expandRowKey']['StartDate'];
				$endDate = $_POST['expandRowKey']['EndDate'];
			}else{
				$projectID = '';
				$startDate = '';
				$endDate = '';
			}
			
			$queryParams = [
				'projectID' => $projectID,
				'startDate' => $startDate,
				'endDate' => $endDate,
			];

			$getUrl = 'expense%2Fget-accountant-details&' . http_build_query([
				'projectID' => $projectID,
				'startDate' => $startDate,
				'endDate' => $endDate,
			]);
			$getResponseData = json_decode(Parent::executeGetRequest($getUrl, Constants::API_VERSION_3), true); //indirect RBAC
			$detailsData = $getResponseData['details'];

			// Put data in data provider
			$accountantDetialsDataProvider = new ArrayDataProvider
			([
				'allModels' => $detailsData,
				'pagination' => false,
				'key' => 'ProjectID',
			]);

			if (Yii::$app->request->isAjax) {
				return $this->renderAjax('_accountant-detail-expand', [
					'accountantDetialsDataProvider' => $accountantDetialsDataProvider,
				]);
			} else {
				return $this->render('_accountant-detail-expand', [
					'accountantDetialsDataProvider' => $accountantDetialsDataProvider,
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
     * Displays all expense entries for a given user, project, and date range.
     * @param int @userID
     * @param int @projectID
     * @param string @startDate
     * @param string @endDate
     * @throws \yii\web\HttpException
     * @return mixed
     */
    public function actionShowEntries($userID, $projectID, $startDate, $endDate){		
		try{
			//guest redirect
			if (Yii::$app->user->isGuest){
				return $this->redirect(['/login']);
			}
			
			//Check if user has permissions
			self::requirePermission('expenseGetEntries');
			
			//build api url path
			$entries_url = 'expense%2Fshow-entries&' . http_build_query([
				'userID' => $userID,
				'projectID' => $projectID,
				'startDate' => $startDate,
				'endDate' => $endDate,
			]);
			$resp = Parent::executeGetRequest($entries_url, Constants::API_VERSION_3); // rbac check
			$expenseData = json_decode($resp, true);
			
			//check if user can approve or deactiave records
            $canApprove = self::can('expenseApprove');
            $canDeactivate = self::can('expenseDeactivate');

			$entries = new ArrayDataProvider([
				'allModels' => $expenseData['entries'],
				'pagination'=> false,
				'key' => function ($entries) {
					return array(
						'ID' => $entries['ID'],
						'IsApproved' => $entries['IsApproved'],
					);
				}
			]);
			$projectName = $expenseData['groupData']['ProjectName'];
			$userName = $expenseData['groupData']['UserName'];
			$isApproved = $expenseData['groupData']['IsApproved'];
			$isSubmitted = $expenseData['groupData']['IsSubmitted'];
			$total = $expenseData['groupData']['Quantity'];
			
			//data to pass to view
			$dataArray = [
				'entries' => $entries,
				'startDate' => $startDate,
				'endDate' => $endDate,
				'projectID' => $projectID,
				'projectName' => $projectName,
				'userID' => $userID,
				'userName' => $userName,
				'canApprove' => $canApprove,
				'canDeactivate' => $canDeactivate,
				'isApproved' => $isApproved,
				'isSubmitted' => $isSubmitted,
				'total' => $total,
			];
			
			if (Yii::$app->request->isAjax) {
				return $this->renderAjax('show-entries', $dataArray);
			} else {
				return $this->render('show-entries', $dataArray);
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
     * Approve Multiple existing Expense Records.
     * @return mixed
     * @throws \yii\web\BadRequestHttpException
     * @throws \yii\web\HttpException
     * @internal param string $id
     *
     */
	public function actionApproveMultiple(){
		try{
			if (Yii::$app->request->isAjax) {
				$data = Yii::$app->request->post();	

				//loop the data array to get all id's.	
				foreach($data['entries'] as $entry){
					if($entry['IsApproved'] == 0)
						$expenseArray[] = $entry['ID'];
				}
				
				$data = array('expenseArray' => $expenseArray);
				$json_data = json_encode($data);
				
				//post url
				$putUrl = 'expense%2Fapprove';
				$putResponse = Parent::executePutRequest($putUrl, $json_data, Constants::API_VERSION_3); // indirect rbac
				//Handle API response if we want to do more robust error handling
			} else {
			  throw new \yii\web\BadRequestHttpException;
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
     * Deactivate existing Expense Records.
     * @return mixed
     * @throws \yii\web\BadRequestHttpException
     * @throws \yii\web\HttpException
     * @internal param string $id
     *
     */
	public function actionDeactivate(){
		try{
			if (Yii::$app->request->isAjax) {
				$data = Yii::$app->request->post();
				//loop the data array to get all id's.	
				foreach($data['entries'] as $entry){
					$expenseArray[] = $entry['ID'];
				}
				
				$data = array('expenseArray' => $expenseArray);		
				$json_data = json_encode($data);
				
				//post url
				$putUrl = 'expense%2Fdeactivate';
				$putResponse = Parent::executePutRequest($putUrl, $json_data, Constants::API_VERSION_3); // indirect rbac
				//Handle API response if we want to do more robust error handling
			} else {
				throw new \yii\web\BadRequestHttpException;
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
     * Add New Expense Entry.
     * @param $projectID
     * @param $userID
     * @param $startDate
     * @param $endDate
     * @return string
     * @throws \yii\web\HttpException
     */

    public function actionAdd($projectID = null, $userID = null, $startDate = null, $endDate = null, $isEntries = null){
		try{
			//guest redirect
			if (Yii::$app->user->isGuest) {
				return $this->redirect(['/login']);
			}
			
			self::requirePermission('expenseCreate');

			//create model object
			$model = new Expense;
			
			//set default it available
			$model->ProjectID = $projectID != null ? $projectID : '';
			$model->UserID = $userID != null ? $userID : '';
				
			if ($model->load(Yii::$app->request->post())){
				if($model->validate()){
					try{
						//if posting fields send create request
						$json_data = json_encode($model->attributes);
						//post url
						$url = 'expense%2Fcreate';
						$response = Parent::executePostRequest($url, $json_data, Constants::API_VERSION_3);
						//reload underlying page
						$referrer = Yii::$app->request->referrer;
						return $this->redirect($referrer);
					}catch(Exception $e){
						//could implement more robust error message here for user
						return 'false';
					}
				}else{
					return 'false';
				}
			}else{
				//make route call with time card id and date params to get filtered overview data
				$modalDropdownUrl = 'expense%2Fget-modal-dropdown&' . http_build_query([
					'projectID' => $projectID,
				]);
				$modalDropdownResponse = Parent::executeGetRequest($modalDropdownUrl, Constants::API_VERSION_3);
				$modalDropdown = json_decode($modalDropdownResponse, true);

				if($isEntries == true){
					//if on entries screen limit dropdown options to screen values
					$projectDropdown = [$projectID => $modalDropdown['projectDropdown'][$projectID]];
					$employeeDropdown = [$userID => $modalDropdown['employeeDropdown'][$userID]];
				}else{
					$projectDropdown = $modalDropdown['projectDropdown'];
					$employeeDropdown = $modalDropdown['employeeDropdown'];
				}
				$coaDropdown = $modalDropdown['coaDropdown'];
				
				$dataArray = [
					'model' => $model,
					'ProjectDropdown' => $projectDropdown,
					'EmployeeDropdown' => $employeeDropdown,
					'CoaDropdown' => $coaDropdown,
					'ProjectID' => $projectID,
					'UserID' => $userID,
					'StartDate' => $startDate,
					'EndDate' => $endDate,
				];
				
				if (Yii::$app->request->isAjax) {
					return $this->renderAjax('expense_add_modal', $dataArray);
				} else {
					return $this->render('expense_add_modal', $dataArray);
				}
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
	
	//TODO potentially put into base
	/**
     * Process Date Range Data
     * @param $dateRange
     * @return array
     */
    public function dateRangeProcessor($dateRange){
        $data = explode(" ", $dateRange);
        $dateData = [];
        foreach ($data as $item){
            if($item != "-"){\
                array_push($dateData, $item);
            }
        }
        return $dateData;
    }
}