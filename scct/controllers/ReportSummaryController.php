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

/**
 * TimeCardController implements the CRUD actions for TimeCard model.
 */
class ReportSummaryController extends BaseCardController
{
    /**
     * Lists a summary of user data.
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws ServerErrorHttpException
     * @throws \yii\web\HttpException
     */
    public function actionIndex($projectID = null, $projectFilterString = null,  $activeWeek = null, $dateRange = null){
		// try {
			//guest redirect
			if (Yii::$app->user->isGuest)
			{
				return $this->redirect(['/login']);
			}

			//Check if user has permissions
			self::requirePermission("viewReportSummary");

			//if request is not coming from report-summary reset session variables. 
			$referrer = Yii::$app->request->referrer;
			if(!strpos($referrer,'report-summary')){
				unset(Yii::$app->session['reportSummaryFormData']);
				unset(Yii::$app->session['reportSummarySort']);
			}

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
				Yii::$app->session['reportSummarySort'] = [
					'sortField' => $sortField,
					'sortOrder' => $sortOrder
				];
            } else {
				if(Yii::$app->session['reportSummarySort']){
					$sortField = Yii::$app->session['reportSummarySort']['sortField'];
					$sortOrder = Yii::$app->session['reportSummarySort']['sortOrder'];
				}else{
					//default sort values
					$sortField = 'RowLabels';
					$sortOrder = 'ASC';
				}
            }

            // check if type was post, if so, get value from $model
            if ($model->load(Yii::$app->request->queryParams)){
				Yii::$app->session['reportSummaryFormData'] = $model;
			}else{
				//set defaults to session data if avaliable
				if(Yii::$app->session['reportSummaryFormData']){
					$model = Yii::$app->session['reportSummaryFormData'];
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
			$url = 'base-card%2Freport-summary&' . $httpQuery;

			//execute request
			$response = Parent::executeGetRequest($url, Constants::API_VERSION_3);
            $response = json_decode($response, true);
            $userData = $response['UserData'];
            $projData = $response['ProjData'];
            $statusData = $response['StatusData'];
			
			//get date values from user data for dynamic headers
			if($userData != null){
				$dateHeaders = [];
				foreach ($userData[0] as $key => $value){
					if(strpos($key, '/') !== false){
						$dateHeaders[] = $key;
					}
				}
			}
			
			//extract format indicators from response data
			//not sure how dropdowns and different view logic will be handled
            // $unapprovedTimeCardInProject = array_key_exists('unapprovedTimeCardInProject', $response) ? $response['unapprovedTimeCardInProject'] : false;
            // $unapprovedTimeCardVisible = array_key_exists('unapprovedTimeCardVisible', $response) ? $response['unapprovedTimeCardVisible'] : false;
            // $showFilter = $response['showProjectDropDown'];
            // $projectWasSubmitted = $response['projectSubmitted'];
			// $clientDropDown = $response['clientDropDown'];
			// $projectDropDown = $response['projectDropDown'];
			// $employeeDropDown = $response['employeeDropDown'];
			
			//check if user can approve cards
			$canApprove = self::can('timeCardApproveCards') && self::can('mileageCardApprove');
			
            // passing user data into dataProvider
            $userDataProvider = new ArrayDataProvider([
				'allModels' => $userData,
				'pagination' => false,
				'key' => function ($userData) {
					return array(
						'UserID' => $userData['UserID']
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
				// 'pages' => $pages,
				// 'clientDropDown' => $clientDropDown,
				// 'projectDropDown' => $projectDropDown,
				// 'employeeDropDown' => $employeeDropDown,
				// 'showFilter' => $showFilter,
				'canApprove' => $canApprove
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
    public function actionEmployeeDetail($userID, $startDate){
		// try {
			//guest redirect
			if (Yii::$app->user->isGuest)
			{
				return $this->redirect(['/login']);
			}

			//Check if user has permissions
			self::requirePermission("reportSummaryEmployeeDetail");


			//build api url path
			$url = 'base-card%2Femployee-detail&' . http_build_query([
				'userID' => $userID,
				'startDate' => $startDate,
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
}