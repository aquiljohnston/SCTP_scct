<?php

namespace app\controllers;

use Exception;
use InspectionRequest;
use Yii;
use yii\data\ArrayDataProvider;
use yii\web\ForbiddenHttpException;
use yii\web\Response;
use yii\data\Pagination;

class InspectionController extends \app\controllers\BaseController
{
    public function actionIndex()
    {
        try {

            // Check if user has permission to view dispatch page
            //self::requirePermission("viewDispatch");

            $model = new \yii\base\DynamicModel([
                'inspectionfilter', 'pagesize', 'mapgridfilter', 'sectionnumberfilter'
            ]);
            $model->addRule('mapgridfilter', 'string', ['max' => 32])
                ->addRule('sectionnumberfilter', 'string', ['max' => 32])
                ->addRule('inspectionfilter', 'string', ['max' => 32])
                ->addRule('pagesize', 'string', ['max' => 32]);

            // Verify logged in
            if (Yii::$app->user->isGuest) {
                return $this->redirect(['/login']);
            }

            //check request
            if ($model->load(Yii::$app->request->queryParams)) {

                $inspectionPageSizeParams = $model->pagesize;
                $inspectionFilterParams = $model->inspectionfilter;
                $inspectionMapGridSelectedParams = $model->mapgridfilter;
            } else {
                $inspectionPageSizeParams = 50;
                $inspectionFilterParams = "";
                $inspectionMapGridSelectedParams = "";
            }

            // get the page number for assigned table
            if (isset($_GET['inspectionPageNumber']) && $_GET['inspectionTableRecordsUpdate'] != "true") {
                $pageAt = $_GET['inspectionPageNumber'];
            } else {
                $pageAt = 1;
            }

            $getUrl = 'inspection%2Fget-map-grids&' . http_build_query([
                    'mapGridSelected' => $inspectionMapGridSelectedParams,
                    'filter' => $inspectionFilterParams,
                    'listPerPage' => $inspectionPageSizeParams,
                    'page' => $pageAt,
                ]);
            $getInspectionDataResponse = json_decode(Parent::executeGetRequest($getUrl, self::API_VERSION_2), true); //indirect RBAC

            $inspectionData = $getInspectionDataResponse['mapGrids'];

            // Put data in data provider
            // render page
            $inspectionDataProvider = new ArrayDataProvider
            ([
                'allModels' => $inspectionData,
                'pagination' => false,
            ]);
            // dispatch section data provider

            $inspectionDataProvider->key = 'MapGrid';

            //todo: set paging on both tables
            // set pages to dispatch table
            $pages = new Pagination($getInspectionDataResponse['pages']);

            //todo: check permission to dispatch work
            $can = 1;

            if (Yii::$app->request->isAjax) {
                return $this->renderAjax('index', [
                    'inspectionDataProvider' => $inspectionDataProvider,
                    'model' => $model,
                    'can' => $can,
                    'pages' => $pages,
                    'inspectionFilterParams' => $inspectionFilterParams,
                    'inspectionPageSizeParams' => $inspectionPageSizeParams,
                ]);
            } else {
                return $this->render('index', [
                    'inspectionDataProvider' => $inspectionDataProvider,
                    'model' => $model,
                    'can' => $can,
                    'pages' => $pages,
                    'inspectionFilterParams' => $inspectionFilterParams,
                    'inspectionPageSizeParams' => $inspectionPageSizeParams,
                ]);
            }
        } catch (ForbiddenHttpException $e) {
            //Yii::$app->runAction('login/user-logout');
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

        $getUrl = 'inspection%2Fget-map-grids&' . http_build_query([
                'mapGridSelected' => $mapGridSelected,
                'filter' => $sectionFilterParams,
                'listPerPage' => $sectionPageSizeParams,
                'page' => $pageAt,
            ]);

        $getSectionDataResponse = json_decode(Parent::executeGetRequest($getUrl, self::API_VERSION_2), true); //indirect RBAC
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
    public function actionViewSectionDetailModal($searchFilterVal = null, $mapGridSelected = null, $sectionNumberSelected = null)
    {
        Yii::trace("CALL VIEW ASSET");
        $model = new \yii\base\DynamicModel([
            'modalSearch', 'mapGridSelected', 'sectionNumberSelected',
        ]);
        $model->addRule('modalSearch', 'string', ['max' => 32])
            ->addRule('mapGridSelected', 'string', ['max' => 32])
            ->addRule('sectionNumberSelected', 'string', ['max' => 32]);

        // Verify logged in
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['/login']);
        }

        if (Yii::$app->request->get()){
            //todo: need to remove hard code value
            $viewSectionDetailFilterParams = $searchFilterVal;
            $mapGridSelectedParam = $mapGridSelected;
            $sectionNumberSelectedParam = $sectionNumberSelected;
            $viewAssetPageSizeParams = 50;
            $pageAt = 1;
        }else{
            $viewSectionDetailFilterParams = "";
            $viewAssetPageSizeParams = 50;
            $pageAt = 1;
            $searchFilterVal = "";
        }

        $getUrl = 'inspection%2Fget-inspections&' . http_build_query([
                'mapGridSelected' => $mapGridSelectedParam,
                'sectionNumberSelected' => $sectionNumberSelectedParam,
                'filter' => $viewSectionDetailFilterParams,
                'listPerPage' => $viewAssetPageSizeParams,
                'page' => $pageAt,
            ]);
        $getSectionDetailDataResponse = json_decode(Parent::executeGetRequest($getUrl, self::API_VERSION_2), true); //indirect RBAC
        Yii::trace("SECTION DETAIL DATA: ".json_encode($getSectionDetailDataResponse));

        // Put data in data provider
        $sectionDetailDataProvider = new ArrayDataProvider
        ([
            'allModels' => $getSectionDetailDataResponse['inspections'],
            'pagination' => false,
        ]);
        $sectionDetailDataProvider->key = 'InspectionID';

        // set pages to dispatch table
        $pages = new Pagination($getSectionDetailDataResponse['pages']);

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('view_detail_modal', [
                'sectionDetailDataProvider' => $sectionDetailDataProvider,
                'model' => $model,
                //'pages' => $pages,
                'searchFilterVal' => $viewSectionDetailFilterParams,
                'mapGridSelected' => $mapGridSelectedParam,
                'sectionNumberSelected' => $sectionNumberSelectedParam,
            ]);
        } else {
            return $this->render('view_detail_modal', [
                'sectionDetailDataProvider' => $sectionDetailDataProvider,
                'model' => $model,
                //'pages' => $pages,
                'searchFilterVal' => $viewSectionDetailFilterParams,
                'mapGridSelected' => $mapGridSelectedParam,
                'sectionNumberSelected' => $sectionNumberSelectedParam,
            ]);
        }
    }

    /**
     * render expandable event row
     * @return string|Response
     */
    public function actionViewEvent($mapGridSelected = null, $sectionNumberSelected = null)
    {
        $model = new \yii\base\DynamicModel([
            'eventfilter', 'pagesize'
        ]);
        $model->addRule('eventfilter', 'string', ['max' => 32])
            ->addRule('pagesize', 'string', ['max' => 32]);

        // Verify logged in
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['/login']);
        }

        //check request
        if ($model->load(Yii::$app->request->queryParams)) {

            Yii::trace("eventfilter " . $model->eventfilter);
            Yii::trace("pagesize " . $model->pagesize);
            $eventPageSizeParams = $model->pagesize;
            $eventFilterParams = $model->eventfilter;
        } else {
            $eventPageSizeParams = 10;
            $eventFilterParams = "";
        }

        // get the page number for assigned table
        if (isset($_GET['userPage'])) {
            $pageAt = $_GET['userPage'];
        } else {
            $pageAt = 1;
        }
        // get the key to generate event table
        if (isset($_POST['expandRowKey']))
            $inspectionID = $_POST['expandRowKey'];
        else
            $inspectionID = "";

        $getUrl = 'inspection%2Fget-inspections&' . http_build_query([
                'mapGridSelected' => $mapGridSelected,
                'sectionNumberSelected' => $sectionNumberSelected,
                'inspectionID' => $inspectionID,
                'page' => $pageAt,
            ]);
        $getEventDataResponse = json_decode(Parent::executeGetRequest($getUrl, self::API_VERSION_2), true); //indirect RBAC
        $eventData = $getEventDataResponse['events'];

        // Put data in data provider
        // dispatch event data provider
        $eventDataProvider = new ArrayDataProvider
        ([
            'allModels' => $eventData,
            'pagination' => false,
        ]);

        $eventDataProvider->key = 'InspectionID';

        // set pages to dispatch table
        $pages = new Pagination($getEventDataResponse['pages']);

        //todo: check permission to dispatch work
        $can = 1;

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('_event-expand', [
                'eventDataProvider' => $eventDataProvider,
                'model' => $model,
                'can' => $can,
                'pages' => $pages,
                'eventFilterParams' => $eventFilterParams,
                'eventPageSizeParams' => $eventPageSizeParams,
            ]);
        } else {
            return $this->render('_event-expand', [
                'eventDataProvider' => $eventDataProvider,
                'model' => $model,
                'can' => $can,
                'pages' => $pages,
                'eventFilterParams' => $eventFilterParams,
                'eventPageSizeParams' => $eventPageSizeParams,
            ]);
        }
    }
}