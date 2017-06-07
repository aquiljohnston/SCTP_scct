<?php

namespace app\modules\dispatch\controllers;

use Exception;
use InspectionRequest;
use Yii;
use yii\data\ArrayDataProvider;
use yii\web\ForbiddenHttpException;
use yii\web\Response;
use yii\data\Pagination;

class DispatchController extends \app\controllers\BaseController
{
    public function actionIndex()
    {
        try {
            $model = new \yii\base\DynamicModel([
                'division', 'dispatchfilter', 'pagesize', 'mapgridfilter', 'sectionnumberfilter'
            ]);
            $model->addRule('division', 'string', ['max' => 32])
                ->addRule('mapgridfilter', 'string', ['max' => 32])
                ->addRule('sectionnumberfilter', 'string', ['max' => 32])
                ->addRule('dispatchfilter', 'string', ['max' => 32])
                ->addRule('pagesize', 'string', ['max' => 32]);

            // Verify logged in
            if (Yii::$app->user->isGuest) {
                return $this->redirect(['/login']);
            }

            //check request
            if ($model->load(Yii::$app->request->queryParams)) {

                Yii::trace("division " . $model->division);
                Yii::trace("dispatchfilter " . $model->dispatchfilter);
                Yii::trace("pagesize " . $model->pagesize);
                Yii::trace("mapgridfilter " . $model->mapgridfilter);
                Yii::trace("sectionnumberfilter " . $model->sectionnumberfilter);
                $divisionParams = $model->division;
                $dispatchPageSizeParams = $model->pagesize;
                $dispatchFilterParams = $model->dispatchfilter;
                $dispatchMapGridSelectedParams = $model->mapgridfilter;
                $dispatchSectionNumberSelectedParams = $model->sectionnumberfilter;
            } else {
                $divisionParams = "";
                $dispatchPageSizeParams = 10;
                $dispatchFilterParams = "";
                $dispatchMapGridSelectedParams = "";
                $dispatchSectionNumberSelectedParams = "";
            }

            // get the page number for assigned table
            if (isset($_GET['userPage'])) {
                $pageAt = $_GET['userPage'];
            } else {
                $pageAt = 1;
            }

            $getUrl = 'dispatch%2Fget&' . http_build_query([
                    'mapGridSelected' => $dispatchMapGridSelectedParams,
                    'sectionNumberSelected' => $dispatchSectionNumberSelectedParams,
                    'filter' => $dispatchFilterParams,
                    'listPerPage' => $dispatchPageSizeParams,
                    'page' => $pageAt,
                ]);
            $getDispatchDataResponse = json_decode(Parent::executeGetRequest($getUrl, self::API_VERSION_2), true); //indirect RBAC
            Yii::trace("DISPATCH DATA: " . json_encode($getDispatchDataResponse));

            $dispatchData = $getDispatchDataResponse['mapGrids'];

            // Put data in data provider
            // render page
            $dispatchDataProvider = new ArrayDataProvider
            ([
                'allModels' => $dispatchData,
                'pagination' => false,
            ]);
            // dispatch section data provider

            $dispatchDataProvider->key = 'MapGrid';

            //todo: set paging on both tables
            // set pages to dispatch table
            $pages = new Pagination($getDispatchDataResponse['pages']);
            $divisionList = [];
            $workCenterList = [];

            //todo: check permission to dispatch work
            $can = 1;

            if (Yii::$app->request->isAjax) {
                return $this->renderAjax('index', [
                    'dispatchDataProvider' => $dispatchDataProvider,
                    'model' => $model,
                    'can' => $can,
                    'pages' => $pages,
                    'divisionList' => $divisionList,
                    'workCenterList' => $workCenterList,
                    'dispatchFilterParams' => $dispatchFilterParams,
                    'dispatchPageSizeParams' => $dispatchPageSizeParams,
                ]);
            } else {
                return $this->render('index', [
                    'dispatchDataProvider' => $dispatchDataProvider,
                    'model' => $model,
                    'can' => $can,
                    'pages' => $pages,
                    'divisionList' => $divisionList,
                    'workCenterList' => $workCenterList,
                    'dispatchFilterParams' => $dispatchFilterParams,
                    'dispatchPageSizeParams' => $dispatchPageSizeParams,
                ]);
            }
        } catch (ForbiddenHttpException $e) {
            throw new ForbiddenHttpException('You do not have adequate permissions to perform this action.');
        } catch (Exception $e) {
            Yii::$app->runAction('login/user-logout');
        }
    }

    /**
     * render expandable section row
     * @return string|Response
     */
    public function actionViewSection()
    {
        $model = new \yii\base\DynamicModel([
            'sectionfilter', 'pagesize'
        ]);
        $model->addRule('sectionfilter', 'string', ['max' => 32])
            ->addRule('pagesize', 'string', ['max' => 32]);

        // Verify logged in
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['/login']);
        }

        //check request
        if ($model->load(Yii::$app->request->queryParams)) {

            Yii::trace("sectionfilter " . $model->sectionfilter);
            Yii::trace("pagesize " . $model->pagesize);
            $sectionPageSizeParams = $model->pagesize;
            $sectionFilterParams = $model->sectionfilter;
        } else {
            $sectionPageSizeParams = 10;
            $sectionFilterParams = "";
        }

        // get the page number for assigned table
        if (isset($_GET['userPage'])) {
            $pageAt = $_GET['userPage'];
        } else {
            $pageAt = 1;
        }
        // get the key to generate section table
        if (isset($_POST['expandRowKey']))
            $mapGridSelected = $_POST['expandRowKey'];
        else
            $mapGridSelected = "";

        $getUrl = 'dispatch%2Fget&' . http_build_query([
                'mapGridSelected' => $mapGridSelected,
                'filter' => $sectionFilterParams,
                'listPerPage' => $sectionPageSizeParams,
                'page' => $pageAt,
            ]);

        $getSectionDataResponse = json_decode(Parent::executeGetRequest($getUrl, self::API_VERSION_2), true); //indirect RBAC
        Yii::trace("DISPATCH DATA: " . json_encode($getSectionDataResponse));
        $sectionData = $getSectionDataResponse['sections'];

        // Put data in data provider
        // dispatch section data provider
        $sectionDataProvider = new ArrayDataProvider
        ([
            'allModels' => $sectionData,
            'pagination' => false,
        ]);

        $sectionDataProvider->key = 'SectionNumber';

        // set pages to dispatch table
        $pages = new Pagination($getSectionDataResponse['pages']);

        //todo: check permission to dispatch work
        $can = 1;

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('_section-expand', [
                'sectionDataProvider' => $sectionDataProvider,
                'model' => $model,
                'can' => $can,
                'pages' => $pages,
                'sectionFilterParams' => $sectionFilterParams,
                'sectionPageSizeParams' => $sectionPageSizeParams,
            ]);
        } else {
            return $this->render('_section-expand', [
                'sectionDataProvider' => $sectionDataProvider,
                'model' => $model,
                'can' => $can,
                'pages' => $pages,
                'sectionFilterParams' => $sectionFilterParams,
                'sectionPageSizeParams' => $sectionPageSizeParams,
            ]);
        }
    }

    /**
     * render asset modal
     * @return string|Response
     */
    public function actionViewAsset($id)
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

        /*$getUrl = 'dispatch%2Fget-assigned&' . http_build_query([
                'filter' => $dispatchFilterParams,
                'listPerPage' => $dispatchPageSizeParams,
                'page' => $pageAt,
            ]);
        $getAssetDataResponse = json_decode(Parent::executeGetRequest($getUrl, self::API_VERSION_2), true); //indirect RBAC
        Yii::trace("ASSET DATA: ".json_encode($getAssetDataResponse));

        // Put data in data provider
        $assetDataProvider = new ArrayDataProvider
        ([
            'allModels' => $getAssetDataResponse,
            'pagination' => false,
        ]);
        //$assetDataProvider->key = 'MapGrid';

        //todo: set paging on both tables
        // set pages to dispatch table
        $pages = new Pagination($getAssetDataResponse['pages']);
        $divisionList = [];
        $workCenterList = [];

        //todo: check permission to dispatch work
        $can = 1;*/

        $assetDataProvider = new ArrayDataProvider
        ([
            'allModels' => [],
            'pagination' => false,
        ]);
        $surveyorList = [];

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('_section-expand', [
                'assetDataProvider' => $assetDataProvider,
                'model' => $model,
                //'pages' => $pages,
                'surveyorList' => $surveyorList,
            ]);
        } else {
            return $this->render('_section-expand', [
                'assetDataProvider' => $assetDataProvider,
                'model' => $model,
                //'pages' => $pages,
                'surveyorList' => $surveyorList,
            ]);
        }
    }

    /**
     * Dispatch function
     * @throws ForbiddenHttpException
     */
    public function actionDispatch()
    {
        try {
            if (Yii::$app->request->isAjax) {
                $data = Yii::$app->request->post();

                if ($data['SectionNumber'][0] == 000) {
                    $sectionNumber = null;
                    $dispatchMapArr = [];
                    $dispatchMap = array(
                        'MapGrid' => $data['MapGrid'][0],
                        'AssignedUserID' => $data['AssignedUserID'][0],
                    );
                    array_push($dispatchMapArr, $dispatchMap);
                    $dispatchData = array(
                        'dispatchMap' => $dispatchMapArr,
                    );
                } else {
                    $sectionNumber = $data['SectionNumber'][0];
                    $dispatchSectionArr = [];
                    $dispatchSection = array(
                        'MapGrid' => $data['MapGrid'],
                        'AssignedUserID' => $data['AssignedUserID'][0],
                        'SectionNumber' => $sectionNumber,//$data['SectionNumber'][0],
                    );
                    array_push($dispatchSectionArr, $dispatchSection);
                    $dispatchData = array(
                        'dispatchSection' => $dispatchSectionArr,
                    );
                }

                $json_data = json_encode($dispatchData);

                Yii::trace("DISPATCH DATA: " . $json_data);
                // post url
                $putUrl = 'dispatch%2Fdispatch';
                $putResponse = Parent::executePostRequest($putUrl, $json_data, self::API_VERSION_2); // indirect rbac
                Yii::trace("dispatchputResponse " . $putResponse);

            }
        } catch (ForbiddenHttpException $e) {
            throw new ForbiddenHttpException;
        } catch (Exception $e) {
            Yii::$app->runAction('login/user-logout');
        }
    }

    /**
     * CheckExistingWorkCenter function
     * @param $divisionDefaultVal
     * @param $workCenterDefaultVal
     * @param $ErrorMsg
     * @return array
     */
    public function CheckExistingDivision($divisionDefaultVal = null, $workCenterDefaultVal = null, $ErrorMsg = null)
    {

        $divisionDefaultSelectedUrl = 'pge%2Fdropdown%2Fget-default-filter&screen=dispatch';
        $divisionDefaultSelectedResponse = Parent::executeGetRequest($divisionDefaultSelectedUrl); // indirect rbac
        $divisionDefaultSelection = json_decode($divisionDefaultSelectedResponse, true);

        // check if error key exists in the response
        if (array_key_exists("Error", $divisionDefaultSelection)) {
            $ErrorMsg = $divisionDefaultSelection['Error'];
        } else {
            $divisionDefaultVal = $divisionDefaultSelection[0]['Division'];
            $workCenterDefaultVal = $divisionDefaultSelection[0]['WorkCenter'];
        }
        return array($ErrorMsg, $divisionDefaultVal, $workCenterDefaultVal);
    }

    public function GenerateUnassignedData(array $mapGridArr, array $assignedToIDs)
    {
        $unassignedArr = [];
        for ($i = 0; $i < count($mapGridArr); $i++) {
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