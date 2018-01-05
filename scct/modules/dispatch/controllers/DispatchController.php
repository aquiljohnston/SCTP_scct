<?php

namespace app\modules\dispatch\controllers;

use Exception;
use InspectionRequest;
use Yii;
use yii\data\ArrayDataProvider;
use yii\web\ForbiddenHttpException;
use yii\web\Response;
use yii\data\Pagination;
use yii\web\ServerErrorHttpException;
use yii\web\UnauthorizedHttpException;
use yii\web\View;
use app\constants\Constants;

class DispatchController extends \app\controllers\BaseController
{
    public function actionHeavyDispatch()
    {

        try {

            // Check if user has permission to view dispatch page
            self::requirePermission("viewDispatch");

            $model = new \yii\base\DynamicModel([
                'dispatchfilter', 'pagesize', 'mapgridfilter', 'sectionnumberfilter'
            ]);
            $model->addRule('mapgridfilter', 'string', ['max' => 32])
                ->addRule('sectionnumberfilter', 'string', ['max' => 32])
                ->addRule('dispatchfilter', 'string', ['max' => 32])
                ->addRule('pagesize', 'string', ['max' => 32]);

            // Verify logged in
            if (Yii::$app->user->isGuest) {
                return $this->redirect(['/login']);
            }

            //check request
            if ($model->load(Yii::$app->request->queryParams)) {

                //Yii::trace("dispatchfilter " . $model->dispatchfilter);
                //Yii::trace("pagesize " . $model->pagesize);
                //Yii::trace("mapgridfilter " . $model->mapgridfilter);
                //Yii::trace("sectionnumberfilter " . $model->sectionnumberfilter);
                $dispatchPageSizeParams = $model->pagesize;
                $dispatchFilterParams = $model->dispatchfilter;
                $dispatchMapGridSelectedParams = $model->mapgridfilter;
                $dispatchSectionNumberSelectedParams = $model->sectionnumberfilter;
            } else {
                $dispatchPageSizeParams = 50;
                $dispatchFilterParams = "";
                $dispatchMapGridSelectedParams = "";
                $dispatchSectionNumberSelectedParams = "";
            }

            // get the page number for assigned table
            if (isset($_GET['dispatchPageNumber']) && $_GET['dispatchTableRecordsUpdate'] != "true") {
                $pageAt = $_GET['dispatchPageNumber'];
            } else {
                $pageAt = 1;
            }

            $getUrl = 'dispatch%2Fget-available&' . http_build_query([
                    'filter' => $dispatchFilterParams,
                    'listPerPage' => $dispatchPageSizeParams,
                    'page' => $pageAt,
                ]);
            $getDispatchDataResponse = json_decode(Parent::executeGetRequest($getUrl, Constants::API_VERSION_2), true); //indirect RBAC
            //Yii::trace("DISPATCH DATA: " . json_encode($getDispatchDataResponse));

            $dispatchData = $getDispatchDataResponse['mapGrids'];
			$divisionFlag = $getDispatchDataResponse['divisionFlag'];

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

            //todo: check permission to dispatch work
            $can = 1;

            // Sorting Dispatch table
            $dispatchDataProvider->sort = [
                'attributes' => [
                    'MapGrid' => [
                        'asc' => ['MapGrid' => SORT_ASC],
                        'desc' => ['MapGrid' => SORT_DESC]
                    ],
                    'Division' => [
                        'asc' => ['Division' => SORT_ASC],
                        'desc' => ['Division' => SORT_DESC]
                    ],
                    'ComplianceStart' => [
                        'asc' => ['ComplianceStart' => SORT_ASC],
                        'desc' => ['ComplianceStart' => SORT_DESC]
                    ],
                    'ComplianceEnd' => [
                        'asc' => ['ComplianceEnd' => SORT_ASC],
                        'desc' => ['ComplianceEnd' => SORT_DESC]
                    ],
                    'AvailableWorkOrderCount' => [
                        'asc' => ['AvailableWorkOrderCount' => SORT_ASC],
                        'desc' => ['AvailableWorkOrderCount' => SORT_DESC]
                    ],
                    'InspectionType' => [
                        'asc' => ['InspectionType' => SORT_ASC],
                        'desc' => ['InspectionType' => SORT_DESC]
                    ],
                    'BillingCode' => [
                        'asc' => ['BillingCode' => SORT_ASC],
                        'desc' => ['BillingCode' => SORT_DESC]
                    ],
                    'OfficeName' => [
                        'asc' => ['OfficeName' => SORT_ASC],
                        'desc' => ['OfficeName' => SORT_DESC]
                    ]
                ]
            ];

            if (Yii::$app->request->isAjax) {
                //Prevent CSS from being loaded twice, causing a visual bug
                Yii::$app->assetManager->bundles = [
                    'yii\bootstrap\BootstrapPluginAsset' => false,
                    'yii\bootstrap\BootstrapAsset' => false,
                    'yii\web\JqueryAsset' => false,
                ];
                return $this->renderAjax('index', [
                    'dispatchDataProvider' => $dispatchDataProvider,
					'divisionFlag' => $divisionFlag,
                    'model' => $model,
                    'can' => $can,
                    'pages' => $pages,
                    'dispatchFilterParams' => $dispatchFilterParams,
                    'dispatchPageSizeParams' => $dispatchPageSizeParams,
                ]);
            } else {
                return $this->render('index', [
                    'dispatchDataProvider' => $dispatchDataProvider,
					'divisionFlag' => $divisionFlag,
                    'model' => $model,
                    'can' => $can,
                    'pages' => $pages,
                    'dispatchFilterParams' => $dispatchFilterParams,
                    'dispatchPageSizeParams' => $dispatchPageSizeParams,
                ]);
            }
        } catch (UnauthorizedHttpException $e){
            return $this->redirect(['/login/index']);
        } catch (ForbiddenHttpException $e) {
            throw new ForbiddenHttpException('You do not have adequate permissions to perform this action.');
        } catch (Exception $e) {
            return $this->redirect(['/login/index']);
        }
    }

    public function actionIndex() {
        try{
            return $this->render('lightDispatch');
        } catch (UnauthorizedHttpException $e){
            Yii::$app->response->redirect(['login/index']);
        } catch (ForbiddenHttpException $e) {
            throw new ForbiddenHttpException('You do not have adequate permissions to perform this action.');
        } catch (Exception $e) {
            Yii::$app->runAction('login/user-logout');
            return "";
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

        $getUrl = 'dispatch%2Fget-available&' . http_build_query([
                'mapGridSelected' => $mapGridSelected,
                'filter' => $sectionFilterParams,
                'listPerPage' => $sectionPageSizeParams,
                'page' => $pageAt,
            ]);

        $getSectionDataResponse = json_decode(Parent::executeGetRequest($getUrl, Constants::API_VERSION_2), true); //indirect RBAC
        //Yii::trace("DISPATCH DATA: " . json_encode($getSectionDataResponse));
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
                'sectionPageSizeParams' => $sectionPageSizeParams
            ]);
        }
    }

    /**
     * render asset modal
     * @return string|Response
     */
    public function actionViewAsset($searchFilterVal = null, $mapGridSelected = null, $sectionNumberSelected = null, $recordsPerPageSelected = 200, $inspectionType=null,$billingCode=null)
    {
        Yii::trace("CALL VIEW ASSET");

        $model = new \yii\base\DynamicModel([
            'modalSearch', 'mapGridSelected', 'sectionNumberSelected', 'pagesize'
        ]);
        $model->addRule('modalSearch', 'string', ['max' => 32])
            ->addRule('mapGridSelected', 'string', ['max' => 32])
            ->addRule('sectionNumberSelected', 'string', ['max' => 32])
            ->addRule('pagesize', 'string', ['max' => 32]);

        // Verify logged in
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['/login']);
        }

        if (Yii::$app->request->get()){
            //todo: need to remove hard code value
            $viewAssetFilterParams      = $searchFilterVal;
            $mapGridSelectedParam       = $mapGridSelected;
            $sectionNumberSelectedParam = $sectionNumberSelected;
            $viewAssetPageSizeParams    = $recordsPerPageSelected;
            $inspectionType             = $inspectionType; 
            $billingCode                = $billingCode; 

            $pageAt = Yii::$app->getRequest()->getQueryParam('viewDispatchAssetPageNumber');

            //include inspection type and billingType for dispatch assets query
            //$inspectionType = Yii::$app->getRequest()->getQueryParam('inspectionType');
           // $billingCode = Yii::$app->getRequest()->getQueryParam('billingCode');

            Yii::trace('PAGE AT : '.$pageAt);
        } else {
            $viewAssetFilterParams = "";
            $pageAt = 1;
        }

        $getSurveyorUrl = 'dispatch%2Fget-surveyors&' . http_build_query([
                'filter' => '',
            ]);

        $getSurveyorsResponse = json_decode(Parent::executeGetRequest($getSurveyorUrl, Constants::API_VERSION_2), true); // indirect rbac

        $getUrl = 'dispatch%2Fget-available-assets&' . http_build_query([
                'mapGridSelected'       => $mapGridSelectedParam,
                'sectionNumberSelected' => $sectionNumberSelectedParam,
                'filter'                => $viewAssetFilterParams,
                'listPerPage'           => $viewAssetPageSizeParams,
                'page'                  => $pageAt,
                'inspectionType'        => $inspectionType,
                'billingCode'           => $billingCode
            ]);

        $getAssetDataResponse = json_decode(Parent::executeGetRequest($getUrl, Constants::API_VERSION_2), true); //indirect RBAC

        //var_dump($getAssetDataResponse); exit();

        $data = self::reGenerateAssetsData($getAssetDataResponse['assets'], $getSurveyorsResponse['users']);
        Yii::trace("reGenerateAssetsData " . json_encode($data));

        // Put data in data provider
        $assetDataProvider = new ArrayDataProvider
        ([
            'allModels' => $data,
            'pagination' => false,
        ]);
        $assetDataProvider->key = 'WorkOrderID';

        //todo: set paging on both tables
        // set pages to dispatch table
        $pages = new Pagination($getAssetDataResponse['pages']);

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('view_asset_modal', [
                'assetDataProvider'         => $assetDataProvider,
                'model'                     => $model,
                'pages'                     => $pages,
                'searchFilterVal'           => $viewAssetFilterParams,
                'mapGridSelected'           => $mapGridSelectedParam,
                'sectionNumberSelected'     => $sectionNumberSelectedParam,
                'viewAssetPageSizeParams'   => $viewAssetPageSizeParams,
                'inspectionType'            => $inspectionType,
                'billingCode'               => $billingCode

            ]);
        } else {
            return $this->render('view_asset_modal', [
                'assetDataProvider'         => $assetDataProvider,
                'model'                     => $model,
                'pages'                     => $pages,
                'searchFilterVal'           => $viewAssetFilterParams,
                'mapGridSelected'           => $mapGridSelectedParam,
                'sectionNumberSelected'     => $sectionNumberSelectedParam,
                'viewAssetPageSizeParams'   => $viewAssetPageSizeParams,
                'inspectionType'            => $inspectionType,
                'billingCode'               => $billingCode
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
                $json_data = json_encode($data);
                Yii::trace("DISPATCH DATA: " . $json_data);

                // post url
                $putUrl = 'dispatch%2Fdispatch';
                $putResponse = Parent::executePostRequest($putUrl, $json_data, Constants::API_VERSION_2); // indirect rbac
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

    public static function reGenerateAssetsData($assetsData, $surveyorList){
        $newAssetsData = array();
        foreach ($assetsData as $item){
            $item['userList'] = $surveyorList;
            $newAssetsData[] = $item;
        }
        return $newAssetsData;
    }

}