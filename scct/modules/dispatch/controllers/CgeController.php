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
}