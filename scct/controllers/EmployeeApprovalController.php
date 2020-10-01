<?php

namespace app\controllers;

use Yii;
use app\controllers\BaseController;
use yii\data\Pagination;
use yii\base\Exception;
use yii\filters\VerbFilter;
use yii\data\ArrayDataProvider;
use linslin\yii2\curl;
use yii\web\Request;
use \DateTime;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\ServerErrorHttpException;
use yii\web\UnauthorizedHttpException;
use yii\base\Model;
use yii\web\Response;
use app\constants\Constants;
use app\models\EmployeeDetailTime;

class EmployeeApprovalController extends BaseCardController
{
    /**
     * Lists a summary of user data.
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws ServerErrorHttpException
     * @throws \yii\web\HttpException
     */
    public function actionIndex($projectID = null, $projectFilterString = null,  $activeWeek = null, $dateRange = null){
		//TODO clean up extra code
		// try {
			//guest redirect
			if (Yii::$app->user->isGuest)
			{
				return $this->redirect(['/login']);
			}

			//Check if user has permissions
			self::requirePermission("viewEmployeeApproval");

			//if request is not coming from report-summary reset session variables. 
			$referrer = Yii::$app->request->referrer;
			if(!strpos($referrer,'employee-approval')){
				unset(Yii::$app->session['employeeApprovalFormData']);
				unset(Yii::$app->session['employeeApprovalSort']);
			}

			//check user role
            $isProjectManager = Yii::$app->session['UserAppRoleType'] == 'ProjectManager';

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
                'clientID',
                'projectID',
				'employeeID'
            ]);
            $model->addRule('pageSize', 'string', ['max' => 32]);//get page number and records per page
			$model->addRule('page', 'integer');
            $model->addRule('filter', 'string', ['max' => 100]); // Don't want overflow but we can have a relatively high max
            $model->addRule('dateRangePicker', 'string', ['max' => 32]);
            $model->addRule('dateRangeValue', 'string', ['max' => 100]); 
            $model->addRule('clientID', 'integer');
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
			
			//"sort":"-RowLabels"
            //get sort data
            if (isset($_GET['sort'])){
                $sort = $_GET['sort'];
                //parse sort data
                $sortField = str_replace('-', '', $sort, $sortCount);
                $sortOrder = $sortCount > 0 ? 'DESC' : 'ASC';
				Yii::$app->session['employeeApprovalSort'] = [
					'sortField' => $sortField,
					'sortOrder' => $sortOrder
				];
            } else {
				if(Yii::$app->session['employeeApprovalSort']){
					$sortField = Yii::$app->session['employeeApprovalSort']['sortField'];
					$sortOrder = Yii::$app->session['employeeApprovalSort']['sortOrder'];
				}else{
					//default sort values
					$sortField = 'RowLabels';
					$sortOrder = 'ASC';
				}
            }

            // check if type was post, if so, get value from $model
            if ($model->load(Yii::$app->request->queryParams)){
				Yii::$app->session['employeeApprovalFormData'] = $model;
			}else{
				//set defaults to session data if avaliable
				if(Yii::$app->session['employeeApprovalFormData']){
					$model = Yii::$app->session['employeeApprovalFormData'];
				}else{
					//set default values
					$model->pageSize = 50;
					$model->page = 1;
					$model->employeeID = '';
					$model->dateRangePicker	= null;
					$model->dateRangeValue = $currentWeek;
					//set filters if data passed from home screen
					$model->filter = $projectFilterString != null ? urldecode($projectFilterString): '';
					$model->clientID = '';
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
				'clientID' => $model->clientID,
				'projectID' => $model->projectID,
				'employeeID' => $model->employeeID,
				'sortField' => $sortField,
				'sortOrder' => $sortOrder,
			]);
			// set url

			$url = 'employee-approval&' . $httpQuery;

			//execute request
			$response = Parent::executeGetRequest($url, Constants::API_VERSION_3);
            $response = json_decode($response, true);
            $userData = $response['UserData'];
            $projData = $response['ProjData'];
            $statusData = $response['StatusData'];
			
			//get date values from user data for dynamic headers
			$dateHeaders = [];
			if($userData != null){
				foreach ($userData[0] as $key => $value){
					if(strpos($key, '/') !== false){
						$dateHeaders[] = $key;
					}
				}
			}
			
			//extract format indicators from response data
			$projectDropDown = $response['ProjectDropDown'];
			
			//check if user can approve cards
			$canApprove = self::can('timeCardApproveCards') && self::can('mileageCardApprove');
			
            // passing user data into dataProvider
            $userDataProvider = new ArrayDataProvider([
				'allModels' => $userData,
				'pagination' => false,
				'key' => function ($userData) {
					return array(
						'UserID' => $userData['UserID'],
						'UserName' => $userData['RowLabels']
					);
				}
			]);
			
			// passing project data into dataProvider
			$projDataProvider = new ArrayDataProvider([
				'allModels' => $projData,
				'pagination' => false
			]);
			
			// passing status data into dataProvider
			$statusDataProvider = new ArrayDataProvider([
				'allModels' => $statusData,
				'pagination' => false
			]);
			
			//sorting with dynamic headers may prove to be problematic, currently weekday headers are dates
			// Sorting UserData table
			// $userDataProvider->sort = [
				// 'defaultOrder' => [$sortField => ($sortOrder == 'ASC') ? SORT_ASC : SORT_DESC],
				// 'attributes' => [
				// ]
			// ];

			// set pages to dispatch table
			// $pages = new Pagination($response['pages']);

			$dataArray = [
				'userDataProvider' => $userDataProvider,
				'dateHeaders' => $dateHeaders, //list column header dates for the days of the week
				'projDataProvider' => $projDataProvider,
				'statusDataProvider' => $statusDataProvider,
				'dateRangeDD' => $dateRangeDD,
				'model' => $model,
				'projectDropDown' => $projectDropDown,
				'canApprove' => $canApprove,
				'isProjectManager' => $isProjectManager,
				'startDate' => $startDate,
				'endDate' =>  $endDate
			];
			//calling index page to pass dataProvider.
			if(Yii::$app->request->isAjax) {
				return $this->renderAjax('index', $dataArray);
			}else{
				return $this->render('index', $dataArray);
			}
        // } catch (UnauthorizedHttpException $e){
            // Yii::$app->response->redirect(['login/index']);
        // } catch(ForbiddenHttpException $e) {
            // throw $e;
        // } catch(ErrorException $e) {
            // throw new \yii\web\HttpException(400);
        // } catch(Exception $e) {
            // throw new ServerErrorHttpException();
        // }
    }
	
	/**
     * Lists a summary of user data.
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws ServerErrorHttpException
     * @throws \yii\web\HttpException
     */
    public function actionEmployeeDetail($userID, $date){
		// try {
			//guest redirect
			if (Yii::$app->user->isGuest)
			{
				return $this->redirect(['/login']);
			}

			//Check if user has permissions
			self::requirePermission("employeeApprovalDetail");


			//build api url path
			$url = 'employee-approval%2Femployee-detail&' . http_build_query([
				'userID' => $userID,
				'date' => $date,
			]);
			
			//execute request
			$response = Parent::executeGetRequest($url, Constants::API_VERSION_3);
            $response = json_decode($response, true);
            $projectData = $response['ProjectData'];
            $breakdownData = $response['BreakdownData'];
            $totalData = $response['Totals'];

            // passing user data into dataProvider
            $projectDataProvider = new ArrayDataProvider([
				'allModels' => $projectData,
				'pagination' => false
			]);
			
			// passing project data into dataProvider
			$breakdownDataProvider = new ArrayDataProvider([
				'allModels' => $breakdownData,
				'pagination' => false,
				'key' => function ($breakdownData) {
					return array(
						'RowID' => $breakdownData['RowID']
					);
				}
			]);
			
			$dataArray = [
				'projectDataProvider' => $projectDataProvider,
				'breakdownDataProvider' => $breakdownDataProvider,
				'totalData' => $totalData,
				'userID'=> $userID,
			];
			//calling index page to pass dataProvider.
			if(Yii::$app->request->isAjax) {
				return $this->renderAjax('employee-detail', $dataArray);
			}else{
				return $this->render('employee-detail', $dataArray);
			}
        // } catch (UnauthorizedHttpException $e){
            // Yii::$app->response->redirect(['login/index']);
        // } catch(ForbiddenHttpException $e) {
            // throw $e;
        // } catch(ErrorException $e) {
            // throw new \yii\web\HttpException(400);
        // } catch(Exception $e) {
            // throw new ServerErrorHttpException();
        // }
    }
	
	/**
     * Populates the Employee Detail Edit Modal
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws ServerErrorHttpException
     * @throws \yii\web\HttpException
     */
    public function actionEmployeeDetailModal($userID){
		// try {
			//guest redirect
			if (Yii::$app->user->isGuest)
			{
				return $this->redirect(['/login']);
			}

			//Check if user has permissionse
			self::requirePermission("employeeApprovalDetailEdit");
			
			Yii::trace(json_encode($_POST));
			
			//if GET pull data params to populate form
			if (isset($_POST)){
				$data = $_POST['Current'];
				$prevData = $_POST['Prev'];
				$nextData = $_POST['Next'];
				$model = new EmployeeDetailTime;
				$prevModel = new EmployeeDetailTime;
				$nextModel = new EmployeeDetailTime;
				$projectDropDown = [];
				$taskDropDown = [];
				
				$model->attributes = $data;
				$prevModel->attributes = $prevData;
				$nextModel->attributes = $nextData;
				
				yii::trace('current ' . json_encode($model->attributes));
				yii::trace('prev ' . json_encode($prevModel->attributes));
				yii::trace('next ' . json_encode($nextModel->attributes));
				
				$getProjectDropdownURL = 'project%2Fget-project-dropdowns&' . http_build_query([
					'userID' => $userID,
				]);
				$getProjectDropdownResponse = Parent::executeGetRequest($getProjectDropdownURL, Constants::API_VERSION_3);
				$projectDropDown = json_decode($getProjectDropdownResponse, true);
				
				$getAllTaskUrl = 'task%2Fget-by-project&' . http_build_query([
					'projectID' => $model->ProjectID,
				]);
				$getAllTaskResponse = Parent::executeGetRequest($getAllTaskUrl, Constants::API_VERSION_3);
				$allTask = json_decode($getAllTaskResponse, true);
				$taskDropDown = [];				
				foreach($allTask['assets'] as $task) {
					$taskDropDown['Task ' . $task['TaskName']] = $task['TaskName'];
				}
				
				$dataArray = [
					'model' => $model,
					'prevModel' => $prevModel,
					'nextModel' => $nextModel,
					'projectDropDown' => $projectDropDown,
					'taskDropDown' => $taskDropDown,
				];
			}
			
			//calling index page to pass dataProvider.
			if(Yii::$app->request->isAjax) {
				return $this->renderAjax('_employee-detail-edit-modal', $dataArray);
			}else{
				return $this->render('_employee-detail-edit-modal', $dataArray);
			}
        // } catch (UnauthorizedHttpException $e){
            // Yii::$app->response->redirect(['login/index']);
        // } catch(ForbiddenHttpException $e) {
            // throw $e;
        // } catch(ErrorException $e) {
            // throw new \yii\web\HttpException(400);
        // } catch(Exception $e) {
            // throw new ServerErrorHttpException();
        // }
    }
        
	public function actionApproveTimecards(){
		try{
			if (Yii::$app->request->isAjax) {
				//get requesting controller type
				$requestType = self::getRequestType();
				$data = Yii::$app->request->post();					
				// loop the data array to get all id's.	
				$cardIDArray = "";
				foreach($data['userid'] as $keyitem){
					$cardIDArray .= $keyitem['UserID'] . ",";
				}
				$cardIDArray = substr_replace($cardIDArray ,"", -1);
				$startDate = $data['startDate'];
				$endDate = $data['endDate'];
				$data = array(
					'cardIDArray' => $cardIDArray,
					'startDate' =>  $startDate,
					'endDate' =>  $endDate
				);
				$json_data = json_encode($data);
				Yii::trace("Data params json: " . $json_data);
				// post url
				$putUrl = $requestType.'%2Fapprove-timecards';
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
}