<?php

namespace app\controllers;

use Yii;
use yii\data\Pagination;
use yii\data\ArrayDataProvider;
use yii\web\ForbiddenHttpException;
use yii\web\Response;
use app\models\TrackerHistoryMapFilters;

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


        return $this->render('index', []);
    }


    public function actionHistory()
    {
        //guest redirect
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['/login']);
        }

        $url = "dropdown%2Fget-tracker-map-grids";
        $mapGridsResponse = Parent::executeGetRequest($url, self::API_VERSION_2); // indirect rbac
        $mapGridsResponse = json_decode($mapGridsResponse, true);

        return $this->render("landing", [
            "dropdown" => $mapGridsResponse
        ]);
    }
}
