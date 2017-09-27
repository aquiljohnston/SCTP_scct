<?php

namespace app\controllers;

use Yii;
use yii\data\Pagination;
use yii\data\ArrayDataProvider;
use yii\web\ForbiddenHttpException;
use yii\web\Response;
use app\models\TrackerHistoryMapFilters;
use app\constants\Constants;

/**
 * Tracker implements the CRUD actions for Tracker model.
 */
class TrackerController extends BaseController
{
    /**
     * Lists all Tracker models.
     * @return mixed
     */
    public function actionIndex()
    {
        //guest redirect
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['/login']);
        }
		
		//Check if user has permission to tracker page
		self::requirePermission("viewTrackerMenu");
		
        $url = "dropdown%2Fget-tracker-map-grids";
        $mapGridsResponse = Parent::executeGetRequest($url, Constants::API_VERSION_2); // indirect rbac
        $mapGridsResponse = json_decode($mapGridsResponse, true);
        $mapGridsResponse =  ['select' => 'Select a map...'] + $mapGridsResponse;
        return $this->render("landing", [
            "dropdown" => $mapGridsResponse
        ]);
    }

    /**
     * render Google Map view based on selected MapGrid
     * @param null $mapgrid
     * @return string|\yii\web\Response
     */
    public function actionViewMap($mapgrid = null){
        // Verify logged in
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['/login']);
        }

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('mapview', [
            ]);
        } else {
            return $this->render('mapview', [
            ]);
        }
    }

    public function actionGetMapData($mapGrid = null){
        if ($mapGrid != null){
            $getUrl = 'map%2Fget&' . http_build_query([
                    'mapgrid' => $mapGrid
                ]);
            $getMapDataResponse = Parent::executeGetRequest($getUrl, Constants::API_VERSION_2); //indirect RBAC
            Yii::trace("ASSETS DATA: ".json_encode($getMapDataResponse));
            echo $getMapDataResponse;
        }else{
            echo null;
        }
    }
}
