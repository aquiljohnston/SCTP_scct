<?php

namespace app\modules\dispatch\controllers;

use Yii;
use yii\data\ArrayDataProvider;
use yii\data\Pagination;
use app\constants\Constants;
use yii\web\UnauthorizedHttpException;
use yii\web\ForbiddenHttpException;
use Exception;

class AssignedController extends \app\controllers\BaseController
{
    public function actionIndex()
    {
        try {
            //Check if user has permission to view assigned page
            self::requirePermission("viewAssigned");

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
            $getAssignedDataResponse = json_decode(Parent::executeGetRequest($getUrl, Constants::API_VERSION_2), true); //indirect RBAC
            Yii::trace("ASSIGNED DATA: " . json_encode($getAssignedDataResponse));
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

            // Sorting Unassign table
            $assignedDataProvider->sort = [
                'attributes' => [
                    'MapGrid' => [
                        'asc' => ['MapGrid' => SORT_ASC],
                        'desc' => ['MapGrid' => SORT_DESC]
                    ],
                    'AssignedUser' => [
                        'asc' => ['AssignedUser' => SORT_ASC],
                        'desc' => ['AssignedUser' => SORT_DESC]
                    ],
                    'ComplianceStart' => [
                        'asc' => ['ComplianceStart' => SORT_ASC],
                        'desc' => ['ComplianceStart' => SORT_DESC]
                    ],
                    'ComplianceEnd' => [
                        'asc' => ['ComplianceEnd' => SORT_ASC],
                        'desc' => ['ComplianceEnd' => SORT_DESC]
                    ],
                    'Percent Completed' => [
                        'asc' => ['Percent Completed' => SORT_ASC],
                        'desc' => ['Percent Completed' => SORT_DESC]
                    ],
                    'Counts' => [
                        'asc' => ['Counts' => SORT_ASC],
                        'desc' => ['Counts' => SORT_DESC]
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
        } catch (UnauthorizedHttpException $e){
            Yii::$app->response->redirect(['login/index']);
        } catch (ForbiddenHttpException $e) {
            Yii::$app->runAction('login/user-logout');
            //throw new ForbiddenHttpException('You do not have adequate permissions to perform this action.');
        } catch (Exception $e) {
            Yii::$app->runAction('login/user-logout');
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
                $json_data = json_encode($data);
                Yii::trace("Unassigned Data: ".$json_data);

                // post url
                $deleteUrl = 'dispatch%2Funassign';
                $deleteResponse = Parent::executeDeleteRequest($deleteUrl, $json_data, Constants::API_VERSION_2); // indirect rbac
                Yii::trace("unassignputResponse " . $deleteResponse);

            } else {
                throw new \yii\web\BadRequestHttpException;
            }
        } catch (UnauthorizedHttpException $e){
            Yii::$app->response->redirect(['login/index']);
        }catch (ForbiddenHttpException $e) {
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
        $getSectionDataResponseResponse = json_decode(Parent::executeGetRequest($getUrl, Constants::API_VERSION_2), true); //indirect RBAC
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
    public function actionViewAsset($searchFilterVal = null, $mapGridSelected = null, $sectionNumberSelected = null, $inspectionType=null)
    {
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
            $viewAssetFilterParams = $searchFilterVal;
            $mapGridSelectedParam = $mapGridSelected;
            $sectionNumberSelectedParam = $sectionNumberSelected;
			$inspectionType             = $inspectionType; 
			//should this not be hard coded?
            $viewAssetPageSizeParams = 200;
            $pageAt = Yii::$app->getRequest()->getQueryParam('viewAssignedAssetPageNumber');
        }else{
            $viewAssetFilterParams = "";
            $viewAssetPageSizeParams = 200;
            $pageAt = 1;
        }

        $getSurveyorUrl = 'dispatch%2Fget-surveyors&' . http_build_query([
                'filter' => '',
            ]);
        $getSurveyorsResponse = json_decode(Parent::executeGetRequest($getSurveyorUrl, Constants::API_VERSION_2), true); // indirect rbac


        $getUrl = 'dispatch%2Fget-assigned-assets&' . http_build_query([
                'mapGridSelected' => $mapGridSelectedParam,
                'sectionNumberSelected' => $sectionNumberSelectedParam,
                'filter' => $viewAssetFilterParams,
                'listPerPage' => $viewAssetPageSizeParams,
                'page' => $pageAt,
				'inspectionType'        => $inspectionType,
            ]);
        $getAssetDataResponse = json_decode(Parent::executeGetRequest($getUrl, Constants::API_VERSION_2), true); //indirect RBAC

        $data = DispatchController::reGenerateAssetsData($getAssetDataResponse['assets'], $getSurveyorsResponse['users']);

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
                'assetDataProvider' => $assetDataProvider,
                'model' => $model,
                'pages' => $pages,
                'searchFilterVal' => $viewAssetFilterParams,
                'mapGridSelected' => $mapGridSelectedParam,
                'sectionNumberSelected' => $sectionNumberSelectedParam,
				'inspectionType'        => $inspectionType,
            ]);
        } else {
            return $this->render('view_asset_modal', [
                'assetDataProvider' => $assetDataProvider,
                'model' => $model,
                'pages' => $pages,
                'searchFilterVal' => $viewAssetFilterParams,
                'mapGridSelected' => $mapGridSelectedParam,
                'sectionNumberSelected' => $sectionNumberSelectedParam,
				'inspectionType'        => $inspectionType,
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