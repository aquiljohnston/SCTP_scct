<?php

namespace app\controllers;

use Yii;
use app\models\MileageCard;
use app\models\MileageCardSearch;
use app\models\MileageEntry;
use app\controllers\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\grid\GridView;
use yii\data\ArrayDataProvider;
use linslin\yii2\curl;
use yii\web\Request;
use yii\web\ForbiddenHttpException;
use \DateTime;

/**
 * MileageCardController implements the CRUD actions for MileageCard model.
 */
class MileageCardController extends BaseController
{

    /**
     * Lists all MileageCard models.
     * @return mixed
     */
    public function actionIndex()
    {
		//guest redirect
		if (Yii::$app->user->isGuest)
		{
			return $this->redirect(['login/login']);
		}
		//get week
		$week = Yii::$app->request->getQueryParam("week");
		///week defaults to current
		if ($week == "") $week = "current";

		//build url with params
		$url = "mileage-card%2Fget-cards&week=" . $week;
		$response = Parent::executeGetRequest($url); // indirect rbac

		$filteredResultData = $this->filterColumnMultiple(json_decode($response, true), 'UserFirstName', 'filterfirstname');
		$filteredResultData = $this->filterColumnMultiple($filteredResultData, 'UserLastName', 'filterlastname');
		$filteredResultData = $this->filterColumnMultiple($filteredResultData, 'ProjectName', 'filterprojectname');
		$filteredResultData = $this->filterColumnMultiple($filteredResultData, 'MileageCardApprovedFlag', 'filterapproved');

		// passing decode data into dataProvider
		$dataProvider = new ArrayDataProvider
		([
			'allModels' => $filteredResultData,
			'pagination' => [
				'pageSize' => 100,
			]
		]);
		//Set Mile Card ID On the JS Call
		$dataProvider->key ='MileageCardID';

		$searchModel = [
			'UserFirstName' => Yii::$app->request->getQueryParam('filterfirstname', ''),
			'UserLastName' => Yii::$app->request->getQueryParam('filterlastname', ''),
			'ProjectName' => Yii::$app->request->getQueryParam('filterprojectname', ''),
			'MileageCardApprovedFlag' => Yii::$app->request->getQueryParam('filterapproved', '')
		];

		$approvedInput = '<select class="form-control" name="filterapproved">'
			. '<option value=""></option><option value="Yes"';
		if($searchModel['MileageCardApprovedFlag'] == "Yes") {
			$approvedInput.= " selected";
		}
		$approvedInput .= '>Yes</option><option value="No"';
		if($searchModel['MileageCardApprovedFlag'] == "No") {
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
    }

    /**
     * Displays a single MileageCard model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
		//guest redirect
		if (Yii::$app->user->isGuest)
		{
			return $this->redirect(['login/login']);
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
				$url = 'mileage-card%2Fget-entries&cardID='.$id;
				$mileage_card_url = 'mileage-card%2Fview&id='.$id;

				//Indirect RBAC checks
				$response = Parent::executeGetRequest($url);
				$mileage_card_response = Parent::executeGetRequest($mileage_card_url);
				$model = json_decode($mileage_card_response, true);
				$dateProvider = json_decode($response, true);
				$ApprovedFlag = $dateProvider["ApprovedFlag"];
				$Sundaydata = $dateProvider["MileageEntries"][0]["Sunday"];
				$SundayProvider = new ArrayDataProvider([
					'allModels' => $Sundaydata,
					'pagination' => [
						'pageSize' => 10,
					],
					// 'sort' => [
						// 'attributes' => ['id', 'name'],
					// ],
				]);
				$Total_Mileage_Sun = $this->TotalMileageCal($Sundaydata);

				$Mondaydata = $dateProvider["MileageEntries"][0]["Monday"];
				$MondayProvider = new ArrayDataProvider([
					'allModels' => $Mondaydata,
					'pagination' => [
						'pageSize' => 10,
					],
					// 'sort' => [
						// 'attributes' => ['id', 'name'],
					// ],
				]);
				$Total_Mileage_Mon = $this->TotalMileageCal($Mondaydata);

				$Tuesdaydata = $dateProvider["MileageEntries"][0]["Tuesday"];
				$TuesdayProvider = new ArrayDataProvider([
					'allModels' => $Tuesdaydata,
					'pagination' => [
						'pageSize' => 10,
					],
					// 'sort' => [
						// 'attributes' => ['id', 'name'],
					// ],
				]);
				$Total_Mileage_Tue = $this->TotalMileageCal($Tuesdaydata);

				$Wednesdaydata = $dateProvider["MileageEntries"][0]["Wednesday"];
				$WednesdayProvider = new ArrayDataProvider([
					'allModels' => $Wednesdaydata,
					'pagination' => [
						'pageSize' => 10,
					],
					// 'sort' => [
						// 'attributes' => ['id', 'name'],
					// ],
				]);
				$Total_Mileage_Wed = $this->TotalMileageCal($Wednesdaydata);

				$Thursdaydata = $dateProvider["MileageEntries"][0]["Thursday"];
				$ThursdayProvider = new ArrayDataProvider([
					'allModels' => $Thursdaydata,
					'pagination' => [
						'pageSize' => 10,
					],
					// 'sort' => [
						// 'attributes' => ['id', 'name'],
					// ],
				]);
				$Total_Mileage_Thr = $this->TotalMileageCal($Thursdaydata);

				$Fridaydata = $dateProvider["MileageEntries"][0]["Friday"];
				$FridayProvider = new ArrayDataProvider([
					'allModels' => $Fridaydata,
					'pagination' => [
						'pageSize' => 10,
					],
					// 'sort' => [
						// 'attributes' => ['id', 'name'],
					// ],
				]);
				$Total_Mileage_Fri = $this->TotalMileageCal($Fridaydata);

				$Saturdaydata = $dateProvider["MileageEntries"][0]["Saturday"];
				$SaturdayProvider = new ArrayDataProvider([
					'allModels' => $Saturdaydata,
					'pagination' => [
						'pageSize' => 10,
					],
					// 'sort' => [
						// 'attributes' => ['id', 'name'],
					// ],
				]);
				$Total_Mileage_Sat = $this->TotalMileageCal($Saturdaydata);

				//calculation total miles for this mileage card
				$Total_Mileage_Current_MileageCard = $Total_Mileage_Sun +
												$Total_Mileage_Mon +
												$Total_Mileage_Tue +
												$Total_Mileage_Wed +
												$Total_Mileage_Thr +
												$Total_Mileage_Fri +
												$Total_Mileage_Sat;

				//set MileageEntryID as id
						$SundayProvider->key ='MileageEntryID';
						$MondayProvider->key ='MileageEntryID';
						$TuesdayProvider->key ='MileageEntryID';
						$WednesdayProvider->key ='MileageEntryID';
						$ThursdayProvider->key ='MileageEntryID';
						$FridayProvider->key ='MileageEntryID';
						$SaturdayProvider->key ='MileageEntryID';

				return $this -> render('view', [
												'model' => $model,
												'duplicateFlag' => $duplicateFlag,
												'ApprovedFlag' => $ApprovedFlag,
												'Total_Mileage_Current_MileageCard' => $Total_Mileage_Current_MileageCard,
												'dateProvider' => $dateProvider,
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
		} catch(ErrorException $e){
			throw new \yii\web\HttpException(400);
		}
    }

	/**
     * Creates a new MileageEntry model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
	public function actionCreateMileageEntry($mileageCardId, $mileageCardTechId, $mileageCardDate) {
		//guest redirect
		if (Yii::$app->user->isGuest)
		{
			return $this->redirect(['login/login']);
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
			$MileageEntryStartTimeConcatenate = new DateTime($mileageCardDate.$model->MileageEntryStartDate);
			$MileageEntryStartTimeConcatenate = $MileageEntryStartTimeConcatenate->format('Y-m-d H:i:s');

			//concatenate end time
			$MileageEntryEndTimeConcatenate = new DateTime($mileageCardDate.$model->MileageEntryEndDate);
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
				'MileageEntryDate' => $mileageCardDate,
				'MileageEntryType' => '0', //Automatically set the mileage entry type to 0 for BUSINESS - Andre V.
				'MileageEntryActivityID' => '3', //Automatically set the mileage entry activity type to 3 for PRODUCTION - Andre V.
				'MileageEntryMileageCardID' => $mileageCardId,
				'MileageEntryApprovedBy' => $model->MileageEntryApprovedBy,
				'MileageEntryComment' => $model->MileageEntryComment,
			);

			// check difference between StartingMileage and EndingMileage
			if($endTimeObj > $startTimeObj && $endMileageObj > $startMileageObj){
						$time_entry_data = array();
						$data[] = array(
							'ActivityTitle' => $mileageEntryTitle,
							'ActivityCreatedBy' => Yii::$app->session['userID'],
							'timeEntry' => $time_entry_data,
							'mileageEntry' => $mileage_entry_data,
						);

						$activity = array(
							'activity' => $data,
						);

						$json_data = json_encode($activity);

						try{
							// post url
							$url_send_activity = 'activity%2Fcreate';
							$response_activity= Parent::executePostRequest($url_send_activity, $json_data);
							$obj = json_decode($response_activity, true);

							return $this->redirect(['view', 'id' => $obj["activity"][0]["mileageEntry"][0]["MileageEntryMileageCardID"]]);
						}catch(\Exception $e){
							$concatenate_id = $mileageCardId . "yes";
							return $this->redirect(['view', 'id' => $concatenate_id]);
						}

					}else{
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
     */
	public function actionApprove($id){
		//guest redirect
		if(Yii::$app->user->isGuest){
			return $this->redirect(['login/login']);
		}
		$cardIDArray[] = $id;
		
		$data = array(
			'cardIDArray' => $cardIDArray,
		);
		$json_data = json_encode($data);
		
		// post url
		$putUrl = 'mileage-card%2Fapprove-cards';
		$putResponse = Parent::executePutRequest($putUrl, $json_data);  // indirect RBAC
		$obj = json_decode($putResponse, true);
		$responseMileageCardID = $obj[0]["MileageCardID"];
		return $this->redirect(['view', 'id' => $responseMileageCardID]);
	}
	
	/**
	 * deactivate Multiple existing Mileage Card(s)
	 * If deactivate is successful, the browser will be redirected to the 'view' page.
	 * @return mixed
	 */
	public function actionDeactivate() {

		if (Yii::$app->request->isAjax) {
			$data = Yii::$app->request->post();

			// loop the data array to get all id's.
			foreach ($data as $key) {
				foreach($key as $keyitem){

					$MileageEntryIDArray[] = $keyitem;
					Yii::Trace("Mileage Card ID is : ". $keyitem);
				}
			}

			$data = array(
				'deactivatedBy' => Yii::$app->session['userID'],
				'entryArray' => $MileageEntryIDArray,
			);
			$json_data = json_encode($data);

			// post url
			$putUrl = 'mileage-entry%2Fdeactivate';
			$putResponse = Parent::executePutRequest($putUrl, $json_data);
			$obj = json_decode($putResponse, true);
			$responseMileageCardID = $obj[0]["MileageEntryMileageCardID"];
			return $this->redirect(['view', 'id' => $responseMileageCardID]);

		}else{
			throw new \yii\web\BadRequestHttpException;
		}
	}

	/**
	 * Approve Multiple existing Mileage Card(s)
	 * If approve is successful, the browser will be redirected to the 'view' page.
	 * @return mixed
	 */
	public function actionApproveMultiple() {

		if (Yii::$app->request->isAjax) {
			$data = Yii::$app->request->post();

			// loop the data array to get all id's.
			foreach ($data as $key) {
				foreach($key as $keyitem){

					$MileageCardIDArray[] = $keyitem;
					Yii::Trace("Mileage Card ID is : ". $keyitem);
				}
			}

			$data = array(
				'cardIDArray' => $MileageCardIDArray,
			);
			$json_data = json_encode($data);

			// post url
			$putUrl = 'mileage-card%2Fapprove-cards';
			Parent::executePutRequest($putUrl, $json_data); //indirect rbac

			return $this->redirect(['index']);
		}else{
			throw new \yii\web\BadRequestHttpException;
		}
	}

		/**
     * Calculate total work hours
     * @return total work hours
     */
	 public function TotalMileageCal($dataProvider){
		 $Total_Mileages = 0;
		 foreach($dataProvider as $item){
			 if($item["MileageEntryActiveFlag"] != "Inactive"){
				 $Total_Mileages += $item["MileageEntryEndingMileage"] - $item["MileageEntryStartingMileage"];
				 Yii::Trace("End mileage is: ".$item["MileageEntryEndingMileage"]);
			 }
		 }
		 
		 return number_format ($Total_Mileages, 2);
	 }
}
