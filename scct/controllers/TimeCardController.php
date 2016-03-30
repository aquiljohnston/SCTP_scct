<?php

namespace app\controllers;

use Yii;
use app\models\TimeCard;
use app\models\TimeCardSearch;
use app\models\TimeEntry;
use app\controllers\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\grid\GridView;
use yii\data\ArrayDataProvider;
use linslin\yii2\curl;
use yii\web\Request;
use \DateTime;
use yii\web\ForbiddenHttpException;

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
			// create curl for restful call.		
			// get response from api 		
			//$url = "http://api.southerncrossinc.com/index.php?r=time-card%2Fview-all-time-cards-current-week";
			$url = "http://api.southerncrossinc.com/index.php?r=time-card%2Fview-time-card-hours-worked-current";
			$response = Parent::executeGetRequest($url);
			
			// passing decode data into dataProvider
			$dataProvider = new ArrayDataProvider
			([
				'allModels' => json_decode($response, true),
				/*'key' => function (){
					return md5($model["TimeCardID"]);
				},*/
				'pagination' => [
					'pageSize' => 100,
				],
			]);
			
			//set timecardid as id 
			$dataProvider->key ='TimeCardID';
			

			// fill gridview by applying data provider
			GridView::widget([
				'dataProvider' => $dataProvider,
				]);
				
			//calling index page to pass dataProvider.
			return $this->render('index', [
				'dataProvider' => $dataProvider
			]);
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
			$url = 'http://api.southerncrossinc.com/index.php?r=time-card%2Fview-time-entries&id='.$id;
			$time_card_url = 'http://api.southerncrossinc.com/index.php?r=time-card%2Fview&id='.$id;
			
			$response = Parent::executeGetRequest($url);
			$time_card_response = Parent::executeGetRequest($time_card_url);
			$dateProvider = json_decode($response, true);
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
			
			return $this -> render('view', [
											'model' => json_decode($time_card_response, true),
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
			$url = 'http://api.southerncrossinc.com/index.php?r=time-entry%2Fview&id='.$id;
			$response = Parent::executeGetRequest($url);

			return $this -> render('view', ['model' => json_decode($response), true]);
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
			$model = new TimeEntry();
			
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
			
			if ($model->load(Yii::$app->request->post())) {
				// concatenate start time
				$TimeEntryStartTimeConcatenate = new DateTime($TimeEntryDate.$model->TimeEntryStartTime);
				$TimeEntryStartTimeConcatenate = $TimeEntryStartTimeConcatenate->format('Y-m-d H:i:s');
				
				// concatenate end time
				$TimeEntryEndTimeConcatenate = new DateTime($TimeEntryDate.$model->TimeEntryEndTime);
				$TimeEntryEndTimeConcatenate = $TimeEntryEndTimeConcatenate->format('Y-m-d H:i:s');
				
				$data = array(
					'TimeEntryStartTime' => $TimeEntryStartTimeConcatenate,
					'TimeEntryEndTime' => $TimeEntryEndTimeConcatenate,
					'TimeEntryDate' => $TimeEntryDate,
					'TimeEntryCreateDate' => $model->TimeEntryCreateDate,
					'TimeEntryModifiedDate' => $model->TimeEntryModifiedDate,
					'TimeEntryUserID' => $TimeCardTechID,
					'TimeEntryTimeCardID' => $id,
					'TimeEntryActivityID' => $model->TimeEntryActivityID,
					'TimeEntryComment' => $model->TimeEntryComment,
					'TimeEntryCreatedBy' => $model->TimeEntryCreatedBy,
					'TimeEntryModifiedBy' => $model->TimeEntryModifiedBy,
				);
				
				$json_data = json_encode($data);	
				
				// post url
				$url_send = 'http://api.southerncrossinc.com/index.php?r=time-entry%2Fcreate';				
				$response = Parent::executePostRequest($url_send, $json_data);
				$obj = json_decode($response, true);
				
				return $this->redirect(['view', 'id' => $obj["TimeEntryTimeCardID"]]);
			} else {
				return $this->render('create_time_entry', [
					'model' => $model,
					'activityCode' => $activityCode,
				]);
			}
		}
		else
		{
			throw new ForbiddenHttpException('You do not have adequate permissions to perform this action.');
		}
    }
	
    /**
     * Approve an existing TimeCard.
     * If approve is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
	public function actionApprove($id){
		//guest redirect
		if(Yii::$app->user->isGuest){
			return $this->redirect(['login/login']);
		}
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
	}
	
	/**
     * Approve Multiple existing TimeCard.
     * If approve is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
	public function actionApproveMultiple() {
		
		if (Yii::$app->request->isAjax) {
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
			$url = 'http://api.southerncrossinc.com/index.php?r=time-card%2Fdelete&id='.$id;
			Parent::executeDeleteRequest($url);
			$this->redirect('/index.php?r=time-card%2Findex');
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
		 $Total_Work_Hours = 0;
		 foreach($dataProvider as $item){
			 $Total_Work_Hours += $item["TimeEntryMinutes"];
			 Yii::Trace("item minutes is: ".$item["TimeEntryMinutes"]);
		 }
		 
		 return number_format ($Total_Work_Hours / 60, 2);
	 }
}
