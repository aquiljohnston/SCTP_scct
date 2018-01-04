<?php

namespace app\controllers;

use Yii;
use app\models\MileageCard;
use app\models\MileageCardSearch;
use app\models\MileageEntry;
use app\controllers\BaseController;
use yii\base\Exception;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\grid\GridView;
use yii\data\ArrayDataProvider;
use linslin\yii2\curl;
use yii\web\Request;
use yii\web\ForbiddenHttpException;
use \DateTime;
use yii\data\Pagination;
use yii\web\Response;
use yii\web\ServerErrorHttpException;
use app\constants\Constants;

/**
 * MileageCardController implements the CRUD actions for MileageCard model.
 */
class MileageCardController extends BaseController
{

    /**
     * Lists all MileageCard models.
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws \yii\web\HttpException
     */
    public function actionIndex()
    {
        //guest redirect
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['/login']);
        }
        try {
			//Check if user has permission to view mileage card page
			self::requirePermission("viewMileageCardMgmt");
            $model = new \yii\base\DynamicModel([
                'DateRangePicker',
				'pagesize',
                'filter',
                'dateRangeValue'
            ]);
			$model->addRule('DateRangePicker', 'string', ['max' => 30]);
            $model->addRule('pagesize', 'string', ['max' => 32]);//get page number and records per page
            $model->addRule('filter', 'string', ['max' => 100]);
            $model->addRule('dateRangeValue', 'string', ['max' => 30]);
			
			//get current and prior weeks date range
			$today = BaseController::getDate();
			$priorWeek = BaseController::getWeekBeginEnd("$today -1 week");
			$currentWeek = BaseController::getWeekBeginEnd($today);
            $other = "other";

            // Store start/end date data
            $dateData = [];
            $startDate = null;
            $endDate = null;
			
			//create default prior/current week values
			$dateRangeDD = [
			$priorWeek => 'Prior Week',
			$currentWeek => 'Current Week',
            $other => 'Other'
			];
			
			//check current filtering values
            if ($model->load(Yii::$app->request->queryParams)) {
                $mileageCardPageSizeParams = $model->pagesize;
                $filter = $model->filter;
				$dateRangeValue = $model->dateRangeValue;
                $dateRangePicker = $model->DateRangePicker;

            } else {
                $mileageCardPageSizeParams = 50;
                $filter = "";
				$dateRangeValue = $priorWeek;
                $dateRangePicker = null;
            }

            if ($dateRangeValue == "other") {
                if ($dateRangePicker == null){
                    $endDate = $startDate = date('Y-m-d');
                }else {
                    $dateData = TimeCardController::dateRangeProcessor($dateRangePicker);
                    $startDate = $dateData[0];
                    $endDate = $dateData[1];
                }
            } else {
                $dateRangeArray = BaseController::splitDateRange($dateRangeValue);
                $startDate = $dateRangeArray['startDate'];
                $endDate =  $dateRangeArray['endDate'];
            }

            Yii::trace("START DATE: ".$startDate);
            Yii::trace("END DATE: ".$endDate);

            //check current page at
			if (isset($_GET['mileageCardPageNumber'])){
                $page = $_GET['mileageCardPageNumber'];
            } else {
                $page = 1;
            }
			
			$encodedFilter = urlencode($filter);

            //build url with params
            $url = "mileage-card%2Fget-cards&startDate=$startDate&endDate=$endDate&filter=$encodedFilter&listPerPage=$mileageCardPageSizeParams&page=$page";
            $response = Parent::executeGetRequest($url, Constants::API_VERSION_2); // indirect rbac
            $response = json_decode($response, true);
            $assets = $response['assets'];

            // passing decode data into dataProvider
            $dataProvider = new ArrayDataProvider
            ([
                'allModels' => $assets,
                'pagination' => false
            ]);

            // Sorting MileageCard table
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
                    'MileageStartDate' => [
                        'asc' => ['MileageStartDate' => SORT_ASC],
                        'desc' => ['MileageStartDate' => SORT_DESC]
                    ],
                    'MileageEndDate' => [
                        'asc' => ['MileageEndDate' => SORT_ASC],
                        'desc' => ['MileageEndDate' => SORT_DESC]
                    ],
                    'SumMiles' => [
                        'asc' => ['SumMiles' => SORT_ASC],
                        'desc' => ['SumMiles' => SORT_DESC]
                    ]
                ]
            ];

            //Set Mile Card ID On the JS Call
            $dataProvider->key = 'MileageCardID';

            // set pages to dispatch table
            $pages = new Pagination($response['pages']);

            //calling index page to pass dataProvider.
			if (Yii::$app->request->isAjax) {
				 return $this->renderAjax('index', [
					'dataProvider' => $dataProvider,
					'dateRangeDD' => $dateRangeDD,
					'dateRangeValue' => $dateRangeValue,
					'mileageCardPageSizeParams' => $mileageCardPageSizeParams,
					'pages' => $pages,
					'model' => $model,
					'mileageCardFilterParams' => $filter
				]);
			}else{
				return $this->render('index', [
					'dataProvider' => $dataProvider,
					'dateRangeDD' => $dateRangeDD,
					'dateRangeValue' => $dateRangeValue,
					'mileageCardPageSizeParams' => $mileageCardPageSizeParams,
					'pages' => $pages,
					'model' => $model,
					'mileageCardFilterParams' => $filter
				]); 
			}
        } catch (UnauthorizedHttpException $e){
            Yii::$app->response->redirect(['login/index']);
        } catch (ForbiddenHttpException $e) {
            throw $e;
        } catch (ErrorException $e) {
            throw new \yii\web\HttpException(400);
        } catch (Exception $e) {
            throw new ServerErrorHttpException();
        }
    }

    /**
     * Displays a single MileageCard model.
     * @param integer $id
     * @return mixed
     * @throws \yii\web\HttpException
     */
    public function actionView($id)
    {
        //guest redirect
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['/login']);
        }

        try {
            // set default value 0 to duplicateFlag
            // duplicationflag:
            // 1: yes 0: no
            // set duplicateFlag to 1, which means duplication happened.

            $duplicateFlag = 0;
            if (strpos($id, "yes") == true) {
                $id = str_replace("yes", "", $id);
                $duplicateFlag = 1;
            }
            $url = 'mileage-card%2Fget-entries&cardID=' . $id;
            $mileage_card_url = 'mileage-card%2Fview&id=' . $id;

            //Indirect RBAC checks
            $response = Parent::executeGetRequest($url, Constants::API_VERSION_2);
            $mileage_card_response = Parent::executeGetRequest($mileage_card_url, Constants::API_VERSION_2);
            $model = json_decode($mileage_card_response, true);
            $entryData = json_decode($response, true);
            $ApprovedFlag = $entryData['ApprovedFlag'];
			
            $Sundaydata = $entryData['MileageEntries']['Sunday']['Entries'];
            $SundayProvider = new ArrayDataProvider([
                'allModels' => $Sundaydata,
                'pagination' => false,
            ]);
            $Total_Mileage_Sun = $entryData['MileageEntries']['Sunday']['Total'];

            $Mondaydata = $entryData['MileageEntries']['Monday']['Entries'];
            $MondayProvider = new ArrayDataProvider([
                'allModels' => $Mondaydata,
                'pagination' => false,
            ]);
            $Total_Mileage_Mon = $entryData['MileageEntries']['Monday']['Total'];

            $Tuesdaydata = $entryData['MileageEntries']['Tuesday']['Entries'];
            $TuesdayProvider = new ArrayDataProvider([
                'allModels' => $Tuesdaydata,
                'pagination' => false,
            ]);
            $Total_Mileage_Tue = $entryData['MileageEntries']['Tuesday']['Total'];

            $Wednesdaydata = $entryData['MileageEntries']['Wednesday']['Entries'];
            $WednesdayProvider = new ArrayDataProvider([
                'allModels' => $Wednesdaydata,
                'pagination' => false,
            ]);
            $Total_Mileage_Wed = $entryData['MileageEntries']['Wednesday']['Total'];

            $Thursdaydata = $entryData['MileageEntries']['Thursday']['Entries'];
            $ThursdayProvider = new ArrayDataProvider([
                'allModels' => $Thursdaydata,
                'pagination' => false,
            ]);
            $Total_Mileage_Thr = $entryData['MileageEntries']['Thursday']['Total'];

            $Fridaydata = $entryData['MileageEntries']['Friday']['Entries'];
            $FridayProvider = new ArrayDataProvider([
                'allModels' => $Fridaydata,
                'pagination' => false,
            ]);
            $Total_Mileage_Fri = $entryData['MileageEntries']['Friday']['Total'];

            $Saturdaydata = $entryData['MileageEntries']['Saturday']['Entries'];
            $SaturdayProvider = new ArrayDataProvider([
                'allModels' => $Saturdaydata,
                'pagination' => false,
            ]);
            $Total_Mileage_Sat = $entryData['MileageEntries']['Saturday']['Total'];

            //calculation total miles for this mileage card
            $Total_Mileage_Current_MileageCard = $Total_Mileage_Sun +
                $Total_Mileage_Mon +
                $Total_Mileage_Tue +
                $Total_Mileage_Wed +
                $Total_Mileage_Thr +
                $Total_Mileage_Fri +
                $Total_Mileage_Sat;

            //set MileageEntryID as id
            $SundayProvider->key = 'MileageEntryID';
            $MondayProvider->key = 'MileageEntryID';
            $TuesdayProvider->key = 'MileageEntryID';
            $WednesdayProvider->key = 'MileageEntryID';
            $ThursdayProvider->key = 'MileageEntryID';
            $FridayProvider->key = 'MileageEntryID';
            $SaturdayProvider->key = 'MileageEntryID';

            return $this->render('view', [
                'model' => $model,
                'duplicateFlag' => $duplicateFlag,
                'ApprovedFlag' => $ApprovedFlag,
                'Total_Mileage_Current_MileageCard' => $Total_Mileage_Current_MileageCard,
                'SundayProvider' => $SundayProvider,
                'Total_Mileage_Sun' => $Total_Mileage_Sun,
                'MondayProvider' => $MondayProvider,
                'Total_Mileage_Mon' => $Total_Mileage_Mon,
                'TuesdayProvider' => $TuesdayProvider,
                'Total_Mileage_Tue' => $Total_Mileage_Tue,
                'WednesdayProvider' => $WednesdayProvider,
                'Total_Mileage_Wed' => $Total_Mileage_Wed,
                'ThursdayProvider' => $ThursdayProvider,
                'Total_Mileage_Thr' => $Total_Mileage_Thr,
                'FridayProvider' => $FridayProvider,
                'Total_Mileage_Fri' => $Total_Mileage_Fri,
                'SaturdayProvider' => $SaturdayProvider,
                'Total_Mileage_Sat' => $Total_Mileage_Sat,

            ]);
        } catch (ErrorException $e) {
            throw new \yii\web\HttpException(400);
        }
    }

    /**
     * Creates a new MileageEntry model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param $mileageCardId
     * @param $mileageCardTechId
     * @param $mileageCardDate
     * @return mixed
     */
    public function actionCreateMileageEntry($mileageCardId, $mileageCardTechId, $mileageCardDate)
    {
        //guest redirect
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['/login']);
        }
        //RBAC Check
        self::requirePermission("mileageEntryCreate");
        $model = new MileageEntry();

        //GET DATA TO FILL FORM DROPDOWNS
        $activityCodeUrl = "activity-code%2Fget-code-dropdowns";
        $activityCodeResponse = Parent::executeGetRequest($activityCodeUrl);
        $activityCode = json_decode($activityCodeResponse, true);

        if ($model->load(Yii::$app->request->post())) {
            //create timeEntryTitle variable
            $mileageEntryTitle = "mileageEntry";

            //concatenate start time
            $MileageEntryStartTimeConcatenate = new DateTime($mileageCardDate . $model->MileageEntryStartDate);
            $MileageEntryStartTimeConcatenate = $MileageEntryStartTimeConcatenate->format('Y-m-d H:i:s');

            //concatenate end time
            $MileageEntryEndTimeConcatenate = new DateTime($mileageCardDate . $model->MileageEntryEndDate);
            $MileageEntryEndTimeConcatenate = $MileageEntryEndTimeConcatenate->format('Y-m-d H:i:s');

            // check user input validate StartingMileage and EndingMileage
            // MileageEntryStartTime and MileageEntryEndTime
            $startMileageObj = $model->MileageEntryStartingMileage;
            $endMileageObj = $model->MileageEntryEndingMileage;
            $CheckStartTime = $MileageEntryStartTimeConcatenate;
            $CheckEndTime = $MileageEntryEndTimeConcatenate;
            $startTimeObj = new DateTime($CheckStartTime);
            $endTimeObj = new DateTime($CheckEndTime);

            $mileage_entry_data[] = array(
                'MileageEntryUserID' => $mileageCardTechId,
                'MileageEntryStartingMileage' => $model->MileageEntryStartingMileage,
                'MileageEntryEndingMileage' => $model->MileageEntryEndingMileage,
                'MileageEntryStartDate' => $MileageEntryStartTimeConcatenate,
                'MileageEntryEndDate' => $MileageEntryEndTimeConcatenate,
                'MileageEntryType' => '0', //Automatically set the mileage entry type to 0 for BUSINESS - Andre V.
                'MileageEntryActivityID' => '3', //Automatically set the mileage entry activity type to 3 for PRODUCTION - Andre V.
                'MileageEntryMileageCardID' => $mileageCardId,
                'MileageEntryApprovedBy' => $model->MileageEntryApprovedBy,
                'MileageEntryComment' => $model->MileageEntryComment,
            );

            // check difference between StartingMileage and EndingMileage
            if ($endTimeObj > $startTimeObj && $endMileageObj > $startMileageObj) {
                $time_entry_data = array();
                $data[] = array(
                    'ActivityUID' => BaseController::generateUID($mileageEntryTitle),
                    'ActivityTitle' => $mileageEntryTitle,
                    'ActivityCreatedBy' => Yii::$app->session['userID'],
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

                    return $this->redirect(['view', 'id' => $obj["activity"][0]["mileageEntry"][0]["MileageEntryMileageCardID"]]);
                } catch (\Exception $e) {
                    $concatenate_id = $mileageCardId . "yes";
                    return $this->redirect(['view', 'id' => $concatenate_id]);
                }

            } else {
                return $this->redirect(['view', 'id' => $mileageCardId]);
            }
            /*
            $json_data = json_encode($data);

            //Execute the POST call
            $url_send = "mileage-entry%2Fcreate";
            Parent::executePostRequest($url_send, $json_data);

            return $this->redirect(['view', 'id' => $mileageCardId]);
            */
        } else {
            return $this->render('create_mileage_entry', [
                'model' => $model,
                'activityCode' => $activityCode,
            ]);
        }

    }

    /**
     * Approve an existing MileageCard.
     * If approve is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     * @throws Exception redirect user to the current mileage entry page
     */
    public function actionApprove($id)
    {
        try {
            //guest redirect
            if (Yii::$app->user->isGuest) {
                return $this->redirect(['/login']);
            }
            $cardIDArray[] = $id;

            $data = array(
                'cardIDArray' => $cardIDArray,
            );
            $json_data = json_encode($data);

            // post url
            $putUrl = 'mileage-card%2Fapprove-cards';
            $putResponse = Parent::executePutRequest($putUrl, $json_data, Constants::API_VERSION_2);  // indirect RBAC
            $obj = json_decode($putResponse, true);
            $responseMileageCardID = $obj[0]["MileageCardID"];
            return $this->redirect(['view', 'id' => $responseMileageCardID]);
        } catch (\Exception $e){
            return $this->redirect(['view', 'id' => $id]);
        }
    }

    /**
     * deactivate Multiple existing Mileage Card(s)
     * If deactivate is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     * @throws \yii\web\BadRequestHttpException
     * @throws Exception redirect user to mileage card index page
     */
    public function actionDeactivate()
    {
        try {
            if (Yii::$app->request->isAjax) {
                $data = Yii::$app->request->post();

                // loop the data array to get all id's.
                foreach ($data as $key) {
                    foreach ($key as $keyitem) {

                        $MileageEntryIDArray[] = $keyitem;
                    }
                }

                $data = array(
                    'entryArray' => $MileageEntryIDArray,
                );
                $json_data = json_encode($data);

                // post url
                $putUrl = 'mileage-entry%2Fdeactivate';
                $putResponse = Parent::executePutRequest($putUrl, $json_data, Constants::API_VERSION_2);
                $obj = json_decode($putResponse, true);
                $responseMileageCardID = $obj[0]["MileageEntryMileageCardID"];
                return $this->redirect(['view', 'id' => $responseMileageCardID]);

            } else {
                throw new \yii\web\BadRequestHttpException;
            }
        } catch (\Exception $e){
            return $this->redirect(['index']);
        }
    }

    /**
     * Approve Multiple existing Mileage Card(s)
     * If approve is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     * @throws \yii\web\BadRequestHttpException
     * @throws Exception redirect user to mileage card index page
     */
    public function actionApproveMultiple()
    {
        try {
            if (Yii::$app->request->isAjax) {
                $data = Yii::$app->request->post();

                // loop the data array to get all id's.
                foreach ($data as $key) {
                    foreach ($key as $keyitem) {

                        $MileageCardIDArray[] = $keyitem;
                    }
                }

                $data = array(
                    'cardIDArray' => $MileageCardIDArray,
                );
                $json_data = json_encode($data);

                // post url
                $putUrl = 'mileage-card%2Fapprove-cards';
                $apiResponse = parent::executePutRequest($putUrl, $json_data, Constants::API_VERSION_2); //indirect rbac
                $json_response_data = json_decode($apiResponse, true);
                //create response
                $response = Yii::$app->response;
                $response->format = Response::FORMAT_JSON;
                if ($json_response_data['success']) {
                    $data = [];
                    $data['cards'] = $json_response_data['cards'];
                    $data['status'] = "200 Success";
                    $data['success'] = true;
                    $response->data = $data;
                } else {
                    $data = [];
                    $data['cards'] = null;
                    $data['status'] = "400 Bad Request";
                    $data['success'] = false;
                    $response->data = $data;
                }
                return $response;
            } else {
                throw new \yii\web\BadRequestHttpException;
            }
        } catch (\Exception $e){
            return $this->redirect(['index']);
        }
    }

    /**
     * Get MileageCard History Data
     * @return \yii\web\Response
     * @throws ForbiddenHttpException
     */
    public function actionDownloadMileageCardData() {
        try {
            // check if user has permission
            //self::SCRequirePermission("downloadTimeCardData");  // TODO check if this is the right permission

            //guest redirect
            if (Yii::$app->user->isGuest) {
                return $this->redirect(['/login']);
            }

            $model = new \yii\base\DynamicModel([
                'dateRange'
            ]);
            $model->addRule('dateRange', 'string', ['max' => 64]);

            if ($model->load(Yii::$app->request->queryParams,'')) {
                $dateRange = $model->dateRange;
            }
			
			$dateRangeArray = BaseController::splitDateRange($dateRange);
			$startDate = $dateRangeArray['startDate'];
			$endDate =  $dateRangeArray['endDate'];

            $url = "mileage-card%2Fget-cards-export&startDate=$startDate&endDate=$endDate";
			
            header('Content-Disposition: attachment; filename="mileagecard_history_'.date('Y-m-d_h_i_s').'.csv"');
            $this->requestAndOutputCsv($url);
        } catch (ForbiddenHttpException $e) {
            throw new ForbiddenHttpException('You do not have adequate permissions to perform this action.');
        } catch (\Exception $e) {
            //Yii::trace('EXCEPTION raised'.$e->getMessage());
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
}
