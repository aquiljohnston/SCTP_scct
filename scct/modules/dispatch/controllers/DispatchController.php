<?php

namespace app\modules\dispatch\controllers;

use InspectionRequest;
use Yii;
use yii\data\ArrayDataProvider;

/**
 * Created by PhpStorm.
 * User: jpatton
 * Date: 1/31/2017
 * Time: 2:22 PM
 */

class DispatchController extends \app\controllers\BaseController {
    public function actionIndex() {
        // Verify logged in
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['login/login']);
        }
        /*$filterModel = new InspectionRequest();
        // Retrieve data
        if ($filterModel->load(Yii::$app->request->post())) {
            // We have filter variables
        } else {

        }*/
        $getUrl = 'dispatch%2Fget&division=&filter=';
        $data = json_decode(Parent::executeGetRequest($getUrl, self::API_VERSION_2), true); //indirect RBAC
        $data = $data["Maps"];
        // Filter data
        //todo
        $filterData = $data;

        //Surveyors
        $getSurveyorsUrl = 'dispatch%2Fget-surveyors';
        $surveyorsData = json_decode(Parent::executeGetRequest($getSurveyorsUrl, self::API_VERSION_2), true);
        $surveyorsData = $surveyorsData['users'];
        // Put data in data provider
        // render page
        $dispatchDataProvider = new ArrayDataProvider
        ([
            'allModels' => $filterData,
            'pagination' => false,
        ]);
        $surveyorsDataProvider = new ArrayDataProvider([
            'allModels' => $surveyorsData,
            'pagination' => false
        ]);
        return $this->render('index', [
            'dispatchDataProvider' => $dispatchDataProvider,
            'surveyorsDataProvider' => $surveyorsDataProvider

        ]);

    }
}