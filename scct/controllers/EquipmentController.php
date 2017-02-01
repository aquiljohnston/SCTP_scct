<?php

namespace app\controllers;

use Yii;
use app\models\equipment;
use app\models\EquipmentSearch;
use app\controllers\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use linslin\yii2\curl;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\web\ForbiddenHttpException;
use yii\data\Pagination;

/**
 * EquipmentController implements the CRUD actions for equipment model.
 */
class EquipmentController extends BaseController
{

    /**
     * Lists all equipment models.
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws \yii\web\HttpException
     */
    public function actionIndex()
    {
        //guest redirect
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['login/login']);
        }
        try {

            $model = new \yii\base\DynamicModel([
                'pagesize'
            ]);
            $model->addRule('pagesize', 'string', ['max' => 32]);//get page number and records per page

            // check if type was post, if so, get value from $model
            if ($model->load(Yii::$app->request->post())) {
                Yii::trace("pagesize: " . $model->pagesize);
                $equipmentPageSizeParams = $model->pagesize;
            } else {
                if (isset($_GET['per-page'])) {
                    $equipmentPageSizeParams = $_GET['per-page'];
                    Yii::trace("Post per-page: " . $_GET['per-page']);
                } else {
                    $equipmentPageSizeParams = 10;
                }
            }

            if (isset($_GET['equipmentPage'])) {
                $page = $_GET['equipmentPage'];
                Yii::trace("Post equipmentPage: " . $_GET['equipmentPage']);
            } else {
                $page = 1;
            }

            //build url with params
            $url = 'equipment%2Fget-equipment&listPerPage=' . $equipmentPageSizeParams . "&page=" . $page;
            $response = Parent::executeGetRequest($url);
            $response = json_decode($response, true);
            $assets = $response['assets'];

            $filteredResultData = $this->filterColumnMultiple($assets, 'EquipmentName', 'filtername');
            $filteredResultData = $this->filterColumnMultiple($filteredResultData, 'EquipmentSerialNumber', 'filterserialnumber');
            $filteredResultData = $this->filterColumnMultiple($filteredResultData, 'EquipmentSCNumber', 'filterscnumber');
            $filteredResultData = $this->filterColumnMultiple($filteredResultData, 'EquipmentType', 'filtertype');
            $filteredResultData = $this->filterColumnMultiple($filteredResultData, 'ClientName', 'filterclientname');
            $filteredResultData = $this->filterColumnMultiple($filteredResultData, 'ProjectName', 'filterprojectname');
            $filteredResultData = $this->filterColumnMultiple($filteredResultData, 'EquipmentAcceptedFlag', 'filteraccepted');


            $searchModel = [
                'EquipmentName' => Yii::$app->request->getQueryParam('filtername', ''),
                'EquipmentSerialNumber' => Yii::$app->request->getQueryParam('filterserialnumber', ''),
                'EquipmentSCNumber' => Yii::$app->request->getQueryParam('filterscnumber', ''),
                'EquipmentType' => Yii::$app->request->getQueryParam('filtertype', ''),
                'ClientName' => Yii::$app->request->getQueryParam('filterclientname', ''),
                'ProjectName' => Yii::$app->request->getQueryParam('filterprojectname', ''),
                'EquipmentAcceptedFlag' => Yii::$app->request->getQueryParam('filteraccepted', '')
            ];

            // Create drop down with current selection pre-selected based on GET variable
            $acceptedFilterInput = '<select class="form-control" name="filteraccepted">'
                . '<option value=""></option><option value="Yes"';
            if ($searchModel['EquipmentAcceptedFlag'] == "Yes") {
                $acceptedFilterInput .= " selected";
            }
            $acceptedFilterInput .= '>Yes</option><option value="Pending"';
            if ($searchModel['EquipmentAcceptedFlag'] == "Pending") {
                $acceptedFilterInput .= " selEquipmentAcceptedFlagected";
            }
            $acceptedFilterInput .= '>Pending</option><option value="No"';
            if ($searchModel['EquipmentAcceptedFlag'] == "No") {
                $acceptedFilterInput .= ' selected';
            }
            $acceptedFilterInput .= '>No</option><option value="Pending|No"';
            if ($searchModel['EquipmentAcceptedFlag'] == "Pending|No") {
                $acceptedFilterInput .= ' selected';
            }
            $acceptedFilterInput .= '>Pending & No</option></select>';


            //Passing data to the dataProvider and formatting it in an associative array
            $dataProvider = new ArrayDataProvider
            ([
                'allModels' => $filteredResultData,
                'pagination' => [
                    'pageSize' => 100,
                ],
            ]);

            //set equipmentid as id
            $dataProvider->key = 'EquipmentID';

            // set pages to dispatch table
            $pages = new Pagination($response['pages']);

            return $this->render('index', [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
                'acceptedFilterInput' => $acceptedFilterInput,
                'model' => $model,
                'equipmentPageSizeParams' => $equipmentPageSizeParams,
                'pages' => $pages
            ]);
        } catch (ForbiddenHttpException $e) {
            throw $e;
        } catch (ErrorException $e) {
            throw new \yii\web\HttpException(400);
        } catch (Exception $e) {
            throw new ServerErrorHttpException;
        }
    }

    /**
     * Displays a single equipment model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        //guest redirect
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['login/login']);
        }
        $url = 'equipment%2Fview&id=' . $id;
        $response = Parent::executeGetRequest($url); // indirect RBAC

        return $this->render('view', ['model' => json_decode($response, true)]);
    }

    /**
     * Creates a new equipment model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        //guest redirect
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['login/login']);
        }

        $model = new Equipment();

        //GET DATA TO FILL FORM DROPDOWNS//
        //get clients for form dropdown
        $clientUrl = "client%2Fget-client-dropdowns";
        $clientResponse = Parent::executeGetRequest($clientUrl);
        $clients = json_decode($clientResponse, true);

        //get types for form dropdown
        $typeUrl = "equipment-type%2Fget-type-dropdowns";
        $typeResponse = Parent::executeGetRequest($typeUrl);
        $types = json_decode($typeResponse, true);

        //get conditions for form dropdown
        $conditionUrl = "equipment-condition%2Fget-condition-dropdowns";
        $conditionResponse = Parent::executeGetRequest($conditionUrl);
        $conditions = json_decode($conditionResponse, true);

        //get status for form dropdown
        $statusURL = "equipment-status%2Fget-status-dropdowns";
        $statusResponse = Parent::executeGetRequest($statusURL);
        $statuses = json_decode($statusResponse, true);

        if ($model->load(Yii::$app->request->post())) {

            $data = array(
                'EquipmentName' => $model->EquipmentName,
                'EquipmentSerialNumber' => $model->EquipmentSerialNumber,
                'EquipmentSCNumber' => $model->EquipmentSCNumber,
                'EquipmentDetails' => $model->EquipmentDetails,
                'EquipmentType' => $model->EquipmentType,
                'EquipmentManufacturer' => $model->EquipmentManufacturer,
                'EquipmentManufactureYear' => $model->EquipmentManufactureYear,
                'EquipmentCondition' => $model->EquipmentCondition,
                'EquipmentStatus' => $model->EquipmentStatus,
                'EquipmentMACID' => $model->EquipmentMACID,
                'EquipmentModel' => $model->EquipmentModel,
                'EquipmentColor' => $model->EquipmentColor,
                'EquipmentWarrantyDetail' => $model->EquipmentWarrantyDetail,
                'EquipmentComment' => $model->EquipmentComment,
                'EquipmentClientID' => $model->EquipmentClientID,
                'EquipmentAnnualCalibrationDate' => $model->EquipmentAnnualCalibrationDate,
                'EquipmentCreateDate' => $model->EquipmentCreateDate,
                'EquipmentModifiedBy' => $model->EquipmentModifiedBy,
                'EquipmentModifiedDate' => $model->EquipmentModifiedDate,
            );

            $json_data = json_encode($data);

            // post url
            $url = "equipment%2Fcreate";
            $response = Parent::executePostRequest($url, $json_data);
            $obj = json_decode($response, true);

            return $this->redirect(['view', 'id' => $obj["EquipmentID"]]);
        } else {
            return $this->render('create', [
                'model' => $model,
                'clients' => $clients,
                'types' => $types,
                'conditions' => $conditions,
                'statuses' => $statuses,
            ]);
        }

    }

    /**
     * Approve Multiple existing Equipment.
     * If approve is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionApproveMultipleEquipment()
    {

        if (Yii::$app->request->isAjax) {
            self::requirePermission("acceptEquipment");
            $data = Yii::$app->request->post();

            // loop the data array to get all id's.
            foreach ($data as $key) {
                foreach ($key as $keyitem) {
                    //$cardIDArray[] = $key["TimeCardID"];
                    $EquipmentIDArray[] = $keyitem;
                    Yii::Trace("Equipment is ; " . $keyitem);
                }
            }

            $data_approve = array(
                'equipmentIDArray' => $EquipmentIDArray,
            );
            $json_data = json_encode($data_approve);

            // post url
            $putUrl = 'equipment%2Faccept-equipment';
            $putResponse = Parent::executePutRequest($putUrl, $json_data);

            return $this->redirect(['index']);
        } else {
            throw new \yii\web\BadRequestHttpException;
        }
    }

    /**
     * Approve an existing Equipment.
     * If approve is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionApprove($id)
    {
        //guest redirect
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['login/login']);
        }
        $EquipmentIDArray[0] = $id;

        $data = array(
            'equipmentIDArray' => $EquipmentIDArray,
        );

        $json_data_approve = json_encode($data);

        // post url
        $putUrl = 'equipment%2Faccept-equipment';
        $putResponse = Parent::executePutRequest($putUrl, $json_data_approve);
        $obj = json_decode($putResponse, true);
        $responseEquipmentID = $obj[0]["EquipmentID"];
        return $this->redirect(['view', 'id' => $responseEquipmentID]);
    }

    /**
     * Approve Multiple existing TimeCard.
     * If approve is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionApproveMultiple()
    {

        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();

            // loop the data array to get all id's.
            foreach ($data as $key) {
                foreach ($key as $keyitem) {

                    $EquipmentIDArray[] = $keyitem;
                    Yii::Trace("Equipmentid is ; " . $keyitem);
                }
            }

            $data = array(
                'approvedByID' => Yii::$app->session['userID'],
                'cardIDArray' => $EquipmentIDArray,
            );
            $json_data = json_encode($data);

            // post url
            $putUrl = 'equipment%2Faccept-equipment';
            $putResponse = Parent::executePutRequest($putUrl, $json_data);

            return $this->redirect(['index']);
        } else {
            throw new \yii\web\BadRequestHttpException;
        }
    }

    /**
     * Updates an existing equipment model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        //guest redirect
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['login/login']);
        }

        self::requirePermission("equipmentUpdate");

        $getUrl = 'equipment%2Fview&id=' . $id;
        $getResponse = json_decode(Parent::executeGetRequest($getUrl), true);

        $model = new equipment();
        $model->attributes = $getResponse;

        //GET DATA TO FILL FORM DROPDOWNS//
        //get clients for form dropdown
        $clientUrl = "client%2Fget-client-dropdowns";
        $clientResponse = Parent::executeGetRequest($clientUrl);
        $clients = json_decode($clientResponse, true);

        //get types for form dropdown
        $typeUrl = "equipment-type%2Fget-type-dropdowns";
        $typeResponse = Parent::executeGetRequest($typeUrl);
        $types = json_decode($typeResponse, true);

        //get conditions for form dropdown
        $conditionUrl = "equipment-condition%2Fget-condition-dropdowns";
        $conditionResponse = Parent::executeGetRequest($conditionUrl);
        $conditions = json_decode($conditionResponse, true);

        //get status for form dropdown
        $statusURL = "equipment-status%2Fget-status-dropdowns";
        $statusResponse = Parent::executeGetRequest($statusURL);
        $statuses = json_decode($statusResponse, true);

        //get userIDs for form dropdown
        $userUrl = "user%2Fget-user-dropdowns";
        $userResponse = Parent::executeGetRequest($userUrl);
        $users = json_decode($userResponse, true);

        //get projects for form dropdown
        $projectUrl = "project%2Fget-project-dropdowns";
        $projectResponse = Parent::executeGetRequest($projectUrl);
        $projects = json_decode($projectResponse, true);

        if ($model->load(Yii::$app->request->post())) {
            $data = array(
                'EquipmentName' => $model->EquipmentName,
                'EquipmentSerialNumber' => $model->EquipmentSerialNumber,
                'EquipmentSCNumber' => $model->EquipmentSCNumber,
                'EquipmentDetails' => $model->EquipmentDetails,
                'EquipmentType' => $model->EquipmentType,
                'EquipmentManufacturer' => $model->EquipmentManufacturer,
                'EquipmentManufactureYear' => $model->EquipmentManufactureYear,
                'EquipmentCondition' => $model->EquipmentCondition,
                'EquipmentStatus' => $model->EquipmentStatus,
                'EquipmentMACID' => $model->EquipmentMACID,
                'EquipmentModel' => $model->EquipmentModel,
                'EquipmentColor' => $model->EquipmentColor,
                'EquipmentWarrantyDetail' => $model->EquipmentWarrantyDetail,
                'EquipmentComment' => $model->EquipmentComment,
                'EquipmentClientID' => $model->EquipmentClientID,
                'EquipmentProjectID' => $model->EquipmentProjectID,
                'EquipmentAnnualCalibrationDate' => $model->EquipmentAnnualCalibrationDate,
                'EquipmentAssignedUserID' => $model->EquipmentAssignedUserID,
                'EquipmentCreatedByUser' => $model->EquipmentCreatedByUser,
                'EquipmentCreateDate' => $model->EquipmentCreateDate,
                'EquipmentModifiedBy' => Yii::$app->session['userID'],
                'EquipmentModifiedDate' => $model->EquipmentModifiedDate,
            );

            $json_data = json_encode($data);

            $putUrl = 'equipment%2Fupdate&id=' . $id;
            $putResponse = Parent::executePutRequest($putUrl, $json_data);

            $obj = json_decode($putResponse, true);

            return $this->redirect(['view', 'id' => $model["EquipmentID"]]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'clients' => $clients,
                'types' => $types,
                'conditions' => $conditions,
                'statuses' => $statuses,
                'users' => $users,
                'projects' => $projects,
            ]);
        }
    }

    /**
     * Deletes an existing equipment model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        //guest redirect
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['login/login']);
        }

        $url = 'equipment%2Fdelete&id=' . $id;
        Parent::executeDeleteRequest($url);
        $this->redirect('/equipment/index');
    }
}
