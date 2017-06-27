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
    public function actionIndex($recordNumber = null)
    {
        //guest redirect
        if (Yii::$app->user->isGuest)
        {
            return $this->redirect(['/login']);
        }

        try {
            // create curl for restful call.
            //get user role
            $userID = Yii::$app->session['userID'];

            $model = new \yii\base\DynamicModel([
                'pagesize',
                'filter'
            ]);
            $model ->addRule('pagesize', 'string', ['max' => 32]);//get page number and records per page
            $model ->addRule('filter', 'string', ['max' => 100]); // Don't want overflow but we can have a relatively high max

            // check if type was post, if so, get value from $model
            if ($model->load(Yii::$app->request->queryParams)) {
                $timeCardPageSizeParams = $model->pagesize;
                $filter = $model->filter;
            } else {
                    $timeCardPageSizeParams = 50;
                    $filter = "";
            }

            //check current page at
            if (isset(Yii::$app->request->queryParams['timeCardPageNumber'])){
                $page = Yii::$app->request->queryParams['timeCardPageNumber'];
            } else {
                $page = 1;
            }

            //get week
            if (isset(Yii::$app->request->queryParams['weekTimeCard'])){
                $week = Yii::$app->request->queryParams['weekTimeCard'];
            } else {
                $week = 'current';
            }

            //build url with params
            $url = "time-card%2Fget-cards&filter=$filter&week=$week&listPerPage=$timeCardPageSizeParams&page=$page";
            $response = Parent::executeGetRequest($url, self::API_VERSION_2);
            $response = json_decode($response, true);
            $assets = $response['assets'];


            // passing decode data into dataProvider
            $dataProvider = new ArrayDataProvider
			([
				'allModels' => $assets,
				/*'key' => function (){
                    return md5($model["TimeCardID"]);
                },*/
				'pagination' => [
					'pageSize' => 100,
				],
			]);

            // Sorting TimeCard table
            $dataProvider->sort = [
                'attributes' => [
                    'UserFirstName' => [
                        'asc' => ['UserFirstName' => SORT_ASC],
                        'desc' => ['UserFirstName' => SORT_DESC]
                    ],
                    'UserLastName' => [
                        'asc' => ['UserLastName' => SORT_ASC],
                        'desc' => ['UserLastName' => SORT_DESC]
                    ],
                    'ProjectName' => [
                        'asc' => ['ProjectName' => SORT_ASC],
                        'desc' => ['ProjectName' => SORT_DESC]
                    ],
                    'TimeCardStartDate' => [
                        'asc' => ['TimeCardStartDate' => SORT_ASC],
                        'desc' => ['TimeCardStartDate' => SORT_DESC]
                    ],
                    'TimeCardEndDate' => [
                        'asc' => ['TimeCardEndDate' => SORT_ASC],
                        'desc' => ['TimeCardEndDate' => SORT_DESC]
                    ],
                    'SumHours' => [
                        'asc' => ['SumHours' => SORT_ASC],
                        'desc' => ['SumHours' => SORT_DESC]
                    ]
                ]
            ];

            //set timecardid as id
            $dataProvider->key = 'TimeCardID';


            // Create drop down with current selection pre-selected based on GET variable
//            $approvedInput = '<select class="form-control" name="filterapproved">'
//				. '<option value=""></option><option value="Yes"';
//            if ($searchModel['TimeCardApprovedFlag'] == "Yes") {
//                $approvedInput .= " selected";
//            }
//            $approvedInput .= '>Yes</option><option value="No"';
//            if ($searchModel['TimeCardApprovedFlag'] == "No") {
//                $approvedInput .= ' selected';
//            }
//            $approvedInput .= '>No</option></select>';

            // set pages to dispatch table
            $pages = new Pagination($response['pages']);

            //calling index page to pass dataProvider.
            return $this->render('index', [
                'dataProvider' => $dataProvider,
                //'searchModel' => $searchModel,
                //'approvedInput' => $approvedInput,
                'week' => $week,
                'model' => $model,
                'timeCardPageSizeParams' => $timeCardPageSizeParams,
                'pages' => $pages
            ]);

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

			$response = Parent::executeGetRequest($url); // rbac check
			$time_card_response = Parent::executeGetRequest($time_card_url); // rbac check
			$model = json_decode($time_card_response, true);
			$dateProvider = json_decode($response, true);
			$ApprovedFlag = $dateProvider["ApprovedFlag"];
			$Sundaydata = $dateProvider["TimeEntries"][0]["Sunday"];
			$SundayProvider = new ArrayDataProvider([
				'allModels' => $Sundaydata,
				'pagination' => false,
				// 'sort' => [
					// 'attributes' => ['id', 'name'],
				// ],
			]);
			$Total_Hours_Sun = $this->TotalHourCal($Sundaydata);

			$Mondaydata = $dateProvider["TimeEntries"][0]["Monday"];
			$MondayProvider = new ArrayDataProvider([
				'allModels' => $Mondaydata,
                'pagination' => false,
				// 'sort' => [
					// 'attributes' => ['id', 'name'],
				// ],
			]);
			$Total_Hours_Mon = $this->TotalHourCal($Mondaydata);

			$Tuesdaydata = $dateProvider["TimeEntries"][0]["Tuesday"];
			$TuesdayProvider = new ArrayDataProvider([
				'allModels' => $Tuesdaydata,
                'pagination' => false,
				// 'sort' => [
					// 'attributes' => ['id', 'name'],
				// ],
			]);
			$Total_Hours_Tue = $this->TotalHourCal($Tuesdaydata);

			$Wednesdaydata = $dateProvider["TimeEntries"][0]["Wednesday"];
			$WednesdayProvider = new ArrayDataProvider([
				'allModels' => $Wednesdaydata,
                'pagination' => false,
				// 'sort' => [
					// 'attributes' => ['id', 'name'],
				// ],
			]);
			$Total_Hours_Wed = $this->TotalHourCal($Wednesdaydata);

			$Thursdaydata = $dateProvider["TimeEntries"][0]["Thursday"];
			$ThursdayProvider = new ArrayDataProvider([
				'allModels' => $Thursdaydata,
                'pagination' => false,
				// 'sort' => [
					// 'attributes' => ['id', 'name'],
				// ],
			]);
			$Total_Hours_Thu = $this->TotalHourCal($Thursdaydata);

			$Fridaydata = $dateProvider["TimeEntries"][0]["Friday"];
			$FridayProvider = new ArrayDataProvider([
				'allModels' => $Fridaydata,
                'pagination' => false,
				// 'sort' => [
					// 'attributes' => ['id', 'name'],
				// ],
			]);
			$Total_Hours_Fri = $this->TotalHourCal($Fridaydata);

			$Saturdaydata = $dateProvider["TimeEntries"][0]["Saturday"];
			$SaturdayProvider = new ArrayDataProvider([
				'allModels' => $Saturdaydata,
                'pagination' => false,
				// 'sort' => [
					// 'attributes' => ['id', 'name'],
				// ],
			]);
			$Total_Hours_Sat = $this->TotalHourCal($Saturdaydata);

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
											'dateProvider' => $dateProvider,
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

                Yii::trace("ID is : ".$id);
                Yii::trace("TimeCardTechID: ".$TimeCardTechID);

				$time_entry_data[] = array(
					'TimeEntryStartTime' => $TimeEntryStartTimeConcatenate,
					'TimeEntryEndTime' => $TimeEntryEndTimeConcatenate,
					'TimeEntryDate' => $TimeEntryDate,
					'TimeEntryModifiedDate' => $timeEntryModel->TimeEntryModifiedDate,
					'TimeEntryUserID' => $TimeCardTechID,
					'TimeEntryTimeCardID' => $id,
					'TimeEntryActivityID' => $timeEntryModel->TimeEntryActivityID,
					'TimeEntryComment' => $timeEntryModel->TimeEntryComment,
					'TimeEntryModifiedBy' => $timeEntryModel->TimeEntryModifiedBy,
				);

                Yii::trace("TIMEENTRYDATA: ".json_encode($time_entry_data));

				// check difference between startTime and endTime
				if ($endTimeObj > $startTimeObj) {
					$mileage_entry_data = array();
					$data[] = array(
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
						$response_activity = Parent::executePostRequest($url_send_activity, $json_data);
                        Yii::trace("RESPONSE ACTIVITY".$response_activity);
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

			// post url
			$putUrl = 'time-card%2Fapprove-cards';
			$putResponse = Parent::executePutRequest($putUrl, $json_data); // indirect rbac
			$obj = json_decode($putResponse, true);
			$responseTimeCardID = $obj[0]["TimeCardID"];
			return $this->redirect(['view', 'id' => $responseTimeCardID]);
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

		if (Yii::$app->request->isAjax) {
			
			try{
				
				$data = Yii::$app->request->post();					
				 // loop the data array to get all id's.	
				foreach ($data as $key) {
					foreach($key as $keyitem){
					
					   $TimeEntryIDArray[] = $keyitem;
					   Yii::Trace("TimeCardid is ; ". $keyitem);
					}
				}
				
				$data = array(
						'deactivatedBy' => Yii::$app->session['userID'],
						'entryArray' => $TimeEntryIDArray,
					);		
				$json_data = json_encode($data);
				
				// post url
				$putUrl = 'time-entry%2Fdeactivate';
				$putResponse = Parent::executePutRequest($putUrl, $json_data); // indirect rbac
				$obj = json_decode($putResponse, true);
				$responseTimeCardID = $obj[0]["TimeEntryTimeCardID"];
				return $this->redirect(['view', 'id' => $responseTimeCardID]);
				
			}catch(ErrorException $e){
				throw new \yii\web\HttpException(400);
			}
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
					$putResponse = Parent::executePutRequest($putUrl, $json_data); // indirect rbac
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
            Yii::trace("get called");
            // check if user has permission
            //self::SCRequirePermission("downloadTimeCardData");  // TODO check if this is the right permission

            //guest redirect
            if (Yii::$app->user->isGuest) {
                return $this->redirect(['/login']);
            }

            $model = new \yii\base\DynamicModel([
                'week'
            ]);
            $model->addRule('week', 'string', ['max' => 32]);

            $week = "current";

            if ($model->load(Yii::$app->request->queryParams,'')) {
                $week = $model->week;
            }

            $url = 'time-card%2Fget-time-cards-history-data&' . http_build_query([
                'week' => $week
                ]);

            header('Content-Disposition: attachment; filename="timecard_history_'.date('Y-m-d_h_i_s').'.csv"');
            $this->requestAndOutputCsv($url);
        } catch (ForbiddenHttpException $e) {
            throw new ForbiddenHttpException('You do not have adequate permissions to perform this action.');
        } catch (\Exception $e) {
            Yii::trace('EXCEPTION raised'.$e->getMessage());
            // Yii::$app->runAction('login/user-logout');
        }
    }

    /**
     * Calculate total work hours
     * @param $dataProvider
     * @return total work hours
     * @throws \yii\web\HttpException
     */
	 public function TotalHourCal($dataProvider){
		try{ 
			$Total_Work_Minutes = 0;
			foreach($dataProvider as $item){
				if($item["TimeEntryActiveFlag"] != "Inactive"){
					$Total_Work_Minutes += $item["TimeEntryMinutes"];
					Yii::Trace("item minutes is: ".$item["TimeEntryMinutes"]);
				}
			}

			return number_format ($Total_Work_Minutes / 60, 2);
		}catch(ErrorException $e){
				throw new \yii\web\HttpException(400);
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
        Parent::executeGetRequestToStream($url,$fp);
        rewind($fp);
        echo stream_get_contents($fp);
        fclose($fp);
    }
}
