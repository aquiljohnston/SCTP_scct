<?php
/**
 * Created by PhpStorm.
 * User: tzhang
 * Date: 9/28/2017
 * Time: 3:41 PM
 */

namespace app\modules\dispatch\controllers;

use Yii;
use yii\data\ArrayDataProvider;
use yii\data\Pagination;
use app\constants\Constants;

class CgeController extends \app\controllers\BaseController
{
    /**
     * render index view
     * @return string|Response
     */
    public function actionIndex()
    {
        try {
            //Check if user has permission to view cge page
            //self::requirePermission("viewCGE");

            // Verify logged in
            if (Yii::$app->user->isGuest) {
                return $this->redirect(['/login']);
            }

            $model = new \yii\base\DynamicModel([
                'cgefilter', 'pagesize'
            ]);
            $model->addRule('cgefilter', 'string', ['max' => 32])
                ->addRule('pagesize', 'string', ['max' => 32]);

            //check request
            if ($model->load(Yii::$app->request->queryParams)) {

                Yii::trace("cgefilter " . $model->cgefilter);
                Yii::trace("pagesize " . $model->pagesize);
                $cgePageSizeParams = $model->pagesize;
                $cgeFilterParams = $model->cgefilter;
            } else {
                $cgePageSizeParams = 50;
                $cgeFilterParams = "";
            }

            // get the page number for cge table
            if (isset($_GET['cgePageNumber']) && $_GET['cgeTableRecordsUpdate'] != "true") {
                $pageAt = $_GET['cgePageNumber'];
            } else {
                $pageAt = 1;
            }

            $getUrl = 'cge%2Fget-map-grids&' . http_build_query([
                    'filter' => $cgeFilterParams,
                    'listPerPage' => $cgePageSizeParams,
                    'page' => $pageAt
                ]);
            Yii::trace("GET CGE URL: ".$getUrl);
            $getCGEDataResponse = json_decode(Parent::executeGetRequest($getUrl, Constants::API_VERSION_2), true); //indirect RBAC
            Yii::trace("cge DATA: " . json_encode($getCGEDataResponse));
            $cgeData = $getCGEDataResponse['mapGrids'];

            //set paging on cge table
            $pages = new Pagination($getCGEDataResponse['pages']);

            $cgeDataProvider = new ArrayDataProvider
            ([
                'allModels' => $cgeData,
                'pagination' => false,
            ]);

            $cgeDataProvider->key = 'MapGrid';

            if (Yii::$app->request->isAjax) {
                return $this->render('index', [
                    'cgeDataProvider' => $cgeDataProvider,
                    'model' => $model,
                    'pages' => $pages,
                    'cgePageSizeParams' => $cgePageSizeParams,
                    'cgeFilterParams' => $cgeFilterParams,
                ]);
            } else {
                return $this->render('index', [
                    'cgeDataProvider' => $cgeDataProvider,
                    'model' => $model,
                    'pages' => $pages,
                    'cgePageSizeParams' => $cgePageSizeParams,
                    'cgeFilterParams' => $cgeFilterParams,
                ]);
            }
        } catch (ForbiddenHttpException $e) {
            Yii::$app->runAction('login/user-logout');
            //throw new ForbiddenHttpException('You do not have adequate permissions to perform this action.');
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
        // Verify logged in
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['/login']);
        }

        // get the key to generate section table
        if (isset($_POST['expandRowKey']))
            $mapGridSelected = $_POST['expandRowKey'];
        else
            $mapGridSelected = "";
        //todo: get-history&workOrderID=x
        $getUrl = 'cge%2Fget-by-map&' . http_build_query([
                'mapGrid' => $mapGridSelected,
                /*'filter' => "",
                'listPerPage' => 100,
                'page' => 1*/
            ]);
        $getSectionDataResponseResponse = json_decode(Parent::executeGetRequest($getUrl, Constants::API_VERSION_2), true); //indirect RBAC
        Yii::trace("GET SECTION: ".json_encode($getSectionDataResponseResponse));
        $sectionData = $getSectionDataResponseResponse['cges'];

        //set paging on assigned table
        //$pages = new Pagination($getSectionDataResponseResponse['pages']);

        $sectionDataProvider = new ArrayDataProvider
        ([
            'allModels' => $sectionData,
            'pagination' => false,
        ]);

        $sectionDataProvider->key = 'ID';

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('_section-expand', [
                'sectionDataProvider' => $sectionDataProvider,
                //'pages' => $pages,
            ]);
        } else {
            return $this->render('_section-expand', [
                'sectionDataProvider' => $sectionDataProvider,
                //'pages' => $pages,
            ]);
        }
    }

    /**
     * render asset modal
     * @return string|Response
     */
    public function actionViewHistory($workOrderID = null)
    {
        // Verify logged in
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['/login']);
        }

        $getUrl = 'cge%2Fget-history&' . http_build_query([
                'workOrderID' => $workOrderID
            ]);
        $getHistoryDataResponse = json_decode(Parent::executeGetRequest($getUrl, Constants::API_VERSION_2), true); //indirect RBAC

        Yii::trace("reGenerateAssetsData " . json_encode($getHistoryDataResponse));

        // Put data in data provider
        $historyDataProvider = new ArrayDataProvider
        ([
            'allModels' => $getHistoryDataResponse['cgeHistory'],
            'pagination' => false,
        ]);

		if (Yii::$app->request->isAjax) {
            return $this->renderAjax('view_history_modal', [
                'historyDataProvider' => $historyDataProvider
            ]);
        } else {
			return $this->render('view_history_modal', [
				'historyDataProvider' => $historyDataProvider
			]);
		}
    }

    /**
     * CGE Dispatch function
     * @throws ForbiddenHttpException
     */
    public function actionDispatch(){
        try {
            if (Yii::$app->request->isAjax) {
                $data = Yii::$app->request->post();
                $json_data = json_encode($data);
                Yii::trace("CGE DISPATCH DATA: " . $json_data);

                // post url
                /*$postUrl = 'cge%2Fdispatch';
                $putResponse = Parent::executePostRequest($postUrl, $json_data, Constants::API_VERSION_2); // indirect rbac
                Yii::trace("cge dispatchputResponse " . $putResponse);*/

            }
        } catch (ForbiddenHttpException $e) {
            throw new ForbiddenHttpException;
        } catch (Exception $e) {
            Yii::$app->runAction('login/user-logout');
        }
    }
}