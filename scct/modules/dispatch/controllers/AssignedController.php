<?php

namespace app\modules\dispatch\controllers;

use Yii;
use yii\data\ArrayDataProvider;
use yii\data\Pagination;
use app\constants\Constants;
use yii\web\UnauthorizedHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\ServerErrorHttpException;
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

			//"sort":"-Division"
            //get sort data
            if (isset($_GET['sort'])){
                $sort = $_GET['sort'];
                //parse sort data
                $sortField = str_replace('-', '', $sort, $sortCount);
                $sortOrder = $sortCount > 0 ? 'DESC' : 'ASC';
            } else {
                //default sort values
                $sortField = 'ComplianceEnd';
                $sortOrder = 'ASC';
            }
				
            //check request
            if ($model->load(Yii::$app->request->queryParams)) {
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
                    'page' => $pageAt,
					'sortField' => $sortField,
                    'sortOrder' => $sortOrder,
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
				'key' => function($assignedData){
					return array(
						'MapGrid' => $assignedData['MapGrid'],
                        'InspectionType' => $assignedData['InspectionType'],
                        'BillingCode' => $assignedData['BillingCode'],

					);
				},
            ]);

            // Sorting Unassign table
            $assignedDataProvider->sort = [
				'defaultOrder' => [$sortField => ($sortOrder == 'ASC') ? SORT_ASC : SORT_DESC],
                'attributes' => [
                    'MapGrid',
                    'AssignedUser',
                    'ComplianceStart',
                    'ComplianceEnd',
                    'Percent Completed',
                    'Counts',
                    'InspectionType',
                    'BillingCode',
                    'OfficeName'
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
        } catch(ForbiddenHttpException $e) {
            throw $e;
        } catch(ErrorException $e) {
            throw new \yii\web\HttpException(400);
        } catch(Exception $e) {
            throw new ServerErrorHttpException();
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
                $data = Yii::$app->request->post();
                $json_data = json_encode($data);

                // post url
                $deleteUrl = 'dispatch%2Funassign';
                $deleteResponse = Parent::executeDeleteRequest($deleteUrl, $json_data, Constants::API_VERSION_2); // indirect rbac

            } else {
                throw new \yii\web\BadRequestHttpException;
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
     * render expandable section row
     * @return string|Response
     */
    public function actionViewSection()
    {
		try{
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
			{
				$mapGridSelected = $_POST['expandRowKey']['MapGrid'];
				$inspectionType = $_POST['expandRowKey']['InspectionType'];
				$billingCode = $_POST['expandRowKey']['BillingCode'];
			}else{
				$mapGridSelected = "";
				$inspectionType = '';
				$billingCode = '';
			}

			$getUrl = 'dispatch%2Fget-assigned&' . http_build_query([
					'mapGridSelected' => $mapGridSelected,
					'inspectionType' => $inspectionType,
					'billingCode' => $billingCode,
					'filter' => $assignedFilterParams,
					'listPerPage' => $assignedPageSizeParams,
					'page' => $pageAt
				]);
			$getSectionDataResponseResponse = json_decode(Parent::executeGetRequest($getUrl, Constants::API_VERSION_2), true); //indirect RBAC
			$sectionData = $getSectionDataResponseResponse['sections'];

			//set paging on assigned table
			$pages = new Pagination($getSectionDataResponseResponse['pages']);

			$sectionDataProvider = new ArrayDataProvider
			([
				'allModels' => $sectionData,
				'pagination' => false,
				'key' => function ($sectionData) {
					return array(
						'SectionNumber' => $sectionData['SectionNumber'],
						'InspectionType' => $sectionData['InspectionType'],
						'BillingCode' => $sectionData['BillingCode'],
					);
				},
			]);

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
     * render asset modal
     * @return string|Response
     */
    public function actionViewAsset($searchFilterVal = null, $mapGridSelected = null, $billingCode = null, $sectionNumberSelected = null, $inspectionType=null)
    {
		try{
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
				$inspectionType = $inspectionType; 
				$billingCode = $billingCode;
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
					'billingCode' => $billingCode
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
			$params = [
				'assetDataProvider' => $assetDataProvider,
				'model' => $model,
				'pages' => $pages,
				'searchFilterVal' => $viewAssetFilterParams,
				'mapGridSelected' => $mapGridSelectedParam,
				'sectionNumberSelected' => $sectionNumberSelectedParam,
				'inspectionType' => $inspectionType,
				'billingCode' => $billingCode,
			];
			if (Yii::$app->request->isAjax) {
				return $this->renderAjax('view_asset_modal', $params);
			} else {
				return $this->render('view_asset_modal', $params);
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
     * get user data to populate unassign confirmation modal
     */
    public function actionViewUnassignConfirmation()
    {
		try{
			// Verify logged in
			if (Yii::$app->user->isGuest) {
				return $this->redirect(['/login']);
			}

			if ($data = Yii::$app->request->post()){
				$json_data = json_encode($data);

				// post url
                $url = 'dispatch%2Fget-assigned-user';
                $response = Parent::executeDeleteRequest($url, $json_data); // indirect rbac
				$unassignConfirmationData = json_decode($response, true)['assignedUserMaps'];

				return $this->renderAjax('unassign_confirmation_modal', [
                    'unassignConfirmationData' => $unassignConfirmationData
                ]);
			} else {
				throw new \yii\web\BadRequestHttpException;
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