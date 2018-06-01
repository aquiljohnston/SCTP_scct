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
    public function actionIndex($projectID = null, $projectFilterString = null)
    {
        //guest redirect
        if (Yii::$app->user->isGuest)
        {
            return $this->redirect(['/login']);
        }

        try {
			//if request is not coming from time-card reset session variables. 
			$referrer = Yii::$app->request->referrer;
			if(!strpos($referrer,'time-card')){
				unset(Yii::$app->session['timeCardFormData']);
			}
			
			//Check if user has permission to view time card page
			self::requirePermission("viewTimeCardMgmt");
            //create curl for restful call.
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
                'filter',
                'dateRangeValue',
                'dateRangePicker',
				//need to update this key to projectID, the new value that it represents
                'projectName'
            ]);
            $model ->addRule('pageSize', 'string', ['max' => 32]);//get page number and records per page
            $model ->addRule('filter', 'string', ['max' => 100]); // Don't want overflow but we can have a relatively high max
            $model ->addRule('dateRangePicker', 'string', ['max' => 32]);//get page number and records per page
            $model ->addRule('dateRangeValue', 'string', ['max' => 100]); //
            $model ->addRule('projectName', 'integer'); //

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
            if ($model->load(Yii::$app->request->queryParams)){
				Yii::$app->session['timeCardFormData'] = $model;
			}else{
				//set defaults to session data if avaliable
				if(Yii::$app->session['timeCardFormData'])
				{
					$model = Yii::$app->session['timeCardFormData'];
				}
				else
				{
					$model->pageSize		= 50;
					//set filters if data passed from home screen
					$model->filter			= $projectFilterString != null ? urldecode($projectFilterString): '';
					$model->projectName		= $projectID != null ? $projectID : '';
					$model->dateRangeValue	= $priorWeek;
					$model->dateRangePicker	= null;
				}
            }
			
			//get start/end date based on dateRangeValue
            if ($model->dateRangeValue == "other") {
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

            //check current page at
            if (isset($_GET['timeCardPageNumber'])){
                $page = $_GET['timeCardPageNumber'];
            } else {
                $page = 1;
            }

			//url encode filter
			$encodedFilter = urlencode($model->filter);
			//build params
			$httpQuery = http_build_query([
				'startDate' => $startDate,
				'endDate' => $endDate,
				'listPerPage' => $model->pageSize,
				'page' => $page,
				'filter' => $encodedFilter,
				'projectID' => $model->projectName,
			]);
			// set url
			if($isAccountant)
				$url = 'time-card%2Fget-accountant-view&' . $httpQuery;
			else
				$url = 'time-card%2Fget-cards&' . $httpQuery;

			$response 				        = Parent::executeGetRequest($url, Constants::API_VERSION_2);
            $response 				        = json_decode($response, true);
            $assets 				        = $response['assets'];
            $approvedTimeCardExist 	        = array_key_exists('approvedTimeCardExist', $response) ? $response['approvedTimeCardExist'] : false;
            $showFilter			            = $response['showProjectDropDown'];
            $projectWasSubmitted        	= $response['projectSubmitted'];
			$projectDropDown				= $response['projectDropDown'];
			
			$projArray = array();
			$keys = array_keys($projectDropDown);
			$projCounter=0;
			try{
				for($i=0;$i<sizeof($keys); $i++) {
					if($keys[$i] !== "") {
						$projArray[$projCounter] = $keys[$i];
						++$projCounter;
					}
				}
			} catch(Exception $e) {
				$projArray = $keys;
				Yii::trace('Error: ' . $e);
			}

			//should consider moving submit check into its own function
			$submitCheckData['submitCheck'] = array(
				'ProjectName' => $projArray,
				'StartDate' => $startDate,
				'EndDate' => $endDate,
				'isAccountant' => $isAccountant
			);
			$json_data = json_encode($submitCheckData);
		
			$submit_button_ready_url = 'time-card%2Fcheck-submit-button-status';
			$submit_button_ready_response  = Parent::executePostRequest($submit_button_ready_url, $json_data, Constants::API_VERSION_2);
			$submit_button_ready = json_decode($submit_button_ready_response, true);
			// get submit button status
			if($isAccountant)
				$accountingSubmitReady = $submit_button_ready['SubmitReady'] == "1" ? true : false;
			else
				$pmSubmitReady = $submit_button_ready['SubmitReady'] == "1" ? true : false;
			Yii::trace("Submit button is: " . $submit_button_ready['SubmitReady']);
            // passing decode data into dataProvider
            $dataProvider 		= new ArrayDataProvider
			([
				'allModels' => $assets,
				'pagination' => false,
				'key' => function ($assets) {
					return array(
						'ProjectID' => $assets['ProjectID'],
						'StartDate' => $assets['StartDate'],
						'EndDate' => $assets['EndDate'],
					);
				},
				'sort' => [
					'attributes' => [
						'UserFullName',
						'UserLastName',
						'ProjectName' => [
							'asc' => ['ProjectName' => SORT_ASC],
							'desc' => ['ProjectName' => SORT_DESC],
							'default' => SORT_ASC
						],
						'TimeCardDates',
						'TimeCardOasisSubmitted',
						'TimeCardQBSubmitted',
						'SumHours',
						'TimeCardApprovedFlag',
						'TimeCardPMApprovedFlag'
					]
				]
			]);
			
			if($isAccountant) {
				// Sorting TimeCard table
				$dataProvider->sort = [
					'attributes' => [
						'ProjectName',
						'ProjectManager',
						'StartDate' ,
						'EndDate',
						'ApprovedBy',
						'OasisSubmitted',
						'QBSubmitted',
						'ADPSubmitted' => [
							'default' => SORT_ASC
						]
					]
				];
			} else {
				// Sorting TimeCard table
				$dataProvider->sort = [
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
				'showFilter' => $showFilter,
				'approvedTimeCardExist' => $approvedTimeCardExist,
				'accountingSubmitReady' => $accountingSubmitReady,
				'pmSubmitReady' => $pmSubmitReady,
				'projectSubmitted' => $projectWasSubmitted,
				'isProjectManager' => $isProjectManager,
				'isAccountant' => $isAccountant
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
            throw new ServerErrorHttpException($e);
        }
    }

	/**
	 *Displays time card details for expanded project
	 *@throws \yii\web\HttpException
	 *@returns mixed
	 */
	public function actionViewAccountantDetail()
	{
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
		yii::trace('Query Params: ' . json_encode($queryParams));

        $getUrl = 'time-card%2Fget-accountant-details&' . http_build_query([
                'projectID' => $projectID,
                'startDate' => $startDate,
                'endDate' => $endDate,
            ]);
        $getResponseData = json_decode(Parent::executeGetRequest($getUrl, Constants::API_VERSION_2), true); //indirect RBAC
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
	}
	
     /**
     * Displays all time entries for a given time card.
     * @param string $id
     * @throws \yii\web\HttpException
     * @return mixed
     */
    public function actionShowEntries($id, $projectName = null, $fName = null, $lName = null, $timeCardProjectID = null, $inOvertime = 'false')
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
			$entries_url = 'time-card%2Fshow-entries&cardID='.$id;
			$resp = Parent::executeGetRequest($entries_url, Constants::API_VERSION_2); // rbac check
			$cardData = json_decode($resp, true);

			//send entries to function to calculate if given card is in overtime
			$inOvertime = self::calcInOvertime($cardData['show-entries']);
			
			//populate required values if not received from function call
			$timeCardProjectID = $timeCardProjectID != null ? $timeCardProjectID : $cardData['card']['TimeCardProjectID'];
			$projectName = $projectName != null ? $projectName : $cardData['card']['ProjectName'];
			$fName = $fName != null ? $fName : $cardData['card']['UserFirstName'];
			$lName = $lName != null ? $lName : $cardData['card']['UserLastName'];

			//alter from and to dates a bit
			$from	= str_replace('-','/',$cardData['show-entries'][ENTRIES_ZERO_INDEX]['Date1']);
			$to		= str_replace('-','/',$cardData['show-entries'][ENTRIES_ZERO_INDEX]['Date7']);
			$from	= explode('/', $from);

			//holds dates that accompany table header ex. Sunday 10-23
			$SundayDate 	=  explode('-', $cardData['show-entries'][ENTRIES_ZERO_INDEX]['Date1']);
			$MondayDate		=  explode('-', $cardData['show-entries'][ENTRIES_ZERO_INDEX]['Date2']);
			$TuesdayDate	=  explode('-', $cardData['show-entries'][ENTRIES_ZERO_INDEX]['Date3']);
			$WednesdayDate	=  explode('-', $cardData['show-entries'][ENTRIES_ZERO_INDEX]['Date4']);
			$ThursdayDate 	=  explode('-', $cardData['show-entries'][ENTRIES_ZERO_INDEX]['Date5']);
			$FridayDate		=  explode('-', $cardData['show-entries'][ENTRIES_ZERO_INDEX]['Date6']);
			$SaturdayDate	=  explode('-', $cardData['show-entries'][ENTRIES_ZERO_INDEX]['Date7']);

			$allTask = new ArrayDataProvider([
				'allModels' => $cardData['show-entries'],
                'pagination'=> false,
			]);

			return $this -> render('show-entries', [
				'model' 			=> $cardData['card'],
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
				'SundayDateFull' 	=> date( "Y-m-d", strtotime(str_replace('-', '/', $cardData['show-entries'][ENTRIES_ZERO_INDEX]['Date1']))),
				'MondayDateFull' 	=> date( "Y-m-d", strtotime(str_replace('-', '/', $cardData['show-entries'][ENTRIES_ZERO_INDEX]['Date2']))),
				'TuesdayDateFull' 	=> date( "Y-m-d", strtotime(str_replace('-', '/', $cardData['show-entries'][ENTRIES_ZERO_INDEX]['Date3']))),
				'WednesdayDateFull' => date( "Y-m-d", strtotime(str_replace('-', '/', $cardData['show-entries'][ENTRIES_ZERO_INDEX]['Date4']))),
				'ThursdayDateFull' 	=> date( "Y-m-d", strtotime(str_replace('-', '/', $cardData['show-entries'][ENTRIES_ZERO_INDEX]['Date5']))),
				'FridayDateFull' 	=> date( "Y-m-d", strtotime(str_replace('-', '/', $cardData['show-entries'][ENTRIES_ZERO_INDEX]['Date6']))),
				'SaturdayDateFull' 	=> date( "Y-m-d", strtotime(str_replace('-', '/', $cardData['show-entries'][ENTRIES_ZERO_INDEX]['Date7']))),
				'timeCardProjectID' => $timeCardProjectID,
				'isSubmitted' 		=> $cardData['card']['TimeCardOasisSubmitted']=='Yes' && $cardData['card']['TimeCardQBSubmitted']=='Yes',
				'inOvertime'		=> $inOvertime,

			]);
		}catch(ErrorException $e){
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

			// post url
			$putUrl = 'time-card%2Fapprove-cards';
			$putResponse = Parent::executePutRequest($putUrl, $json_data, Constants::API_VERSION_2); // indirect rbac
			$obj = json_decode($putResponse, true);
			$responseTimeCardID = $obj[0]["TimeCardID"];
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
			$data = Yii::$app->request->post();	
			$jsonData = json_encode($data);
			
			// post url
			$putUrl = 'time-entry%2Fdeactivate';
			$putResponse = Parent::executePutRequest($putUrl, $jsonData,Constants::API_VERSION_2); // indirect rbac
			$obj = json_decode($putResponse, true);	
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

	public function actionPMSubmit() {
		if (Yii::$app->request->isAjax) {
			try{
				$data = Yii::$app->request->post();			
				// set body data
				$body = array(
						'projectIDArray' => $data['projectIDArray'],
						'dateRangeArray' => $data['dateRangeArray'],
					);		
				$json_data = json_encode($body);
				$putResponse = Parent::executePutRequest('time-card%2Fp-m-submit-time-cards', $json_data, Constants::API_VERSION_2); // indirect rbac
				Yii::Trace("Response data: ". json_encode($putResponse));
				return $this->redirect(['index']);
			} catch (\Exception $e) {
				return $e;
				// throw new \yii\web\HttpException(400);
			}
		} else {
			  throw new \yii\web\BadRequestHttpException;
		}
	}

    public function actionAjaxProcessCometTrackerFiles()
	{
        try{
			//TODO clean up JS file so it wont post data that is no longer used. approve_multiple_timecard.js
        	$data = Yii::$app->request->post();	
			
			$response = [];
            $params['params'] = [
			'projectIDArray' => json_encode($data['projectName']),
			'startDate' => $data['weekStart'],
			'endDate' => $data['weekEnd']
			];
			$jsonBody = json_encode($params);
			
            $tcSubmitUrl = 'time-card%2Fsubmit-time-cards';
			
			$submitResponse = json_decode(Parent::executePutRequest($tcSubmitUrl, $jsonBody, Constants::API_VERSION_2), true);
			
			if($submitResponse['success'] == 1)
			{
				$response['success'] = TRUE; 
				$response['message'] = 'Successfully Completed Time Card Process.'; 
				return json_encode($response);
			} else {
				$response['success'] = FALSE; 
                $response['message'] = 'Exception'; 
                return json_encode($response);
			}
        } catch (ForbiddenHttpException $e) {
            throw $e;
        } catch (\Exception $e) {
            $response['success'] = FALSE; 
            $response['message'] = 'Exception occurred.'; 
			return json_encode($response);
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
            if($item != "-"){
                Yii::trace("ITEM: ".$item);
                array_push($dateData, $item);
            }
        }
        return $dateData;
    }

	//count time in provided entries($entriesArray) and returns 'true' if in overtime(over 40 hours) and 'false' if not
	private function calcInOvertime($entriesArray)
	{
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
