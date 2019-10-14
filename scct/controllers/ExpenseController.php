<?php
namespace app\controllers;

use Yii;
use app\controllers\BaseController;
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
    public function actionIndex($projectID = null, $projectFilterString = null,  $activeWeek = null, $dateRange = null)
    {
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
				yii::trace('MODEL DATA: ' . json_encode($model));
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
				yii::trace('DateRange ' . $model->dateRangeValue);
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
			
			//get submit button status
			$isSubmitReady = true;//self::getSubmitButtonStatus($model->projectID, $projectDropDown, $startDate, $endDate, $isAccountant);

			//set submit button status
			if($isAccountant)
				$accountingSubmitReady = $isSubmitReady;
			else
				$pmSubmitReady = $isSubmitReady;
			
			//check if user can approve cards
			$canApprove = true;//self::can('expenseApprove');
			
            // passing decode data into dataProvider
            $dataProvider = new ArrayDataProvider
			([
				'allModels' => $assets,
				'pagination' => false,
				'key' => function ($assets) {
					return array(
						'ProjectID' => $assets['ProjectID'],
						'CreatedDate' => $assets['CreatedDate'],
					);
				}
			]);
			
			if($isAccountant) {
				// Sorting TimeCard table
				$dataProvider->sort = [
					'defaultOrder' => [$sortField => ($sortOrder == 'ASC') ? SORT_ASC : SORT_DESC],
					'attributes' => [
						'ProjectName',
						'CreatedDate',
						'IsApproved',
						'IsSubmitted',
					]
				];
			} else {
				// Sorting TimeCard table
				$dataProvider->sort = [
					'defaultOrder' => [$sortField => ($sortOrder == 'ASC') ? SORT_ASC : SORT_DESC],
					'attributes' => [
						'UserName',
						'ProjectName',
						'ChargeAccount',
						'CreatedDate',
						'IsSubmitted',
						'Quantity',
						'IsApproved',
					]
				];

				//set expense ID as id
				$dataProvider->key = 'ID';
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
				'pmSubmitReady' => $pmSubmitReady,
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