<?php

namespace app\controllers;

use app\models\Activity;
use Yii;
use app\models\TimeCard;
use app\models\TimeCardSearch;
use app\models\TimeEntry;
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
class TimeCardController extends BaseCardController
{
    /**
     * Lists all TimeCard models.
     * @return mixed
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
			self::requirePermission("viewTimeCardMgmt");

			//if request is not coming from time-card reset session variables. 
			$referrer = Yii::$app->request->referrer;
			if(!strpos($referrer,'time-card')){
				unset(Yii::$app->session['timeCardFormData']);
				unset(Yii::$app->session['timeCardSort']);
			}
			
            //check user role
            $isAccountant = Yii::$app->session['UserAppRoleType'] == 'Accountant';
			$accountingSubmitReady = FALSE;
			$pmSubmitReady = FALSE;
			$isProjectManager = self::can("timeCardPmSubmit");
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
			
			//"sort":"-UserFullName"
            //get sort data
            if (isset($_GET['sort'])){
                $sort = $_GET['sort'];
                //parse sort data
                $sortField = str_replace('-', '', $sort, $sortCount);
                $sortOrder = $sortCount > 0 ? 'DESC' : 'ASC';
				Yii::$app->session['timeCardSort'] = [
					'sortField' => $sortField,
					'sortOrder' => $sortOrder
				];
            } else {
				if(Yii::$app->session['timeCardSort']){
					$sortField = Yii::$app->session['timeCardSort']['sortField'];
					$sortOrder = Yii::$app->session['timeCardSort']['sortOrder'];
				}else{
					//default sort values
					$sortField = ($isAccountant) ? 'ProjectName' : 'UserFullName';
					$sortOrder = 'ASC';
				}
            }

            // check if type was post, if so, get value from $model
            if ($model->load(Yii::$app->request->queryParams)){
				Yii::$app->session['timeCardFormData'] = $model;
			}else{
				//set defaults to session data if avaliable
				if(Yii::$app->session['timeCardFormData'])
				{
					$model = Yii::$app->session['timeCardFormData'];
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
				$url = 'time-card%2Fget-accountant-view&' . $httpQuery;
			else
				$url = 'time-card%2Fget-cards&' . $httpQuery;

			//execute request
			$response = Parent::executeGetRequest($url, Constants::API_VERSION_3);
            $response = json_decode($response, true);
            $assets = $response['assets'];
			
			//extract format indicators from response data
            $unapprovedTimeCardInProject = array_key_exists('unapprovedTimeCardInProject', $response) ? $response['unapprovedTimeCardInProject'] : false;
            $unapprovedTimeCardVisible = array_key_exists('unapprovedTimeCardVisible', $response) ? $response['unapprovedTimeCardVisible'] : false;
            $showFilter = $response['showProjectDropDown'];
            $projectWasSubmitted = $response['projectSubmitted'];
			$projectDropDown = $response['projectDropDown'];
			$employeeDropDown = $response['employeeDropDown'];
			
			//get submit button status
			$isSubmitReady = self::getSubmitButtonStatus($model->projectID, $projectDropDown, $startDate, $endDate, $isAccountant);

			//set submit button status
			if($isAccountant)
				$accountingSubmitReady = $isSubmitReady;
			else
				$pmSubmitReady = $isSubmitReady;
			
			//check if user can approve cards
			$canApprove = self::can('timeCardApproveCards');
			
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
			
			if($isAccountant) {
				// Sorting TimeCard table
				$dataProvider->sort = [
					'defaultOrder' => [$sortField => ($sortOrder == 'ASC') ? SORT_ASC : SORT_DESC],
					'attributes' => [
						'ProjectName',
						'ProjectManager',
						'StartDate' ,
						'EndDate',
						'ApprovedBy',
						'OasisSubmitted',
						'MSDynamicsSubmitted',
						'ADPSubmitted'
					]
				];
			} else {
				// Sorting TimeCard table
				$dataProvider->sort = [
					'defaultOrder' => [$sortField => ($sortOrder == 'ASC') ? SORT_ASC : SORT_DESC],
					'attributes' => [
						'UserFullName',
						'ProjectName',
						'TimeCardDates',
						'TimeCardOasisSubmitted',
						'TimeCardQBSubmitted',
						'SumHours',
						'TimeCardApprovedFlag',
						'TimeCardPMApprovedFlag'
					]
				];

				//set timecardid as id
				$dataProvider->key = 'TimeCardID';
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
				'unapprovedTimeCardInProject' => $unapprovedTimeCardInProject,
				'unapprovedTimeCardVisible' => $unapprovedTimeCardVisible,
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

	/**
	 *Displays time card details for expanded project
	 *@throws \yii\web\HttpException
	 *@returns mixed
	 */
	public function actionViewAccountantDetail(){
		try{
			// Verify logged in
			if (Yii::$app->user->isGuest) {
				return $this->redirect(['/login']);
			}

			// get the key to generate section table
			if (isset($_POST['expandRowKey'])){
				$projectID = $_POST['expandRowKey']['ProjectID'];
				if(array_key_exists('EmployeeID', $_POST['expandRowKey']))
					$employeeID = $_POST['expandRowKey']['EmployeeID'];
				$startDate = $_POST['expandRowKey']['StartDate'];
				$endDate = $_POST['expandRowKey']['EndDate'];
				if(array_key_exists('Filter', $_POST['expandRowKey']))
					$filter = $_POST['expandRowKey']['Filter'];
			}else{
				$projectID = '';
				$employeeID = '';
				$startDate = '';
				$endDate = '';
				$filter = '';
			}
			
			$queryParams = [
				'projectID' => $projectID,
				'employeeID' => $employeeID,
				'startDate' => $startDate,
				'endDate' => $endDate,
				'filter' => $filter,
			];

			$getUrl = 'time-card%2Fget-accountant-details&' . http_build_query($queryParams);
			$getResponseData = json_decode(Parent::executeGetRequest($getUrl, Constants::API_VERSION_3), true); //indirect RBAC
			$detailsData = $getResponseData['details'];

			// Put data in data provider
			$accountantDetialsDataProvider = new ArrayDataProvider
			([
				'allModels' => $detailsData,
				'pagination' => false,
				'key' => 'TimeCardProjectID',
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
     * Displays all time entries for a given time card.
     * @param string $id
     * @throws \yii\web\HttpException
     * @return mixed
     */
    public function actionShowEntries($id, $projectName = null, $fName = null, $lName = null, $timeCardProjectID = null, $inOvertime = 'false')
    {		
		try{
			//Defensive Programming - Magic Numbers
			//declare constants to hold constant values	
			define('ENTRIES_ZERO_INDEX',0);
			define('DATES_ZERO_INDEX',0);
			define('DATES_FIRST_INDEX',1);
			define('FROM_DATE_ZERO_INDEX',0);
			define('TO_DATE_FIRST_INDEX',1);

			//guest redirect
			if (Yii::$app->user->isGuest)
			{
				return $this->redirect(['/login']);
			}
			
			//Check if user has permissions
			self::requirePermission("timeCardGetEntries");
			
			//build api url paths
			$entries_url = 'time-card%2Fshow-entries&' . http_build_query([
				'cardID' => $id,
			]);
			$resp = Parent::executeGetRequest($entries_url, Constants::API_VERSION_3); // rbac check
			$cardData = json_decode($resp, true);

			//send entries to function to calculate if given card is in overtime
			$inOvertime = self::calcInOvertime($cardData['show-entries']);
			
			//populate required values if not received from function call
			$timeCardProjectID = $timeCardProjectID != null ? $timeCardProjectID : $cardData['card']['TimeCardProjectID'];
			$projectName = $projectName != null ? $projectName : $cardData['card']['ProjectName'];
			$fName = $fName != null ? $fName : $cardData['card']['UserFirstName'];
			$lName = $lName != null ? $lName : $cardData['card']['UserLastName'];

			//alter from and to dates a bit
			$from = str_replace('-','/',$cardData['show-entries'][ENTRIES_ZERO_INDEX]['Date1']);
			$to = str_replace('-','/',$cardData['show-entries'][ENTRIES_ZERO_INDEX]['Date7']);
			$from = explode('/', $from);

			//holds dates that accompany table header ex. Sunday 10-23
			$SundayDate =  explode('-', $cardData['show-entries'][ENTRIES_ZERO_INDEX]['Date1']);
			$MondayDate =  explode('-', $cardData['show-entries'][ENTRIES_ZERO_INDEX]['Date2']);
			$TuesdayDate =  explode('-', $cardData['show-entries'][ENTRIES_ZERO_INDEX]['Date3']);
			$WednesdayDate =  explode('-', $cardData['show-entries'][ENTRIES_ZERO_INDEX]['Date4']);
			$ThursdayDate =  explode('-', $cardData['show-entries'][ENTRIES_ZERO_INDEX]['Date5']);
			$FridayDate =  explode('-', $cardData['show-entries'][ENTRIES_ZERO_INDEX]['Date6']);
			$SaturdayDate =  explode('-', $cardData['show-entries'][ENTRIES_ZERO_INDEX]['Date7']);

			//check if user can approve cards
			$canApprove = self::can('timeCardApproveCards');

			$allTask = new ArrayDataProvider([
				'allModels' => $cardData['show-entries'],
				'pagination'=> false,
			]);

			return $this -> render('show-entries', [
				'model' => $cardData['card'],
				'task' => $allTask,
				'from' => $from[FROM_DATE_ZERO_INDEX].'/'.$from[TO_DATE_FIRST_INDEX],
				'to' => $to,
				'SundayDate' => $SundayDate[DATES_ZERO_INDEX].'-'.$SundayDate[DATES_FIRST_INDEX],
				'MondayDate' => $MondayDate[DATES_ZERO_INDEX].'-'.$MondayDate[DATES_FIRST_INDEX],
				'TuesdayDate' => $TuesdayDate[DATES_ZERO_INDEX].'-'.$TuesdayDate[DATES_FIRST_INDEX],
				'WednesdayDate' => $WednesdayDate[DATES_ZERO_INDEX].'-'.$WednesdayDate[DATES_FIRST_INDEX],
				'ThursdayDate' => $ThursdayDate[DATES_ZERO_INDEX].'-'.$ThursdayDate[DATES_FIRST_INDEX],
				'FridayDate' => $FridayDate[DATES_ZERO_INDEX].'-'.$FridayDate[DATES_FIRST_INDEX],
				'SaturdayDate' => $SaturdayDate[DATES_ZERO_INDEX].'-'.$SaturdayDate[DATES_FIRST_INDEX],
				'projectName' => $projectName,
				'fName' => $fName,
				'lName' => $lName,
				'SundayDateFull' => date( "Y-m-d", strtotime(str_replace('-', '/', $cardData['show-entries'][ENTRIES_ZERO_INDEX]['Date1']))),
				'MondayDateFull' => date( "Y-m-d", strtotime(str_replace('-', '/', $cardData['show-entries'][ENTRIES_ZERO_INDEX]['Date2']))),
				'TuesdayDateFull' => date( "Y-m-d", strtotime(str_replace('-', '/', $cardData['show-entries'][ENTRIES_ZERO_INDEX]['Date3']))),
				'WednesdayDateFull' => date( "Y-m-d", strtotime(str_replace('-', '/', $cardData['show-entries'][ENTRIES_ZERO_INDEX]['Date4']))),
				'ThursdayDateFull' => date( "Y-m-d", strtotime(str_replace('-', '/', $cardData['show-entries'][ENTRIES_ZERO_INDEX]['Date5']))),
				'FridayDateFull' => date( "Y-m-d", strtotime(str_replace('-', '/', $cardData['show-entries'][ENTRIES_ZERO_INDEX]['Date6']))),
				'SaturdayDateFull' => date( "Y-m-d", strtotime(str_replace('-', '/', $cardData['show-entries'][ENTRIES_ZERO_INDEX]['Date7']))),
				'timeCardProjectID' => $timeCardProjectID,
				'isSubmitted' => $cardData['card']['TimeCardOasisSubmitted']=='Yes' && $cardData['card']['TimeCardMSDynamicsSubmitted']=='Yes',
				'inOvertime' => $inOvertime,
				'canApprove' => $canApprove

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
     * Deactivate an existing TimeEntry.
     * If deactivate is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     * @throws \yii\web\HttpException
     * @internal param string $id
     *
     */
	public function actionDeactivateByTask(){
		try{
			$data = Yii::$app->request->post();	
			$jsonData = json_encode($data);
			
			// post url
			$putUrl = 'time-entry%2Fdeactivate-by-task';
			$putResponse = Parent::executePutRequest($putUrl, $jsonData,Constants::API_VERSION_3); // indirect rbac
			$obj = json_decode($putResponse, true);	
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
	
	public function actionPMReset(){
		try{
			$data = Yii::$app->request->post();	
			$jsonData = json_encode($data);
			
			// post url
			$putUrl = 'time-card%2Fp-m-reset';
			$putResponse = Parent::executePutRequest($putUrl, $jsonData,Constants::API_VERSION_3); // indirect rbac
			$response = json_decode($putResponse, true);
			
			return $response['success'];
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
	
	public function actionAccountantReset(){
		try{
			$data = Yii::$app->request->post();	
			$jsonData = json_encode($data);
			
			// post url
			$putUrl = 'time-card%2Faccountant-reset';
			$putResponse = Parent::executePutRequest($putUrl, $jsonData,Constants::API_VERSION_3); // indirect rbac
			$response = json_decode($putResponse, true);
			
			return $response['success'];
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

	//count time in provided entries($entriesArray) and returns 'true' if in overtime(over 40 hours) and 'false' if not
	private function calcInOvertime($entriesArray){
		define('FIRST_ENTRY_ROW',1);
		define('FIRST_ENTRY_COLUMN',1);
		
		$totalSeconds = 0;
		
		foreach(array_slice($entriesArray, FIRST_ENTRY_ROW) as $rKey => $rVal)
		{			
			foreach(array_slice($rVal, FIRST_ENTRY_COLUMN) as $cKey => $cVal)
			{
				$time = $rVal[$cKey];
				if($time != '')
				{
					$splitTime = explode(':', $time);
					$totalSeconds += $splitTime[0] * 3600 + $splitTime[1] * 60;
				}
			}
		}
		
		$totalHours = $totalSeconds/3600;
		
		return $totalHours >= 40 ? 'true' : 'false';
	}

	/**
     * Collect all time card ids
     * @param $assets
     * @return array $timeCardIDs
     */
    private function GetAllTimeCardIDs($assets){
        $timeCardIDs = [];
        if(count($assets) > 0){
            //
             foreach ($assets as $item){
            $timeCardIDs[] = $item['TimeCardID'];
			}
		}
        return $timeCardIDs;
    }
}
