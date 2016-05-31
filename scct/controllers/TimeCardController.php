<?php

namespace app\controllers;

use app\models\Activity;
use Yii;
use app\models\TimeCard;
use app\models\TimeCardSearch;
use app\models\TimeEntry;
use app\controllers\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ArrayDataProvider;
use linslin\yii2\curl;
use yii\web\Request;
use \DateTime;
use yii\web\ForbiddenHttpException;
use yii\base\Model;

/**
 * TimeCardController implements the CRUD actions for TimeCard model.
 */
class TimeCardController extends BaseController
{
    /**
     * Lists all TimeCard models.
     * @return mixed
     */
    public function actionIndex()
    {
		//guest redirect
		if (Yii::$app->user->isGuest)
		{
			return $this->redirect(['login/login']);
		}
		//RBAC permissions check
		if (Yii::$app->user->can('viewTimeCardIndex'))
		{
			try{
					// create curl for restful call.
					//get user role
					$userID = Yii::$app->session['userID'];
					$userRole = Yii::$app->authManager->getRolesByUser($userID);
					$role = current($userRole);
					//get week		
					$week = Yii::$app->request->getQueryParam("week");
					//check role and use appropriate routes
					if($role->name == "Admin"){	
						// If week is undefined then the current week route will be chosen
						if($week=="prior") {
							$url = "http://api.southerncrossinc.com/index.php?r=time-card%2Fview-time-card-hours-worked-prior";
						} else {
							$url = "http://api.southerncrossinc.com/index.php?r=time-card%2Fview-time-card-hours-worked-current";
						}
					} else {
						// If week is undefined then the current week route will be chosen
						if($week=="prior") {
							$url = "http://api.southerncrossinc.com/index.php?r=time-card%2Fview-all-by-user-by-project-prior&userID=" . $userID;
						} else {
							$url = "http://api.southerncrossinc.com/index.php?r=time-card%2Fview-all-by-user-by-project-current&userID=" . $userID;
						}
					}
					$response = Parent::executeGetRequest($url);
					$filteredResultData = $this->filterColumn(json_decode($response, true), 'UserFirstName', 'filterfirstname');
					$filteredResultData = $this->filterColumn($filteredResultData, 'UserLastName', 'filterlastname');
					$filteredResultData = $this->filterColumnMultiple($filteredResultData, 'ProjectName', 'filterprojectname');
					$filteredResultData = $this->filterColumn($filteredResultData, 'TimeCardApprovedFlag', 'filterapproved');
					// passing decode data into dataProvider
					$dataProvider = new ArrayDataProvider
					([
						'allModels' => $filteredResultData,
						/*'key' => function (){
							return md5($model["TimeCardID"]);
						},*/
						'pagination' => [
							'pageSize' => 100,
						],
					]);
					
					//set timecardid as id
					// 
					$dataProvider->key ='TimeCardID';

					$searchModel = [
						'UserFirstName' => Yii::$app->request->getQueryParam('filterfirstname', ''),
						'UserLastName' => Yii::$app->request->getQueryParam('filterlastname', ''),
						'ProjectName' => Yii::$app->request->getQueryParam('filterprojectname', ''),
						'TimeCardApprovedFlag' => Yii::$app->request->getQueryParam('filterapproved', '')
					];

					// Create drop down with current selection pre-selected based on GET variable
					$approvedInput = '<select class="form-control" name="filterapproved">'
						. '<option value=""></option><option value="Yes"';
					if($searchModel['TimeCardApprovedFlag'] == "Yes") {
						$approvedInput.= " selected";
					}
					$approvedInput .= '>Yes</option><option value="No"';
					if($searchModel['TimeCardApprovedFlag'] == "No") {
						$approvedInput .= ' selected';
					}
					$approvedInput .= '>No</option></select>';
					
					//calling index page to pass dataProvider.
					return $this->render('index', [
						'dataProvider' => $dataProvider,
						'searchModel' => $searchModel,
						'approvedInput' => $approvedInput,
						'week' => $week
					]);
				
			}catch(ErrorException $e){
				throw new \yii\web\HttpException(400);
			}
		}
		else
		{
			throw new ForbiddenHttpException('You do not have adequate permissions to perform this action.');
		}
    }

    /**
     * Displays a single TimeCard model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {		
		//guest redirect
		if (Yii::$app->user->isGuest)
		{
			return $this->redirect(['login/login']);
		}
		//RBAC permissions check
		if (Yii::$app->user->can('viewTimeCard'))
		{
			try{
				
					$url = 'http://api.southerncrossinc.com/index.php?r=time-card%2Fview-time-entries&id='.$id;
					$time_card_url = 'http://api.southerncrossinc.com/index.php?r=time-card%2Fview&id='.$id;
					
					$response = Parent::executeGetRequest($url);
					$time_card_response = Parent::executeGetRequest($time_card_url);
					$model = json_decode($time_card_response, true);
					$dateProvider = json_decode($response, true);
					$ApprovedFlag = $dateProvider["ApprovedFlag"];
					$Sundaydata = $dateProvider["TimeEntries"][0]["Sunday"];
					$SundayProvider = new ArrayDataProvider([
						'allModels' => $Sundaydata,
						'pagination' => [
							'pageSize' => 10,
						],
						// 'sort' => [
							// 'attributes' => ['id', 'name'],
						// ],
					]);
					$Total_Hours_Sun = $this->TotalHourCal($Sundaydata);
					
					$Mondaydata = $dateProvider["TimeEntries"][0]["Monday"];
					$MondayProvider = new ArrayDataProvider([
						'allModels' => $Mondaydata,
						'pagination' => [
							'pageSize' => 10,
						],
						// 'sort' => [
							// 'attributes' => ['id', 'name'],
						// ],
					]);
					$Total_Hours_Mon = $this->TotalHourCal($Mondaydata);
					
					$Tuesdaydata = $dateProvider["TimeEntries"][0]["Tuesday"];
					$TuesdayProvider = new ArrayDataProvider([
						'allModels' => $Tuesdaydata,
						'pagination' => [
							'pageSize' => 10,
						],
						// 'sort' => [
							// 'attributes' => ['id', 'name'],
						// ],
					]);
					$Total_Hours_Tue = $this->TotalHourCal($Tuesdaydata);
					
					$Wednesdaydata = $dateProvider["TimeEntries"][0]["Wednesday"];
					$WednesdayProvider = new ArrayDataProvider([
						'allModels' => $Wednesdaydata,
						'pagination' => [
							'pageSize' => 10,
						],
						// 'sort' => [
							// 'attributes' => ['id', 'name'],
						// ],
					]);
					$Total_Hours_Wed = $this->TotalHourCal($Wednesdaydata);
					
					$Thursdaydata = $dateProvider["TimeEntries"][0]["Thursday"];
					$ThursdayProvider = new ArrayDataProvider([
						'allModels' => $Thursdaydata,
						'pagination' => [
							'pageSize' => 10,
						],
						// 'sort' => [
							// 'attributes' => ['id', 'name'],
						// ],
					]);
					$Total_Hours_Thu = $this->TotalHourCal($Thursdaydata);
					
					$Fridaydata = $dateProvider["TimeEntries"][0]["Friday"];
					$FridayProvider = new ArrayDataProvider([
						'allModels' => $Fridaydata,
						'pagination' => [
							'pageSize' => 10,
						],
						// 'sort' => [
							// 'attributes' => ['id', 'name'],
						// ],
					]);
					$Total_Hours_Fri = $this->TotalHourCal($Fridaydata);
					
					$Saturdaydata = $dateProvider["TimeEntries"][0]["Saturday"];
					$SaturdayProvider = new ArrayDataProvider([
						'allModels' => $Saturdaydata,
						'pagination' => [
							'pageSize' => 10,
						],
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
		else
		{
			throw new ForbiddenHttpException('You do not have adequate permissions to perform this action.');
		}
    }
	
	/**
     * Displays a single TimeEntry model.
     * @param integer $id
     * @return mixed
     */
    public function actionViewTE($id)
    {
		//guest redirect
		if (Yii::$app->user->isGuest)
		{
			return $this->redirect(['login/login']);
		}
		//RBAC permissions check
		if (Yii::$app->user->can('viewTimeEntry'))
		{
			try{
					$url = 'http://api.southerncrossinc.com/index.php?r=time-entry%2Fview&id='.$id;
					$response = Parent::executeGetRequest($url);

					return $this -> render('view', ['model' => json_decode($response), true]);
			}catch(ErrorException $e){
				throw new \yii\web\HttpException(400);
			}
		}
		else
		{
			throw new ForbiddenHttpException('You do not have adequate permissions to perform this action.');
		}
    }
	
    /**
     * Creates a new TimeCard model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
		//guest redirect
		if (Yii::$app->user->isGuest)
		{
			return $this->redirect(['login/login']);
		}
		//RBAC permissions check
		if (Yii::$app->user->can('createTimeCard'))
		{
			try{
				
					$model = new \yii\base\DynamicModel([
						'TimeCardStartDate', 'TimeCardEndDate', 'TimeCardCreateDate', 'TimeCardModifiedDate', 'TimeCardProjectID', 
						'TimeCardTechID', 'TimeCardApprovedBy', 'TimeCardApprovedFlag', 'TimeCardSupervisorName', 'TimeCardComment', 'TimeCardCreatedBy', 
						'TimeCardModifiedBy', 'isNewRecord'
					]);
					
					$model->addRule('TimeCardStartDate', 'safe')
						  ->addRule('TimeCardEndDate', 'safe')
						  ->addRule('TimeCardCreateDate', 'safe')
						  ->addRule('TimeCardModifiedDate', 'safe')
						  ->addRule('TimeCardProjectID', 'integer')
						  ->addRule('TimeCardTechID', 'integer')
						  ->addRule('TimeCardApprovedBy', 'string')
						  ->addRule('TimeCardApprovedFlag', 'integer')
						  ->addRule('TimeCardSupervisorName', 'string')
						  ->addRule('TimeCardComment', 'string')
						  ->addRule('TimeCardCreatedBy', 'string')
						  ->addRule('TimeCardModifiedBy', 'string');
					
					// create curl object
					$curl = new curl\Curl();
					
					// post url
					$url_send = "http://api.southerncrossinc.com/index.php?r=time-card%2Fcreate";
					
					if ($model->load(Yii::$app->request->post())) {
						
						$data = array(
							'TimeCardStartDate' => $model->TimeCardStartDate,
							'TimeCardEndDate' => $model->TimeCardEndDate,
							'TimeCardProjectID' => $model->TimeCardProjectID,
							'TimeCardTechID' => $model->TimeCardTechID,
							'TimeCardApprovedBy' => $model->TimeCardApprovedBy,
							'TimeCardApprovedFlag' => $model->TimeCardApprovedFlag,
							'TimeCardSupervisorName' => $model->TimeCardSupervisorName,
							'TimeCardComment' => $model->TimeCardComment,
							'TimeCardCreateDate' => $model->TimeCardCreateDate,
							'TimeCardCreatedBy' => $model->TimeCardCreatedBy,
							'TimeCardModifiedDate' => $model->TimeCardModifiedDate,
							'TimeCardModifiedBy' => $model->TimeCardModifiedBy,
						);
						
						$json_data = json_encode($data);		
						$ch = curl_init($url_send);
						curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"POST");
						curl_setopt($ch, CURLOPT_POSTFIELDS,$json_data);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($ch, CURLOPT_HTTPHEADER, array(
							'Content-Type: application/json',
							'Content-Length: ' . strlen($json_data))
						);			
						$result = curl_exec($ch);
						curl_close($ch);
						//var_dump($result);
						$obj = (array)json_decode($result);
						//var_dump($obj["TimeCardID"]);
						return $this->redirect(['view', 'id' => $obj["TimeCardID"]]);
					} else {
						return $this->render('create', [
							'model' => $model,
						]);
					}
			}catch(ErrorException $e){
				throw new \yii\web\HttpException(400);
			}
		}
		else
		{
			throw new ForbiddenHttpException('You do not have adequate permissions to perform this action.');
		}
    }

	/**
     * Creates a new TimeEntry model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
	public function actionCreatee($id, $TimeCardTechID, $TimeEntryDate)
    {
		//guest redirect
		if (Yii::$app->user->isGuest)
		{
			return $this->redirect(['login/login']);
		}
		//RBAC permissions check
		if (Yii::$app->user->can('createTimeCard'))
		{	
			try{
				
					$timeEntryModel = new TimeEntry();
					$activityModel = new Activity();
					//generate array for Active Flag dropdown
					$flag = 
					[
						1 => "Active",
						0 => "Inactive",
					];
					$obj = "";
					
					//GET DATA TO FILL FORM DROPDOWNS//
					//get clients for form dropdown
					$activityCodeUrl = "http://api.southerncrossinc.com/index.php?r=activity-code%2Fget-code-dropdowns";
					$activityCodeResponse = Parent::executeGetRequest($activityCodeUrl);
					$activityCode = json_decode($activityCodeResponse, true);
					
					$activityPayCodeUrl = "http://api.southerncrossinc.com/index.php?r=pay-code%2Fget-code-dropdowns";
					$activityPayCodeResponse = Parent::executeGetRequest($activityPayCodeUrl);
					$activityPayCode = json_decode($activityPayCodeResponse, true);

					if ($timeEntryModel->load(Yii::$app->request->post()) && $activityModel->load(Yii::$app->request->post())
						&& $timeEntryModel->validate() && $activityModel->validate()) {
						//create timeEntryTitle variable 
						$timeEntryTitle = "timeEntry";
						// concatenate start time
						$TimeEntryStartTimeConcatenate = new DateTime($TimeEntryDate.$timeEntryModel->TimeEntryStartTime);
						$TimeEntryStartTimeConcatenate = $TimeEntryStartTimeConcatenate->format('Y-m-d H:i:s');
						
						// concatenate end time
						$TimeEntryEndTimeConcatenate = new DateTime($TimeEntryDate.$timeEntryModel->TimeEntryEndTime);
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
							'TimeEntryDate' => $TimeEntryDate,
							'TimeEntryCreateDate' => $timeEntryModel->TimeEntryCreateDate,
							'TimeEntryModifiedDate' => $timeEntryModel->TimeEntryModifiedDate,
							'TimeEntryUserID' => $TimeCardTechID,
							'TimeEntryTimeCardID' => $id,
							'TimeEntryActivityID' => $timeEntryModel->TimeEntryActivityID,
							'TimeEntryComment' => $timeEntryModel->TimeEntryComment,
							'TimeEntryCreatedBy' => $timeEntryModel->TimeEntryCreatedBy,
							'TimeEntryModifiedBy' => $timeEntryModel->TimeEntryModifiedBy,
						);
						
						// check difference between startTime and endTime
						if($endTimeObj > $startTimeObj){
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
							
							// post url
							$url_send_activity = 'http://api.southerncrossinc.com/index.php?r=activity%2Fcreate';	
							$response_activity= Parent::executePostRequest($url_send_activity, $json_data);
							$obj = json_decode($response_activity, true);
							
							return $this->redirect(['view', 'id' => $obj["activity"][0]["timeEntry"][0]["TimeEntryTimeCardID"]]);						
						}else{
							return $this->redirect(['view', 'id' => $id]);
						}
					}else {
						return $this->render('create_time_entry', [
							'model' => $timeEntryModel,
							'activityCode' => $activityCode,
							'activityPayCode' => $activityPayCode,
							'activityModel' => $activityModel
						]);
					}
			}catch(ErrorException $e){
				throw new \yii\web\HttpException(400);
			}
		}
		else
		{
			throw new ForbiddenHttpException('You do not have adequate permissions to perform this action.');
		}
    }
	
    /**
     * Approve an existing TimeEntry.
     * If approve is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
	public function actionApprove($id){
		//guest redirect
		if(Yii::$app->user->isGuest){
			return $this->redirect(['login/login']);
		}
		
		try{			
				$cardIDArray[] = $id;				
				$data = array(
					'approvedByID' => Yii::$app->session['userID'],
					'cardIDArray' => $cardIDArray,
				);
				Yii::Trace("approvedByID is : ".$id);
				$json_data = json_encode($data);
				
				// post url
				$putUrl = 'http://api.southerncrossinc.com/index.php?r=time-card%2Fapprove-time-cards';
				$putResponse = Parent::executePutRequest($putUrl, $json_data);
				$obj = json_decode($putResponse, true);
				$responseTimeCardID = $obj[0]["TimeCardID"];
				return $this->redirect(['view', 'id' => $responseTimeCardID]);
			}catch(ErrorException $e){
				throw new \yii\web\HttpException(400);
			}
	}
	
	/**
     * deActive an existing TimeEntry.
     * If deActive is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
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
				$putUrl = 'http://api.southerncrossinc.com/index.php?r=time-entry%2Fdeactivate';
				$putResponse = Parent::executePutRequest($putUrl, $json_data);
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
     * @param string $id
     * @return mixed
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
						'approvedByID' => Yii::$app->session['userID'],
						'cardIDArray' => $TimeCardIDArray,
					);		
				$json_data = json_encode($data);
				
				// post url
					$putUrl = 'http://api.southerncrossinc.com/index.php?r=time-card%2Fapprove-time-cards';
					$putResponse = Parent::executePutRequest($putUrl, $json_data);
					
					return $this->redirect(['index']);
			}catch(ErrorException $e){
				throw new \yii\web\HttpException(400);
			}
		}else{
			  throw new \yii\web\BadRequestHttpException;
		}
	}
	
	/**
     * Updates an existing TimeCard model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
		//guest redirect
		if (Yii::$app->user->isGuest)
		{
			return $this->redirect(['login/login']);
		}
		//RBAC permissions check
		if (Yii::$app->user->can('updateTimeCard'))
		{
			try{
					$getUrl = 'http://api.southerncrossinc.com/index.php?r=time-card%2Fview&id='.$id;
					$getResponse = json_decode(Parent::executeGetRequest($getUrl), true);
					
					$model = new \yii\base\DynamicModel($getResponse);
					
					$model->addRule('TimeCardStartDate', 'safe')
						  ->addRule('TimeCardEndDate', 'safe')
						  ->addRule('TimeCardCreateDate', 'safe')
						  ->addRule('TimeCardModifiedDate', 'safe')
						  ->addRule('TimeCardProjectID', 'integer')
						  ->addRule('TimeCardTechID', 'integer')
						  ->addRule('TimeCardApprovedBy', 'string')
						  ->addRule('TimeCardApprovedFlag', 'integer')
						  ->addRule('TimeCardSupervisorName', 'string')
						  ->addRule('TimeCardComment', 'string')
						  ->addRule('TimeCardCreatedBy', 'string')
						  ->addRule('TimeCardModifiedBy', 'string');
					
					if ($model->load(Yii::$app->request->post()))
					{
						$data = array(
							'TimeCardStartDate' => $model->TimeCardStartDate,
							'TimeCardEndDate' => $model->TimeCardEndDate,
							'TimeCardProjectID' => $model->TimeCardProjectID,
							'TimeCardTechID' => $model->TimeCardTechID,
							'TimeCardApprovedBy' => $model->TimeCardApprovedBy,
							'TimeCardApprovedFlag' => $model->TimeCardApprovedFlag,
							'TimeCardSupervisorName' => $model->TimeCardSupervisorName,
							'TimeCardComment' => $model->TimeCardComment,
							'TimeCardCreateDate' => $model->TimeCardCreateDate,
							'TimeCardCreatedBy' => $model->TimeCardCreatedBy,
							'TimeCardModifiedDate' => $model->TimeCardModifiedDate,
							'TimeCardModifiedBy' => $model->TimeCardModifiedBy,
						);
						
						$json_data = json_encode($data);
						
						$putUrl = 'http://api.southerncrossinc.com/index.php?r=time-card%2Fupdate&id='.$id;
						$putResponse = Parent::executePutRequest($putUrl, $json_data);
						
						$obj = json_decode($putResponse, true);
						
						return $this->redirect(['view', 'id' => $obj["TimeCardID"]]);
					} else {
						return $this->render('update', [
							'model' => $model,
						]);
					}
			}catch(ErrorException $e){
				throw new \yii\web\HttpException(400);
			}
		}
		else
		{
			throw new ForbiddenHttpException('You do not have adequate permissions to perform this action.');
		}
    }

    /**
     * Deletes an existing TimeCard model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
		//guest redirect
		if (Yii::$app->user->isGuest)
		{
			return $this->redirect(['login/login']);
		}
		//RBAC permissions check
		if (Yii::$app->user->can('deleteTimeCard'))
		{
			try{
					$url = 'http://api.southerncrossinc.com/index.php?r=time-card%2Fdelete&id='.$id;
					Parent::executeDeleteRequest($url);
					$this->redirect('/index.php?r=time-card%2Findex');
			}catch(ErrorException $e){
				throw new \yii\web\HttpException(400);
			}
		}
		else
		{
			throw new ForbiddenHttpException('You do not have adequate permissions to perform this action.');
		}
    }
	
	/**
     * Calculate total work hours
     * @return total work hours
     */
	 public function TotalHourCal($dataProvider){
		try{ 
				$Total_Work_Hours = 0;
				foreach($dataProvider as $item){
					if($item["TimeEntryActiveFlag"] != "Inactive"){
						$Total_Work_Hours += $item["TimeEntryMinutes"];
						Yii::Trace("item minutes is: ".$item["TimeEntryMinutes"]);
					}
				}
				
				return number_format ($Total_Work_Hours / 60, 2);
		}catch(ErrorException $e){
				throw new \yii\web\HttpException(400);
			} 
	 }
}
