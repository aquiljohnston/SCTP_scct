<?php

namespace app\modules\dispatch\controllers;

use Yii;
use yii\data\ArrayDataProvider;
use yii\data\Pagination;

class AssignedController extends \app\controllers\BaseController
{
    public function actionIndex()
    {
        // Verify logged in
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['/login']);
        }

        $model = new \yii\base\DynamicModel([
            'assignedfilter', 'pagesize'
        ]);
        $model->addRule('assignedfilter', 'string', ['max' => 32])
            ->addRule('pagesize', 'string', ['max' => 32]);

        //check request
        if ($model->load(Yii::$app->request->queryParams)) {

            Yii::trace("assignedfilter " . $model->assignedfilter);
            Yii::trace("pagesize " . $model->pagesize);
            $assignedPageSizeParams = $model->pagesize;
            $assignedFilterParams = $model->assignedfilter;
        } else {
            $assignedPageSizeParams = 50;
            $assignedFilterParams = "";
        }

        // get the page number for assigned table
        if (isset($_GET['assignedPageNumber']) && $_GET['assignedTableRecordsUpdate'] != "true") {
            $pageAt = $_GET['assignedPageNumber'];
        } else {
            $pageAt = 1;
        }

        $getUrl = 'dispatch%2Fget-assigned&' . http_build_query([
                'filter' => $assignedFilterParams,
                'listPerPage' => $assignedPageSizeParams,
                'page' => $pageAt
            ]);
        $getAssignedDataResponse = json_decode(Parent::executeGetRequest($getUrl, self::API_VERSION_2), true); //indirect RBAC
        Yii::trace("ASSIGNED DATA: ".json_encode($getAssignedDataResponse));
        $assignedData = $getAssignedDataResponse['mapGrids'];

        //todo: check permission to un-assign work
        $canUnassign = 1;
        $canAddSurveyor = 1;

        //todo: set default value or callback value
        $divisionParams = "";

        //set paging on assigned table
        $pages = new Pagination($getAssignedDataResponse['pages']);

        $assignedDataProvider = new ArrayDataProvider
        ([
            'allModels' => $assignedData,
            'pagination' => false,
        ]);

        $assignedDataProvider->key = 'MapGrid';

        if (Yii::$app->request->isAjax) {
            return $this->render('index', [
                'assignedDataProvider' => $assignedDataProvider,
                'model' => $model,
                'pages' => $pages,
                'canUnassign' => $canUnassign,
                'canAddSurveyor' => $canAddSurveyor,
                'divisionParams' => $divisionParams,
                'assignedPageSizeParams' => $assignedPageSizeParams,
                'assignedFilterParams' => $assignedFilterParams,
            ]);
        } else {
            return $this->render('index', [
                'assignedDataProvider' => $assignedDataProvider,
                'model' => $model,
                'pages' => $pages,
                'canUnassign' => $canUnassign,
                'canAddSurveyor' => $canAddSurveyor,
                'divisionParams' => $divisionParams,
                'assignedPageSizeParams' => $assignedPageSizeParams,
                'assignedFilterParams' => $assignedFilterParams,
            ]);
        }
    }

    /**
     * Unassign work function
     * @return \yii\web\Response
     * @throws ForbiddenHttpException
     */
    public function actionUnassign()
    {
        try {
            if (Yii::$app->request->isAjax) {
                Yii::trace("call Unassign");
                $data = Yii::$app->request->post();
                //$data = self::GenerateUnassignedData($data['MapGrid'], $data['AssignedToIDs']);
                $json_data = json_encode($data);
                Yii::trace("Unassigned Data: ".$json_data);

                // post url
                $deleteUrl = 'dispatch%2Funassign';
                $deleteResponse = Parent::executeDeleteRequest($deleteUrl, $json_data, self::API_VERSION_2); // indirect rbac
                Yii::trace("unassignputResponse " . $deleteResponse);

            } else {
                throw new \yii\web\BadRequestHttpException;
            }
        } catch (ForbiddenHttpException $e) {
            throw new ForbiddenHttpException;
        } catch (\Exception $e) {
            Yii::$app->runAction('login/user-logout');
        }
    }

    /**
     * render expandable section row
     * @return string|Response
     */
    public function actionViewSection()
    {
        // Verify logged in
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['/login']);
        }

        $model = new \yii\base\DynamicModel([
            'assignedfilter', 'pagesize', 'mapGridSelected'
        ]);
        $model->addRule('assignedfilter', 'string', ['max' => 32])
            ->addRule('pagesize', 'string', ['max' => 32])
            ->addRule('mapGridSelected', 'string', ['max' => 32]);

        //check request
        if ($model->load(Yii::$app->request->queryParams)) {

            Yii::trace("assignedfilter " . $model->assignedfilter);
            Yii::trace("pagesize " . $model->pagesize);
            $assignedPageSizeParams = $model->pagesize;
            $assignedFilterParams = $model->assignedfilter;
            $mapGridSelected = $model->mapGridSelected;
        } else {
            $assignedPageSizeParams = 50;
            $assignedFilterParams = "";
            $mapGridSelected = "";
        }

        // get the page number for assigned table
        if (isset($_GET['assignedPageNumber']) && $_GET['assignedTableRecordsUpdate'] != "true") {
            $pageAt = $_GET['assignedPageNumber'];
        } else {
            $pageAt = 1;
        }

        // get the key to generate section table
        if (isset($_POST['expandRowKey']))
            $mapGridSelected = $_POST['expandRowKey'];
        else
            $mapGridSelected = "";

        $getUrl = 'dispatch%2Fget-assigned&' . http_build_query([
                'mapGridSelected' => $mapGridSelected,
                'filter' => $assignedFilterParams,
                'listPerPage' => $assignedPageSizeParams,
                'page' => $pageAt
            ]);
        $getSectionDataResponseResponse = json_decode(Parent::executeGetRequest($getUrl, self::API_VERSION_2), true); //indirect RBAC
        Yii::trace("ASSIGNED SECTION: ".json_encode($getSectionDataResponseResponse));
        $sectionData = $getSectionDataResponseResponse['sections'];

        //set paging on assigned table
        $pages = new Pagination($getSectionDataResponseResponse['pages']);

        $sectionDataProvider = new ArrayDataProvider
        ([
            'allModels' => $sectionData,
            'pagination' => false,
        ]);

        $sectionDataProvider->key = 'SectionNumber';

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('_section-expand', [
                'sectionDataProvider' => $sectionDataProvider,
                'model' => $model,
                'pages' => $pages,
                'assignedPageSizeParams' => $assignedPageSizeParams,
                'assignedFilterParams' => $assignedFilterParams,
            ]);
        } else {
            return $this->render('_section-expand', [
                'sectionDataProvider' => $sectionDataProvider,
                'model' => $model,
                'pages' => $pages,
                'assignedPageSizeParams' => $assignedPageSizeParams,
                'assignedFilterParams' => $assignedFilterParams,
            ]);
        }
    }

    /**
     * render asset modal
     * @return string|Response
     */
    public function actionViewAsset($mapGridSelected = null, $sectionNumberSelected = null)
    {
        Yii::trace("CALL VIEW ASSET");
        $model = new \yii\base\DynamicModel([
            'surveyor', 'assetID'
        ]);
        $model->addRule('surveyor', 'string', ['max' => 32])
            ->addRule('assetID', 'string', ['max' => 32]);

        // Verify logged in
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['/login']);
        }

        if ($model->load(Yii::$app->request->queryParams)){
            //todo: need to remove hard code value
            $viewAssetFilterParams = "";
            $viewAssetPageSizeParams = 50;
            $pageAt = 1;
            $searchFilterVal = "";
        }else{
            $viewAssetFilterParams = "";
            $viewAssetPageSizeParams = 50;
            $pageAt = 1;
            $searchFilterVal = "";
        }

        $getUrl = 'dispatch%2Fget-assigned-assets&' . http_build_query([
                'mapGridSelected' => $mapGridSelected,
                'sectionNumberSelected' => $sectionNumberSelected,
                'filter' => $viewAssetFilterParams,
                'listPerPage' => $viewAssetPageSizeParams,
                'page' => $pageAt,
            ]);
        $getAssetDataResponse = json_decode(Parent::executeGetRequest($getUrl, self::API_VERSION_2), true); //indirect RBAC
        Yii::trace("ASSET DATA: ".json_encode($getAssetDataResponse));

        // Reading the response from the the api and filling the surveyorGridView
        $getUrl = 'dispatch%2Fget-surveyors&' . http_build_query([
                'filter' => $searchFilterVal,
            ]);
        Yii::trace("surveyors " . $getUrl);
        $surveyorsResponse = json_decode(Parent::executeGetRequest($getUrl, self::API_VERSION_2), true); // indirect rbac
        Yii::trace("Surveyors response " . json_encode($surveyorsResponse));

        // Put data in data provider
        $assetDataProvider = new ArrayDataProvider
        ([
            'allModels' => $getAssetDataResponse['assets'],
            'pagination' => false,
        ]);
        $assetDataProvider->key = 'WorkOrderID';
        $surveyorList = [];
        $surveyorList = $surveyorsResponse['users'];

        //todo: set paging on both tables
        // set pages to dispatch table
        $pages = new Pagination($getAssetDataResponse['pages']);

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('view_asset_modal', [
                'assetDataProvider' => $assetDataProvider,
                'model' => $model,
                //'pages' => $pages,
                'surveyorList' => $surveyorList,
            ]);
        } else {
            return $this->render('view_asset_modal', [
                'assetDataProvider' => $assetDataProvider,
                'model' => $model,
                //'pages' => $pages,
                'surveyorList' => $surveyorList,
            ]);
        }
    }

    public function GenerateUnassignedData(array $mapGridArr, array $assignedToIDs){
        $unassignedArr = [];
        for ($i = 0; $i < count($mapGridArr); $i++){
            $data = array(
                'MapGrid' => $mapGridArr[$i],
                'AssignedUserID' => $assignedToIDs[$i],
            );
            array_push($unassignedArr, $data);
        }
        $unassignedArr['data'] = $unassignedArr;
        return $unassignedArr;
    }
}