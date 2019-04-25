<?php

namespace app\controllers;

use Yii;
use app\models\MileageCard;
use app\models\MileageCardSearch;
use app\models\MileageEntry;
use app\controllers\BaseController;
use yii\base\Exception;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\ServerErrorHttpException;
use yii\web\UnauthorizedHttpException;
use yii\filters\VerbFilter;
use yii\grid\GridView;
use yii\data\ArrayDataProvider;
use linslin\yii2\curl;
use yii\web\Request;
use \DateTime;
use yii\data\Pagination;
use yii\web\Response;

use app\constants\Constants;

/**
 * MileageCardController implements the CRUD actions for MileageCard model.
 */
class MileageCardController extends BaseCardController
{

    /**
     * Lists all MileageCard models.
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws \yii\web\HttpException
     */
    public function actionIndex($projectID = null, $projectFilterString = null)//variables for redirect defaults?
    {
        try {
			//guest redirect
			if (Yii::$app->user->isGuest) {
				return $this->redirect(['/login']);
			}
		
			//Check if user has permission to view mileage card page
			self::requirePermission("viewMileageCardMgmt");
			
			//if request is not coming from time-card reset session variables. 
			$referrer = Yii::$app->request->referrer;
			if(!strpos($referrer,'mileage-card')){
				unset(Yii::$app->session['mileageCardFormData']);
				unset(Yii::$app->session['mileageCardSort']);
			}

            //check user role
            $isAccountant = Yii::$app->session['UserAppRoleType'] == 'Accountant';
			$accountingSubmitReady = FALSE;
			$pmSubmitReady = FALSE;
			$isProjectManager = self::can("mileageCardPmSubmit");
            // Store start/end date data
            $dateData = [];
            $startDate = null;
            $endDate = null;
			
            $model = new \yii\base\DynamicModel([
                'dateRangePicker',
				'pageSize',
                'filter',
                'dateRangeValue',
				'projectID',
				'employeeID'
            ]);
			$model->addRule('dateRangePicker', 'string', ['max' => 32]);
            $model->addRule('pageSize', 'string', ['max' => 32]);//get page number and records per page
            $model->addRule('filter', 'string', ['max' => 100]);
            $model->addRule('dateRangeValue', 'string', ['max' => 100]);
			$model->addRule('projectID', 'integer'); //
            $model->addRule('employeeID', 'integer'); //
			
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
				Yii::$app->session['mileageCardSort'] = [
					'sortField' => $sortField,
					'sortOrder' => $sortOrder
				];
            } else {
				if(Yii::$app->session['mileageCardSort']){
					$sortField = Yii::$app->session['mileageCardSort']['sortField'];
					$sortOrder = Yii::$app->session['mileageCardSort']['sortOrder'];
				}else{
					//default sort values
					$sortField = ($isAccountant) ? 'ProjectName' : 'UserFullName';
					$sortOrder = 'ASC';
				}
            }
			
			// check if type was post, if so, get value from $model
            if ($model->load(Yii::$app->request->queryParams)){
				Yii::$app->session['mileageCardFormData'] = $model;
			}else{
				//set defaults to session data if avaliable
				if(Yii::$app->session['mileageCardFormData'])
				{
					$model = Yii::$app->session['mileageCardFormData'];
				}
				else
				{
					//set default values
					$model->pageSize = 50;
					$model->employeeID = '';
					$model->dateRangeValue = $currentWeek;
					$model->dateRangePicker	= null;
					//set filters if data passed from home screen
					$model->filter = $projectFilterString != null ? urldecode($projectFilterString): '';
					$model->projectID = $projectID != null ? $projectID : '';
				}
            }

            if ($model->dateRangeValue == "other") {
                if ($model->dateRangePicker == null){
                    $endDate = $startDate = date('Y-m-d');
                }else {
					//TODO extract this out to a higher level
                    $dateData = TimeCardController::dateRangeProcessor($model->dateRangePicker);
                    $startDate = $dateData[0];
                    $endDate = $dateData[1];
                }
            } else {
                $dateRangeArray = BaseController::splitDateRange($model->dateRangeValue);
                $startDate = $dateRangeArray['startDate'];
                $endDate =  $dateRangeArray['endDate'];
            }

            //check current page at
			if (isset($_GET['mileageCardPageNumber'])){
                $page = $_GET['mileageCardPageNumber'];
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
				'projectID' => $model->projectID,
				'employeeID' => $model->employeeID,
				'sortField' => $sortField,
				'sortOrder' => $sortOrder,
			]);
			//set url
			if($isAccountant)
				$url = 'mileage-card%2Fget-accountant-view&' . $httpQuery;
			else
				$url = 'mileage-card%2Fget-cards&' . $httpQuery;
			
			//execute request
            $response = Parent::executeGetRequest($url, Constants::API_VERSION_3); // indirect rbac
            $response = json_decode($response, true);
            $assets = $response['assets'];
			
			//extract format indicators from response data
			$unapprovedMileageCardExist = array_key_exists('unapprovedMileageCardExist', $response) ? $response['unapprovedMileageCardExist'] : false;
            $showFilter = $response['showProjectDropDown'];
            $projectWasSubmitted = $response['projectSubmitted'];
			$projectDropDown = $response['projectDropDown'];
			$isAccountant ? $employeeDropDown = [] : $employeeDropDown = $response['employeeDropDown'];

			//get submit button status
			$isSubmitReady = self::getSubmitButtonStatus($model->projectID, $projectDropDown, $startDate, $endDate, $isAccountant);
	
			//set submit button status
			if($isAccountant)
				$accountingSubmitReady = $isSubmitReady;
			else
				$pmSubmitReady = $isSubmitReady;

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
				// Sorting Accountant MileageCard table
				$dataProvider->sort = [
					'defaultOrder' => [$sortField => ($sortOrder == 'ASC') ? SORT_ASC : SORT_DESC],
					'attributes' => [
						'ProjectName',
						'ProjectManager',
						'StartDate',
						'EndDate',
						'ApprovedBy',
						'OasisSubmitted',
						'MSDynamicsSubmitted',
						'ADPSubmitted'
					]
				];
			} else {
				// Sorting Base MileageCard table
				$dataProvider->sort = [
					'defaultOrder' => [$sortField => ($sortOrder == 'ASC') ? SORT_ASC : SORT_DESC],
					'attributes' => [
						'UserFullName',
						'ProjectName',
						'MileageCardDates',
						'MileageCardOasisSubmitted',
						'MileageCardQBSubmitted',
						'SumMiles',
						'MileageCardApprovedFlag',
						'MileageCardPMApprovedFlag'
					]
				];

				//set timecardid as id
				$dataProvider->key = 'MileageCardID';
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
				'unapprovedMileageCardExist' => $unapprovedMileageCardExist,
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
        } catch (ForbiddenHttpException $e) {
            throw $e;
        } catch (ErrorException $e) {
            throw new \yii\web\HttpException(400);
        } catch (Exception $e) {
            throw new ServerErrorHttpException();
        }
    }

	/**
	 *Displays mileage card details for expanded project
	 *@throws \yii\web\HttpException
	 *@returns mixed
	 */
	public function actionViewAccountantDetail()
	{
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

			$getUrl = 'mileage-card%2Fget-accountant-details&' . http_build_query([
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
				'key' => 'MileageCardProjectID',
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
    public function actionShowEntries($id, $projectName = null, $fName = null, $lName = null, $mileageCardProjectID = null)
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
			self::requirePermission("mileageCardGetEntries");
			
			//build api url paths
			$entries_url = 'mileage-card%2Fshow-entries&' . http_build_query([
				'cardID' => $id,
			]);
			$resp = Parent::executeGetRequest($entries_url, Constants::API_VERSION_3); // rbac check
			$cardData = json_decode($resp, true);
			
			//populate required values if not received from function call
			$mileageCardProjectID = $mileageCardProjectID != null ? $mileageCardProjectID : $cardData['card']['mileageCardProjectID'];
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
			
			//add empty row if no netries exist
			if(count($cardData['show-entries']) == 1){
				$cardData['show-entries'][] = [
					'Task' => 'MileageActivity',
					'Date1' => '',
					'Date2' => '',
					'Date3' => '',
					'Date4' => '',
					'Date5' => '',
					'Date6' => '',
					'Date7' => '',
				];
			}

			$allTask = new ArrayDataProvider([
				'allModels' => $cardData['show-entries'],
				'pagination'=> false,
			]);
			
			//data to pass to view
			$dataArray = [
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
				'mileageCardProjectID' => $mileageCardProjectID,
				'isSubmitted' => $cardData['card']['MileageCardOasisSubmitted']=='Yes',
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
	 * removed useage of this route 3/12/19
     * deactivate Multiple existing Mileage Card(s)
     * If deactivate is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     * @throws \yii\web\BadRequestHttpException
     * @throws Exception redirect user to mileage card index page
     */
    // public function actionDeactivate()
    // {
        // try {
            // $data = Yii::$app->request->post();	
			// $jsonData = json_encode($data);
			
			// // post url
			// $putUrl = 'mileage-entry%2Fdeactivate';
			// $putResponse = Parent::executePutRequest($putUrl, $jsonData,Constants::API_VERSION_3); // indirect rbac
			// $obj = json_decode($putResponse, true);	
        // } catch (UnauthorizedHttpException $e){
            // Yii::$app->response->redirect(['login/index']);
        // } catch(ForbiddenHttpException $e) {
            // throw $e;
        // } catch(ErrorException $e) {
            // throw new \yii\web\HttpException(400);
        // } catch(Exception $e) {
            // throw new ServerErrorHttpException();
        // }
    // }
	
	/**
     * render Image
     * @param null $photoPath
     * @return string|\yii\web\Response
     */
    public function actionViewImage($photoPath = null){
        // Verify logged in
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['/login']);
        }
        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('viewImage', [
                'photoPath' => $photoPath
            ]);
        } else {
            return $this->render('viewImage', [
                'photoPath' => $photoPath
            ]);
        }
    }
}
