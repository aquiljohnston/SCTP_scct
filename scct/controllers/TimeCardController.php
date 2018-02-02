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
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ArrayDataProvider;
use linslin\yii2\curl;
use yii\web\Request;
use \DateTime;
use yii\web\ForbiddenHttpException;
use yii\base\Model;
use yii\web\Response;
use yii\web\ServerErrorHttpException;
use app\constants\Constants;

/**
 * TimeCardController implements the CRUD actions for TimeCard model.
 */
class TimeCardController extends BaseController
{
    /**
     * Lists all TimeCard models.
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws ServerErrorHttpException
     * @throws \yii\web\HttpException
     */
    public function actionIndex()
    {
        //guest redirect
        if (Yii::$app->user->isGuest)
        {
            return $this->redirect(['/login']);
        }

        try {
			//Check if user has permission to view time card page
			self::requirePermission("viewTimeCardMgmt");
            // create curl for restful call.
            //get user role
            $userID = Yii::$app->session['userID'];

            // Store start/end date data
            $dateData = [];
            $startDate = null;
            $endDate = null;

            $model = new \yii\base\DynamicModel([
                'pagesize',
                'filter',
                'dateRangeValue',
                'DateRangePicker',
                'projectName'
            ]);
            $model ->addRule('pagesize', 'string', ['max' => 32]);//get page number and records per page
            $model ->addRule('filter', 'string', ['max' => 100]); // Don't want overflow but we can have a relatively high max
            $model ->addRule('DateRangePicker', 'string', ['max' => 32]);//get page number and records per page
            $model ->addRule('dateRangeValue', 'string', ['max' => 100]); //
            $model ->addRule('projectName', 'string', ['max' => 200]); //

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

            // check if type was post, if so, get value from $model
            if ($model->load(Yii::$app->request->queryParams)) {
                $timeCardPageSizeParams = $model->pagesize;
                $filter = $model->filter;
                $projectName = $model->projectName;
                $dateRangeValue = $model->dateRangeValue;
                $dateRangePicker = $model->DateRangePicker;
            } else {
                    $timeCardPageSizeParams = 50;
                    $filter 			= "";
                    $projectName 		= "";
                    $dateRangeValue 	= $priorWeek;
                    $DateRangePicker 	= null;
            }

            if ($dateRangeValue == "other") {
                if ($dateRangePicker == null){
                    $endDate = $startDate = date('Y-m-d');
                }else {
                    $dateData 	= SELF::dateRangeProcessor($dateRangePicker);
                    $startDate 	= $dateData[0];
                    $endDate 	= $dateData[1];
                }
            }else{
                $dateRangeArray = BaseController::splitDateRange($dateRangeValue);
                $startDate = $dateRangeArray['startDate'];
                $endDate =  $dateRangeArray['endDate'];
            }


            //check current page at
            if (isset($_GET['timeCardPageNumber'])){
                $page = $_GET['timeCardPageNumber'];
            } else {
                $page = 1;
            }

            //get week
            if (isset(Yii::$app->request->queryParams['weekTimeCard'])){
                $week = Yii::$app->request->queryParams['weekTimeCard'];
            } else {
                $week = 'prior';
            }

			//url encode filter
			$encodedFilter 		= urlencode($filter);
			$encodedProjectName = urlencode($projectName);

            //build url with params
            $url 				= "time-card%2Fget-cards&startDate=$startDate&endDate=$endDate&listPerPage=$timeCardPageSizeParams&page=$page&filter=$encodedFilter&projectName=$encodedProjectName";
            $response 			= Parent::executeGetRequest($url, Constants::API_VERSION_2);
            $response 			= json_decode($response, true);
            $assets 			= $response['assets'];
            $projectDropDown 	= $response['projectDropDown'];
            $showFilter		 	= $response['showFilter'];


            // passing decode data into dataProvider
            $dataProvider 		= new ArrayDataProvider
			([
				'allModels' => $assets,
				'pagination' => false
			]);

            // Sorting TimeCard table
            $dataProvider->sort = [
                'attributes' => [
                    'UserFullName' => [
                        'asc' => ['UserFullName' => SORT_ASC],
                        'desc' => ['UserFullName' => SORT_DESC]
                    ],
                    'UserLastName' => [
                        'asc' => ['UserLastName' => SORT_ASC],
                        'desc' => ['UserLastName' => SORT_DESC]
                    ],
                    'ProjectName' => [
                        'asc' => ['ProjectName' => SORT_ASC],
                        'desc' => ['ProjectName' => SORT_DESC]
                    ],
                    'TimeCardDates' => [
                        'asc' => ['TimeCardDates' => SORT_ASC],
                        'desc' => ['TimeCardDates' => SORT_DESC]
                    ],
                    'TimeCardOasisSubmitted' => [
                        'asc' => ['TimeCardOasisSubmitted' => SORT_ASC],
                        'desc' => ['TimeCardOasisSubmitted' => SORT_DESC]
                    ],
                    'TimeCardQBSubmitted' => [
                        'asc' => ['TimeCardQBSubmitted' => SORT_ASC],
                        'desc' => ['TimeCardQBSubmitted' => SORT_DESC]
                    ],
                    'SumHours' => [
                        'asc' => ['SumHours' => SORT_ASC],
                        'desc' => ['SumHours' => SORT_DESC]
                    ],
                    'TimeCardApprovedFlag' => [
                        'asc' => ['TimeCardApprovedFlag' => SORT_ASC],
                        'desc' => ['TimeCardApprovedFlag' => SORT_DESC]
                    ],
                ]
            ];

            //set timecardid as id
            $dataProvider->key = 'TimeCardID';

            // set pages to dispatch table
            $pages = new Pagination($response['pages']);

			//calling index page to pass dataProvider.
			if(Yii::$app->request->isAjax) {
				return $this->renderAjax('index', [
					'dataProvider' 				=> $dataProvider,
                    'dateRangeDD'				=> $dateRangeDD,
                    'dateRangeValue' 			=> $dateRangeValue,
                    'week' 						=> $week,
					'model' 					=> $model,
					'timeCardPageSizeParams' 	=> $timeCardPageSizeParams,
					'pages' 					=> $pages,
					'timeCardFilterParams' 		=> $filter,
					'projectDropDown' 			=> $projectDropDown,
					'showFilter' 				=> $showFilter
				]);
			}else{
				return $this->render('index', [
					'dataProvider' 				=> $dataProvider,
                    'dateRangeDD' 				=> $dateRangeDD,
                    'dateRangeValue' 			=> $dateRangeValue,
                    'week' 						=> $week,
					'model' 					=> $model,
					'timeCardPageSizeParams' 	=> $timeCardPageSizeParams,
					'pages' 					=> $pages,
					'timeCardFilterParams' 		=> $filter,
					'projectDropDown' 			=> $projectDropDown,
					'showFilter' 				=> $showFilter
				]);
			}
        } catch (UnauthorizedHttpException $e){
            Yii::$app->response->redirect(['login/index']);
        } catch(ForbiddenHttpException $e) {
            throw $e;
        } catch(ErrorException $e) {
            throw new \yii\web\HttpException(400);
        } catch(Exception $e) {
            throw new ServerErrorHttpException("An unknown error has occurred.");
        }
    }

    /**
     * Displays a single TimeCard model.
     * @param string $id
     * @throws \yii\web\HttpException
     * @return mixed
     */
    public function actionView($id)
    {		
		//guest redirect
		if (Yii::$app->user->isGuest)
		{
			return $this->redirect(['/login']);
		}

		try{
			// set default value 0 to duplicateFlag
			// duplicationflag:
			// 1: yes 0: no
			// set duplicateFlag to 1, which means duplication happened.

			$duplicateFlag = 0;
			if (strpos($id, "yes") == true) {
				$id = str_replace("yes", "", $id);
				$duplicateFlag = 1;
			}
			$url = 'time-card%2Fget-entries&cardID='.$id;
			$time_card_url = 'time-card%2Fview&id='.$id;

			$response = Parent::executeGetRequest($url, Constants::API_VERSION_2); // rbac check
			$time_card_response = Parent::executeGetRequest($time_card_url, Constants::API_VERSION_2); // rbac check
			$model = json_decode($time_card_response, true);
			$entryData = json_decode($response, true);
			$ApprovedFlag = $entryData['ApprovedFlag'];
			
			$Sundaydata = $entryData['TimeEntries']['Sunday']['Entries'];
			$SundayProvider = new ArrayDataProvider([
				'allModels' => $Sundaydata,
				'pagination' => false,
			]);
			$Total_Hours_Sun = $entryData['TimeEntries']['Sunday']['Total'];

			$Mondaydata = $entryData['TimeEntries']['Monday']['Entries'];
			$MondayProvider = new ArrayDataProvider([
				'allModels' => $Mondaydata,
                'pagination' => false,
			]);
			$Total_Hours_Mon = $entryData['TimeEntries']['Monday']['Total'];

			$Tuesdaydata = $entryData['TimeEntries']['Tuesday']['Entries'];
			$TuesdayProvider = new ArrayDataProvider([
				'allModels' => $Tuesdaydata,
                'pagination' => false,
			]);
			$Total_Hours_Tue = $entryData['TimeEntries']['Tuesday']['Total'];

			$Wednesdaydata = $entryData['TimeEntries']['Wednesday']['Entries'];
			$WednesdayProvider = new ArrayDataProvider([
				'allModels' => $Wednesdaydata,
                'pagination' => false,
			]);
			$Total_Hours_Wed = $entryData['TimeEntries']['Wednesday']['Total'];

			$Thursdaydata = $entryData['TimeEntries']['Thursday']['Entries'];
			$ThursdayProvider = new ArrayDataProvider([
				'allModels' => $Thursdaydata,
                'pagination' => false,
			]);
			$Total_Hours_Thu = $entryData['TimeEntries']['Thursday']['Total'];

			$Fridaydata = $entryData['TimeEntries']['Friday']['Entries'];
			$FridayProvider = new ArrayDataProvider([
				'allModels' => $Fridaydata,
                'pagination' => false,
			]);
			$Total_Hours_Fri = $entryData['TimeEntries']['Friday']['Total'];

			$Saturdaydata = $entryData['TimeEntries']['Saturday']['Entries'];
			$SaturdayProvider = new ArrayDataProvider([
				'allModels' => $Saturdaydata,
                'pagination' => false,
			]);
			$Total_Hours_Sat = $entryData['TimeEntries']['Saturday']['Total'];

			//calculation total hours for this timecardid
			$Total_Hours_Current_TimeCard = $Total_Hours_Sun +
											$Total_Hours_Mon +
											$Total_Hours_Tue +
											$Total_Hours_Wed +
											$Total_Hours_Thu +
											$Total_Hours_Fri +
											$Total_Hours_Sat;
			//set TimeEntryID as id
			$SundayProvider->key ='TimeEntryID';
			$MondayProvider->key ='TimeEntryID';
			$TuesdayProvider->key ='TimeEntryID';
			$WednesdayProvider->key ='TimeEntryID';
			$ThursdayProvider->key ='TimeEntryID';
			$FridayProvider->key ='TimeEntryID';
			$SaturdayProvider->key ='TimeEntryID';

			return $this -> render('view', [
											'model' => $model,
											'duplicateFlag' => $duplicateFlag,
											'ApprovedFlag' => $ApprovedFlag,
											'Total_Hours_Current_TimeCard' => $Total_Hours_Current_TimeCard,
											'SundayProvider' => $SundayProvider,
											'Total_Hours_Sun' => $Total_Hours_Sun,
											'MondayProvider' => $MondayProvider,
											'Total_Hours_Mon' => $Total_Hours_Mon,
											'TuesdayProvider' => $TuesdayProvider,
											'Total_Hours_Tue' => $Total_Hours_Tue,
											'WednesdayProvider' => $WednesdayProvider,
											'Total_Hours_Wed' => $Total_Hours_Wed,
											'ThursdayProvider' => $ThursdayProvider,
											'Total_Hours_Thu' => $Total_Hours_Thu,
											'FridayProvider' => $FridayProvider,
											'Total_Hours_Fri' => $Total_Hours_Fri,
											'SaturdayProvider' => $SaturdayProvider,
											'Total_Hours_Sat' => $Total_Hours_Sat,
									]);
		}catch(ErrorException $e){
			throw new \yii\web\HttpException(400);
		}
    } 



     /**
     * Displays all time entries for a given time card.
     * @param string $id
     * @throws \yii\web\HttpException
     * @return mixed
     */
    public function actionShowEntries($id, $projectName = null, $fName = null, $lName = null)
    {		
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

		try{

			//build api url paths
			$time_card_url	= 'time-card%2Fview&id='.$id;
			$entries_url 	= 'time-card%2Fshow-entries&cardID='.$id;

			//execute API request
			$resp 			= Parent::executeGetRequest($entries_url, Constants::API_VERSION_2); // rbac check
			$time_response 	= Parent::executeGetRequest($time_card_url, Constants::API_VERSION_2); // rbac check
			$card 			= json_decode($time_response, true);
			$entries 		= json_decode($resp, true);

			//alter from and to dates a bit
			$from   		=	str_replace('-','/',$entries[ENTRIES_ZERO_INDEX]['Date1']);
			$to   			=	str_replace('-','/',$entries[ENTRIES_ZERO_INDEX]['Date7']);
			$from 			= 	explode('/', $from);

			//holds dates that accompany table header ex. Sunday 10-23
			$SundayDate 	=  explode('-', $entries[ENTRIES_ZERO_INDEX]['Date1']);
			$MondayDate		=  explode('-', $entries[ENTRIES_ZERO_INDEX]['Date2']);
			$TuesdayDate	=  explode('-', $entries[ENTRIES_ZERO_INDEX]['Date3']);
			$WednesdayDate	=  explode('-', $entries[ENTRIES_ZERO_INDEX]['Date4']);
			$ThursdayDate 	=  explode('-', $entries[ENTRIES_ZERO_INDEX]['Date5']);
			$FridayDate		=  explode('-', $entries[ENTRIES_ZERO_INDEX]['Date6']);
			$SaturdayDate	=  explode('-', $entries[ENTRIES_ZERO_INDEX]['Date7']);
		
			$allTask = new ArrayDataProvider([
				'allModels' => $entries,
                'pagination'=> false,
			]);

			return $this -> render('show-entries', [
											'model' 			=> $card,
											'task' 				=> $allTask,
											'from' 				=> $from[FROM_DATE_ZERO_INDEX].'/'.$from[TO_DATE_FIRST_INDEX],
											'to' 				=> $to,
											'SundayDate' 		=> $SundayDate[DATES_ZERO_INDEX].'-'.$SundayDate[DATES_FIRST_INDEX],
											'MondayDate' 		=> $MondayDate[DATES_ZERO_INDEX].'-'.$MondayDate[DATES_FIRST_INDEX],
											'TuesdayDate' 		=> $TuesdayDate[DATES_ZERO_INDEX].'-'.$TuesdayDate[DATES_FIRST_INDEX],
											'WednesdayDate' 	=> $WednesdayDate[DATES_ZERO_INDEX].'-'.$WednesdayDate[DATES_FIRST_INDEX],
											'ThursdayDate' 		=> $ThursdayDate[DATES_ZERO_INDEX].'-'.$ThursdayDate[DATES_FIRST_INDEX],
											'FridayDate' 		=> $FridayDate[DATES_ZERO_INDEX].'-'.$FridayDate[DATES_FIRST_INDEX],
											'SaturdayDate' 		=> $SaturdayDate[DATES_ZERO_INDEX].'-'.$SaturdayDate[DATES_FIRST_INDEX],
                                            'projectName'   	=> $projectName,
                                            'fName'   			=> $fName,
                                            'lName'   			=> $lName,
                                            'SundayDateFull' 	=> date( "Y-m-d", strtotime(str_replace('-', '/', $entries[ENTRIES_ZERO_INDEX]['Date1']))),
											'MondayDateFull' 	=>  date( "Y-m-d", strtotime(str_replace('-', '/', $entries[ENTRIES_ZERO_INDEX]['Date2']))),
											'TuesdayDateFull' 	=>  date( "Y-m-d", strtotime(str_replace('-', '/', $entries[ENTRIES_ZERO_INDEX]['Date3']))),
											'WednesdayDateFull' =>  date( "Y-m-d", strtotime(str_replace('-', '/', $entries[ENTRIES_ZERO_INDEX]['Date4']))),
											'ThursdayDateFull' 	=>  date( "Y-m-d", strtotime(str_replace('-', '/', $entries[ENTRIES_ZERO_INDEX]['Date5']))),
											'FridayDateFull' 	=>  date( "Y-m-d", strtotime(str_replace('-', '/', $entries[ENTRIES_ZERO_INDEX]['Date6']))),
											'SaturdayDateFull' 	=>  date( "Y-m-d", strtotime(str_replace('-', '/', $entries[ENTRIES_ZERO_INDEX]['Date7'])))

									]);
		}catch(ErrorException $e){
			throw new \yii\web\HttpException(400);
		}
    }

    /**
     * Creates a new TimeEntry model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param $id
     * @param $TimeCardTechID
     * @param $TimeEntryDate
     * @return mixed
     * @throws \yii\web\HttpException
     */
	public function actionCreateTimeEntry($id = null, $TimeCardTechID = null, $TimeEntryDate = null)
	{
		//guest redirect
		if (Yii::$app->user->isGuest) {
			return $this->redirect(['/login']);
		}
		self::requirePermission("timeEntryCreate");
		try {

			$timeEntryModel = new TimeEntry();
			$activityModel = new Activity();
            //$id = "";
            //$TimeCardTechID = "";
			//generate array for Active Flag dropdown
			$flag =
				[
					1 => "Active",
					0 => "Inactive",
				];
			$obj = "";

			//GET DATA TO FILL FORM DROPDOWNS//
			//get clients for form dropdown
			$activityCodeUrl = "activity-code%2Fget-code-dropdowns";
			$activityCodeResponse = Parent::executeGetRequest($activityCodeUrl);
			$activityCode = json_decode($activityCodeResponse, true);

			$activityPayCodeUrl = "pay-code%2Fget-code-dropdowns";
			$activityPayCodeResponse = Parent::executeGetRequest($activityPayCodeUrl);
			$activityPayCode = json_decode($activityPayCodeResponse, true);

			if ($timeEntryModel->load(Yii::$app->request->post()) && $activityModel->load(Yii::$app->request->post())
				&& $timeEntryModel->validate() && $activityModel->validate())
			{
				//create timeEntryTitle variable
				$timeEntryTitle = "timeEntry";
				// concatenate start time
				$TimeEntryStartTimeConcatenate = new DateTime($TimeEntryDate . $timeEntryModel->TimeEntryStartTime);
				$TimeEntryStartTimeConcatenate = $TimeEntryStartTimeConcatenate->format('Y-m-d H:i:s');

				// concatenate end time
				$TimeEntryEndTimeConcatenate = new DateTime($TimeEntryDate . $timeEntryModel->TimeEntryEndTime);
				$TimeEntryEndTimeConcatenate = $TimeEntryEndTimeConcatenate->format('Y-m-d H:i:s');

				// check user input validate startTime and endTime
				$CheckStartTime = $TimeEntryStartTimeConcatenate;
				$CheckEndTime = $TimeEntryEndTimeConcatenate;
				$startTimeObj = new DateTime($CheckStartTime);
				$endTimeObj = new DateTime($CheckEndTime);
				//$interval = $datetimeObj1->diff($datetimeObj2);
				//$dateTimeDiff = $interval->format('%R%a');

				$time_entry_data[] = array(
					'TimeEntryStartTime' => $TimeEntryStartTimeConcatenate,
					'TimeEntryEndTime' => $TimeEntryEndTimeConcatenate,
					'TimeEntryModifiedDate' => $timeEntryModel->TimeEntryModifiedDate,
					'TimeEntryUserID' => $TimeCardTechID,
					'TimeEntryTimeCardID' => $id,
					'TimeEntryActivityID' => $timeEntryModel->TimeEntryActivityID,
					'TimeEntryComment' => $timeEntryModel->TimeEntryComment,
					'TimeEntryModifiedBy' => $timeEntryModel->TimeEntryModifiedBy,
				);

				// check difference between startTime and endTime
				if ($endTimeObj > $startTimeObj) {
					$mileage_entry_data = array();
					$data[] = array(
						'ActivityUID' => BaseController::generateUID($timeEntryTitle),
						'ActivityTitle' => $timeEntryTitle,
						'ActivityCreatedBy' => Yii::$app->session['userID'],
						'ActivityPayCode' => $activityModel->ActivityPayCode,
						'ActivityCode' => $activityModel->ActivityCode,
						'timeEntry' => $time_entry_data,
						'mileageEntry' => $mileage_entry_data,
					);

					$activity = array(
						'activity' => $data,
					);

					$json_data = json_encode($activity);

					try {
						// post url
						$url_send_activity = 'activity%2Fcreate';
						$response_activity = Parent::executePostRequest($url_send_activity, $json_data, Constants::API_VERSION_2);
						$obj = json_decode($response_activity, true);

                        /*return $this->renderAjax('view',[
                            //'id' => $obj["activity"][0]["timeEntry"][0]["TimeEntryTimeCardID"]
                        ]);*/

						return $this->redirect(['view', 'id' => $obj["activity"][0]["timeEntry"][0]["TimeEntryTimeCardID"], 'AjaxRender' => true]);
					} catch (\Exception $e) {

						$concatenate_id = $id . "yes";
						return $this->redirect(['view', 'id' => $concatenate_id]);
					}
				} else {
					return $this->redirect(['view', 'id' => $id]);
				}
			} else {
				return $this->render('create_time_entry', [
					'model' => $timeEntryModel,
					'activityCode' => $activityCode,
					'activityPayCode' => $activityPayCode,
					'activityModel' => $activityModel,
				]);
			}
		} catch (ErrorException $e) {
			throw new \yii\web\HttpException(400);
		}
	}

    /**
     * Approve an existing TimeEntry.
     * If approve is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     * @throws \yii\web\HttpException
     */
	public function actionApprove($id){
		//guest redirect
		if(Yii::$app->user->isGuest){
			return $this->redirect(['/login']);
		}
		
		try{			
			$cardIDArray[] = $id;
			$data = array(
				'cardIDArray' => $cardIDArray,
			);
			$json_data = json_encode($data);

			$referrer = Yii::$app->request->referrer;

			// post url
			$putUrl = 'time-card%2Fapprove-cards';
			$putResponse = Parent::executePutRequest($putUrl, $json_data, Constants::API_VERSION_2); // indirect rbac
			$obj = json_decode($putResponse, true);
			$responseTimeCardID = $obj[0]["TimeCardID"];

			if(strpos($referrer,'show-entries')){

				return $this->redirect(['index']);

			}else{
				return $this->redirect(['view', 'id' => $responseTimeCardID]);
			}

			
		}catch(ErrorException $e){
			throw new \yii\web\HttpException(400);
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
	public function actionDeactivate(){

			
			try{

				$data 			= Yii::$app->request->post();	

				$json_data 		= json_encode($data['entries']);

				//var_dump($json_data);exit();
				
				// post url
				$putUrl 		= 'time-entry%2Fdeactivate';
				$putResponse 	= Parent::executePutRequest($putUrl, $json_data,Constants::API_VERSION_2); // indirect rbac
				$obj 			= json_decode($putResponse, true);

				//var_dump($obj);exit();


				//return $this->redirect(['index', 'id' => $obj[0]['TimeEntryTimeCardID']]);
				//fail gracefully if no response time card entry id
				//return $this->redirect(['index']);
				
			}catch(ErrorException $e){
				throw new \yii\web\HttpException(400);
			}
		
	}

    /**
     * Approve Multiple existing TimeCard.
     * If approve is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     * @throws \yii\web\BadRequestHttpException
     * @throws \yii\web\HttpException
     * @internal param string $id
     *
     */
	public function actionApproveMultiple() {
		
		if (Yii::$app->request->isAjax) {
			
			try{
				
				$data = Yii::$app->request->post();					
				 // loop the data array to get all id's.	
				foreach ($data as $key) {
					foreach($key as $keyitem){
					
					   $TimeCardIDArray[] = $keyitem;
					   Yii::Trace("TimeCardid is ; ". $keyitem);
					}
				}
				
				$data = array(
						'cardIDArray' => $TimeCardIDArray,
					);		
				$json_data = json_encode($data);
				
				// post url
					$putUrl = 'time-card%2Fapprove-cards';
					$putResponse = Parent::executePutRequest($putUrl, $json_data, Constants::API_VERSION_2); // indirect rbac
					Yii::trace("PutResponse: ".json_encode($putResponse));
					return $this->redirect(['index']);
			} catch (\Exception $e) {
				throw new \yii\web\HttpException(400);
			}
		} else {
			  throw new \yii\web\BadRequestHttpException;
		}
	}

    /**
     * Get TimeCard History Data
     * @return \yii\web\Response
     * @throws ForbiddenHttpException
     */
    public function actionDownloadTimeCardData() {
        try {
            // check if user has permission
            //self::SCRequirePermission("downloadTimeCardData");  // TODO check if this is the right permission

            //guest redirect
            if (Yii::$app->user->isGuest) {
                return $this->redirect(['/login']);
            }

            $selectedTimeCardIDs = isset(Yii::$app->request->queryParams['selectedTimeCardIDs']) ? Yii::$app->request->queryParams['selectedTimeCardIDs'] : null;

            $url = 'time-card%2Fget-time-cards-history-data&' . http_build_query([
                'selectedTimeCardIDs' => json_encode($selectedTimeCardIDs),
                    'week' => null
                ]);

            Yii::trace("LOAD DATA URL: ".$url);
            $dateTime = date('Y-m-d_h_i');

            header('Content-Disposition: attachment; filename="timecard_history_'.$dateTime.'.csv"');
            $this->requestAndOutputCsv($url);

        } catch (ForbiddenHttpException $e) {
            throw new ForbiddenHttpException('You do not have adequate permissions to perform this action.');
        } catch (\Exception $e) {
            Yii::trace('EXCEPTION raised'.$e->getMessage());
            // Yii::$app->runAction('login/user-logout');
        }
    }

    /**
     * Get Payroll Data
     * @return \yii\web\Response
     * @throws ForbiddenHttpException
     */
    public function actionDownloadPayrollData() {
        try {

            //guest redirect
            if (Yii::$app->user->isGuest) {
                return $this->redirect(['/login']);
            }

            $selectedTimeCardIDs = isset(Yii::$app->request->queryParams['selectedTimeCardIDs']) ? Yii::$app->request->queryParams['selectedTimeCardIDs'] : null;

            $url = 'time-card%2Fget-payroll-data&' . http_build_query([
                    'selectedTimeCardIDs' => json_encode($selectedTimeCardIDs)
                ]);
            $payrollFile = 'payroll';
            $dateTime = date('Y-m-d_h_i');

            Yii::trace("LOAD DATA URL: ".$url);

            header('Content-Disposition: attachment; filename="payroll_history_'.$dateTime.'.csv"');
            $this->requestAndOutputCsv($url);
        } catch (ForbiddenHttpException $e) {
            throw new ForbiddenHttpException('You do not have adequate permissions to perform this action.');
        } catch (\Exception $e) {
            Yii::trace('EXCEPTION raised'.$e->getMessage());
            // Yii::$app->runAction('login/user-logout');
        }
    }

    /**
     * Export TimeCard Table To Excel File
     * @param $url
     */
    public function requestAndOutputCsv($url){
        Yii::$app->response->format = Response::FORMAT_RAW;
        $fp = fopen('php://temp','w');
        header('Content-Type: text/csv;charset=UTF-8');
        header('Pragma: no-cache');
        header('Expires: 0');
        Parent::executeGetRequestToStream($url,$fp, Constants::API_VERSION_2);
        rewind($fp);
        echo stream_get_contents($fp);
        fclose($fp);
    }

    public function actionFtpFiles(){
        $userName =  getenv("username");
        Yii::trace("CURRENT USER NAME IS : ".$userName);
        $defaultPath = 'C:\Users\\'.$userName.'\Downloads';
        $fileType = '.csv';
        $dateTime = date('Y-m-d_h_i');

        $timeCardFile = '\timecard_history_';
        $filePath = $defaultPath . $timeCardFile . $dateTime . $fileType;
        if (file_exists($filePath)) {
            Yii::trace("The file $filePath exists");
        } else {
            Yii::trace("The file $filePath not exists");
        }
        
        $remoteFileName = 'timecard_history_' . $dateTime . $fileType;

        $ftp_server = "10.100.10.10";
        $ftp_user_name='ftpdev.southerncrosslighthouse.com|tzhang';
        $ftp_user_pass='Wojiushiye008';

        // FTP connection
        $ftp_conn = ftp_connect($ftp_server);

        // FTP login
        @$login_result=ftp_login($ftp_conn, $ftp_user_name, $ftp_user_pass);

        ftp_pasv($ftp_conn, true);

        if ($login_result)
            Yii::trace("LOGIN IN SUCCESS");
        else
            Yii::trace("LOGIN IN FALL");

        // get FTP result
        $upload_result = ftp_put($ftp_conn, $remoteFileName, $filePath, FTP_BINARY);

        // Error handling
        if(!$upload_result)
        {
            Yii::trace("FTP error: The file could not be written to the FTP server.");
        } else {
            Yii::trace("FTP success: The file is transferred to FTP server.");
        }

        // close connection
        ftp_close($ftp_conn);
    }

    public function actionFtpFilesPayroll(){
        //$defaultPath = 'C:\Users\tzhang\Downloads';
        $fileType = '.csv';
        $dateTime = date('Y-m-d_h_i');

        $userName =  getenv("username");
        Yii::trace("CURRENT USER NAME IS : ".$userName);
        $defaultPath = 'C:\Users\\'.$userName.'\Downloads';
        $payrollFile = '\payroll_history_';
        $filePath = $defaultPath . $payrollFile . $dateTime . $fileType;
        if (file_exists($filePath)) {
            Yii::trace("The file $filePath exists");
        } else {
            Yii::trace("The file $filePath not exists");
        }

        $remoteFileName = 'payroll_history_' . $dateTime . $fileType;

        $ftp_server = "10.100.10.10";
        $ftp_user_name='ftpdev.southerncrosslighthouse.com|tzhang';
        $ftp_user_pass='Wojiushiye008';

        // FTP connection
        $ftp_conn = ftp_connect($ftp_server);

        // FTP login
        @$login_result=ftp_login($ftp_conn, $ftp_user_name, $ftp_user_pass);

        ftp_pasv($ftp_conn, true);

        if ($login_result)
            Yii::trace("LOGIN IN SUCCESS");
        else
            Yii::trace("LOGIN IN FALL");

        // get FTP result
        $upload_result = ftp_put($ftp_conn, $remoteFileName, $filePath, FTP_BINARY);

        // Error handling
        if(!$upload_result)
        {
            Yii::trace("FTP error: The file could not be written to the FTP server.");
        } else {
            Yii::trace("FTP success: The file is transferred to FTP server.");
        }

        // close connection
        ftp_close($ftp_conn);
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
            if($item != "-"){
                Yii::trace("ITEM: ".$item);
                array_push($dateData, $item);
            }
        }
        return $dateData;
    }

    /**
     * Add New Task Entry.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param $TimeCardID
     * @return string
     * @throws \yii\web\HttpException
     */
    public function actionAddTaskEntry($TimeCardID = null)
    {
        //guest redirect
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['/login']);
        }
        self::requirePermission("timeEntryCreate");

        $model = new \yii\base\DynamicModel([
            'TimeCardID',
            'TaskName',
            'Date',
            'StartTime',
            'EndTime',
            'ChargeOfAccountType'
        ]);
        $model -> addRule('TimeCardID', 'string', ['max' => 100]);
        $model -> addRule('TaskName', 'string', ['max' => 100]);
        $model -> addRule('Date', 'string', ['max' => 32]);
        $model -> addRule('StartTime', 'string', ['max' => 100]);
        $model -> addRule('EndTime', 'string', ['max' => 100]);
        $model -> addRule('ChargeOfAccountType', 'string', ['max' => 100]);

        try {

            //get tasks for form dropdown
            $getAllTaskUrl = "task%2Fget-all-task";
            $getAllTaskResponse = Parent::executeGetRequest($getAllTaskUrl, Constants::API_VERSION_2);
            $allTask = json_decode($getAllTaskResponse, true);
            $allTask = $this->FormatTaskData($allTask['assets']);

            //get chartOfAccountType for form dropdown
            $getAllChartOfAccountTypeUrl = "time-card%2Fget-charge-of-account-type";
            $getAllChartOfAccountTypeResponse = Parent::executeGetRequest($getAllChartOfAccountTypeUrl, Constants::API_VERSION_2);
            $chartOfAccountType = json_decode($getAllChartOfAccountTypeResponse, true);

            if ($model->load(Yii::$app->request->queryParams)) {

                $task_entry_data = array(
                    'TimeCardID' => $model->TimeCardID,
                    'TaskName' => 'Task ' . $model->TaskName,
                    'Date' => $model->Date,
                    'StartTime' => $model->StartTime,
                    'EndTime' => $model->EndTime,
                    'CreatedByUserName' => Yii::$app->session['UserName'],
                );

                // check difference between startTime and endTime
                if ($model->EndTime >= $model->StartTime) {

                    $json_data = json_encode($task_entry_data);
                    Yii::trace("NEW TASK DATA: ".$json_data);
                    try {
                        // post url
                        $url = 'time-card%2Fcreate-task-entry';
                        $response = Parent::executePostRequest($url, $json_data, Constants::API_VERSION_2);
                        Yii::trace("NEW TASK ENTRY RESPONSE: ".$response);
                        $obj = json_decode($response, true);

                    } catch (\Exception $e) {
                        return $this->redirect(['show-entries', 'id' => $model->TimeCardID]);
                    }
                } else {
                    return $this->redirect('show-entries', ['id' => $model->TimeCardID]);
                }
            } else {
                if (Yii::$app->request->isAjax) {
                    return $this->renderAjax('create_task_entry', [
                        'model' => $model,
                        'allTask' => $allTask,
                        'chartOfAccountType' => $chartOfAccountType,
                        'timeCardID' => $TimeCardID
                    ]);
                } else {
                    return $this->render('create_task_entry', [
                        'model' => $model,
                        'allTask' => $allTask,
                        'chartOfAccountType' => $chartOfAccountType,
                        'timeCardID' => $TimeCardID
                    ]);
                }
            }

        } catch (ErrorException $e) {
            throw new \yii\web\HttpException(400);
        }
    }

    /**Format Task Array
     * @param $data
     * @return array
     */
    private function FormatTaskData($data){
        $namePairs = [];
        if ($data != null) {
            $codesSize = count($data);

            for ($i = 0; $i < $codesSize; $i++) {
                $namePairs[$data[$i]['TaskName']] = $data[$i]['TaskName'];
            }
        }
        return $namePairs;
    }
}
