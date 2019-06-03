<?php

namespace app\modules\dispatch\controllers;

use Exception;
use InspectionRequest;
use Yii;
use yii\data\ArrayDataProvider;
use yii\web\Response;
use yii\data\Pagination;
use yii\web\ForbiddenHttpException;
use yii\web\ServerErrorHttpException;
use yii\web\UnauthorizedHttpException;
use yii\web\View;
use app\constants\Constants;

class DispatchController extends \app\controllers\BaseController
{
    public function actionIndex() 
	{
       try {
            // Check if user has permission to view dispatch page
            self::requirePermission("viewDispatch");

            $model = new \yii\base\DynamicModel([
                'dispatchFilter',
				'pageSize',
				'dateRangePicker'
            ]);
            $model->addRule('dispatchFilter', 'string', ['max' => 32])
                ->addRule('pageSize', 'string', ['max' => 32])
                ->addRule('dateRangePicker', 'string', ['max' => 32]);

            // Verify logged in
            if (Yii::$app->user->isGuest) {
                return $this->redirect(['/login']);
            }
			
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
            if (!$model->load(Yii::$app->request->queryParams)) {
                $model->pageSize = 50;
                $model->dispatchFilter = '';
                $model->dateRangePicker = '';
            }
			
            // get the page number for assigned table
            if (isset($_GET['dispatchPageNumber'])) {
                $pageAt = $_GET['dispatchPageNumber'];
            } else {
                $pageAt = 1;
            }

            $getUrl = 'dispatch%2Fget-available&' . http_build_query([
					'dateRange' =>  $model->dateRangePicker,
                    'filter' => $model->dispatchFilter,
                    'listPerPage' => $model->pageSize,
                    'page' => $pageAt,
					'sortField' => $sortField,
					'sortOrder' => $sortOrder,
                ]);
            $getDispatchDataResponse = json_decode(Parent::executeGetRequest($getUrl, Constants::API_VERSION_2), true); //indirect RBAC

            $dispatchData = $getDispatchDataResponse['mapGrids'];
			$divisionFlag = $getDispatchDataResponse['divisionFlag'];

            // Put data in data provider
            // render page
            $dispatchDataProvider = new ArrayDataProvider
            ([
                'allModels' => $dispatchData,
                'pagination' => false,
				'key' => function ($dispatchData) {
					return array(
						'MapGrid' => $dispatchData['MapGrid'],
						'InspectionType' => $dispatchData['InspectionType'],
						'BillingCode' => $dispatchData['BillingCode'],
						'OfficeName' => $dispatchData['OfficeName'],
					);
				},
            ]);

            // set pages to dispatch table
            $pages = new Pagination($getDispatchDataResponse['pages']);

            //todo: check permission to dispatch work
            $can = 1;

            // Sorting Dispatch table
            $dispatchDataProvider->sort = [
				'defaultOrder' => [$sortField => ($sortOrder == 'ASC') ? SORT_ASC : SORT_DESC],
                'attributes' => [
                    'MapGrid',
                    'Division',
                    'ComplianceStart',
                    'ComplianceEnd',
                    'AvailableWorkOrderCount',
                    'InspectionType',
                    'BillingCode',
                    'OfficeName'
                ]
            ];
			
			$dataArray = [
				'dispatchDataProvider' => $dispatchDataProvider,
				'divisionFlag' => $divisionFlag,
				'model' => $model,
				'can' => $can,
				'pages' => $pages,
			];

            if (Yii::$app->request->isAjax) {
                //Prevent CSS from being loaded twice, causing a visual bug
                Yii::$app->assetManager->bundles = [
                    'yii\bootstrap\BootstrapPluginAsset' => false,
                    'yii\bootstrap\BootstrapAsset' => false,
                    'yii\web\JqueryAsset' => false,
                ];
                return $this->renderAjax('index', $dataArray);
            } else {
                return $this->render('index', $dataArray);
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
			$model = new \yii\base\DynamicModel([
				'sectionfilter', 'pagesize'
			]);
			$model->addRule('sectionfilter', 'string', ['max' => 32]);

			// Verify logged in
			if (Yii::$app->user->isGuest) {
				return $this->redirect(['/login']);
			}

			//check request
			if ($model->load(Yii::$app->request->queryParams)) {
				$sectionFilterParams = $model->sectionfilter;
			} else {
				$sectionFilterParams = "";
			}

			// get the key to generate section table
			if (isset($_POST['expandRowKey']))
			{
				$mapGridSelected = $_POST['expandRowKey']['MapGrid'];
				$inspectionType = $_POST['expandRowKey']['InspectionType'];
				$billingCode = $_POST['expandRowKey']['BillingCode'];
				$officeName = $_POST['expandRowKey']['OfficeName'];
			}else{
				$mapGridSelected = '';
				$inspectionType = '';
				$billingCode = '';
				$officeName = '';
			}     

			$getUrl = 'dispatch%2Fget-available&' . http_build_query([
					'mapGridSelected' => $mapGridSelected,
					'inspectionType' => $inspectionType,
					'billingCode' => $billingCode,
					'officeName' => $officeName,
					'filter' => $sectionFilterParams
				]);

			$getSectionDataResponse = json_decode(Parent::executeGetRequest($getUrl, Constants::API_VERSION_2), true); //indirect RBAC
			$sectionData = $getSectionDataResponse['sections'];

			// Put data in data provider
			// dispatch section data provider
			$sectionDataProvider = new ArrayDataProvider
			([
				'allModels' => $sectionData,
				'pagination' => false,
				'key' => function ($sectionData) {
					return array(
						'SectionNumber' => $sectionData['SectionNumber'],
						'InspectionType' => $sectionData['InspectionType'],
						'BillingCode' => $sectionData['BillingCode'],
						'OfficeName' => $sectionData['OfficeName'],
					);
				},
			]);

			//todo: check permission to dispatch work
			$can = 1;

			if (Yii::$app->request->isAjax) {
				return $this->renderAjax('_section-expand', [
					'sectionDataProvider' => $sectionDataProvider,
					'model' => $model,
					'can' => $can,
					'sectionFilterParams' => $sectionFilterParams,
				]);
			} else {
				return $this->render('_section-expand', [
					'sectionDataProvider' => $sectionDataProvider,
					'model' => $model,
					'can' => $can,
					'sectionFilterParams' => $sectionFilterParams,
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
    public function actionViewAsset($searchFilterVal = null, $mapGridSelected = null, $sectionNumberSelected = null, 
		$recordsPerPageSelected = 200, $inspectionType = null, $billingCode = null, $officeName = null)
    {
		try{
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
				$viewAssetFilterParams = $searchFilterVal;
				$mapGridSelectedParam = $mapGridSelected;
				$sectionNumberSelectedParam = $sectionNumberSelected;
				$viewAssetPageSizeParams = $recordsPerPageSelected;
				$inspectionType = $inspectionType; 
				$billingCode = $billingCode;
				$officeName = $officeName;
				$pageAt = Yii::$app->getRequest()->getQueryParam('viewDispatchAssetPageNumber');
			} else {
				$viewAssetFilterParams = "";
				$pageAt = 1;
			}

			$getSurveyorUrl = 'dispatch%2Fget-surveyors&' . http_build_query([
					'filter' => '',
				]);

			$getSurveyorsResponse = json_decode(Parent::executeGetRequest($getSurveyorUrl, Constants::API_VERSION_2), true); // indirect rbac

			$getUrl = 'dispatch%2Fget-available-assets&' . http_build_query([
					'mapGridSelected' => $mapGridSelectedParam,
					'sectionNumberSelected' => $sectionNumberSelectedParam,
					'filter' => $viewAssetFilterParams,
					'listPerPage' => $viewAssetPageSizeParams,
					'page' => $pageAt,
					'inspectionType' => $inspectionType,
					'billingCode' => $billingCode,
					'officeName' => $officeName,
				]);

			$getAssetDataResponse = json_decode(Parent::executeGetRequest($getUrl, Constants::API_VERSION_2), true); //indirect RBAC
			
			$data = self::reGenerateAssetsData($getAssetDataResponse['assets'], $getSurveyorsResponse['users']);

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
					'viewAssetPageSizeParams' => $viewAssetPageSizeParams,
					'inspectionType' => $inspectionType,
					'billingCode' => $billingCode,
					'officeName' => $officeName

				]);
			} else {
				return $this->render('view_asset_modal', [
					'assetDataProvider' => $assetDataProvider,
					'model' => $model,
					'pages' => $pages,
					'searchFilterVal' => $viewAssetFilterParams,
					'mapGridSelected' => $mapGridSelectedParam,
					'sectionNumberSelected' => $sectionNumberSelectedParam,
					'viewAssetPageSizeParams' => $viewAssetPageSizeParams,
					'inspectionType' => $inspectionType,
					'billingCode' => $billingCode,
					'officeName' => $officeName
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
     * Dispatch function
     * @throws ForbiddenHttpException
     */
    public function actionDispatch(){
        try {
            if (Yii::$app->request->isAjax) {
                $data = Yii::$app->request->post();
                $json_data = json_encode($data);
                // post url
                $postUrl = 'dispatch%2Fdispatch';
                $postResponse = Parent::executePostRequest($postUrl, $json_data, Constants::API_VERSION_2); // indirect rbac
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