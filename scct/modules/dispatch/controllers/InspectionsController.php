<?php

namespace app\modules\dispatch\controllers;

use Exception;
use InspectionRequest;
use Yii;
use yii\data\ArrayDataProvider;
use yii\web\UnauthorizedHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\ServerErrorHttpException;
use yii\web\Response;
use yii\data\Pagination;
use app\constants\Constants;

class InspectionsController extends \app\controllers\BaseController
{
    public function actionIndex()
    {
        try {

            // Check if user has permission to view dispatch page
            self::requirePermission("viewInspections");

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
            $getInspectionDataResponse = json_decode(Parent::executeGetRequest($getUrl, Constants::API_VERSION_2), true); //indirect RBAC
            Yii::trace("INSPECTION INDEX DATA: ".json_encode($getInspectionDataResponse));

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

            // Sorting Unassign table
            $inspectionDataProvider->sort = [
                'attributes' => [
                    'MapGrid' => [
                        'asc' => ['MapGrid' => SORT_ASC],
                        'desc' => ['MapGrid' => SORT_DESC]
                    ],
                    'ComplianceStart' => [
                        'asc' => ['ComplianceStart' => SORT_ASC],
                        'desc' => ['ComplianceStart' => SORT_DESC]
                    ],
                    'ComplianceEnd' => [
                        'asc' => ['ComplianceEnd' => SORT_ASC],
                        'desc' => ['ComplianceEnd' => SORT_DESC]
                    ],
                    'TotalInspections' => [
                        'asc' => ['TotalInspections' => SORT_ASC],
                        'desc' => ['TotalInspections' => SORT_DESC]
                    ]
                ]
            ];

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

			$getSectionDataResponse = json_decode(Parent::executeGetRequest($getUrl, Constants::API_VERSION_2), true); //indirect RBAC
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
    public function actionViewInspection($searchFilterVal = null, $mapGridSelected = null, $sectionNumberSelected = null)
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
				$viewAssetPageSizeParams = 750;
				$pageAt = 1;
			}else{
				$viewAssetPageSizeParams = 750;
				$pageAt = 1;
			}

			// get the key to generate section table
			if (isset($_POST['expandRowKey']))
				$mapGridSelected = $_POST['expandRowKey'];
			else
				$mapGridSelected = "";

			$getUrl = 'inspection%2Fget-inspections&' . http_build_query([
					'mapGridSelected' => $mapGridSelected,
					'sectionNumberSelected' => "",
					'filter' => "",
					'listPerPage' => $viewAssetPageSizeParams,
					'page' => $pageAt,
				]);
			$getSectionDetailDataResponse = json_decode(Parent::executeGetRequest($getUrl, Constants::API_VERSION_2), true); //indirect RBAC
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
				return $this->renderAjax('_inspection-expand', [
					'sectionDetailDataProvider' => $sectionDetailDataProvider,
					'model' => $model,
				]);
			} else {
				return $this->render('_inspection-expand', [
					'sectionDetailDataProvider' => $sectionDetailDataProvider,
					'model' => $model,
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
     * render expandable event row
     * @return string|Response
     */
    public function actionViewEvent($inspectionID = null)
    {
		try{
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

			$getUrl = 'inspection%2Fget-inspections&' . http_build_query([
					'mapGridSelected' => "",
					'sectionNumberSelected' => "",
					'inspectionID' => $inspectionID,
					'filter' => $eventFilterParams,
					'listPserPage' => $eventPageSizeParams,
					'page' => $pageAt,
				]);
			Yii::trace("EVENT URL: ".$getUrl);
			$getEventDataResponse = json_decode(Parent::executeGetRequest($getUrl, Constants::API_VERSION_2), true); //indirect RBAC
			Yii::trace("EVENT DATA: ".json_encode($getEventDataResponse));
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
     * render Image
     * @param null $Photo1Path
     * @return string|\yii\web\Response
     */
    public function actionViewImage($Photo1Path = null){
        // Verify logged in
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['/login']);
        }

        Yii::trace("CALL VIEW IMAGE ACTION.");

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('viewImage', [
                'Photo1Path' => $Photo1Path
            ]);
        } else {
            return $this->render('viewImage', [
                'Photo1Path' => $Photo1Path
            ]);
        }
    }
}