<?php

namespace app\modules\dispatch\controllers;

use Yii;
use yii\data\ArrayDataProvider;

class AssignedController extends \app\controllers\BaseController {
    public function actionIndex() {
        // Verify logged in
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['login/login']);
        }
        $getUrl = 'assigned%2Fget';
        $data = json_decode(Parent::executeGetRequest($getUrl, self::API_VERSION_2), true); //indirect RBAC
        $data = $data["Maps"];

        //TODO: Filter
        $filterData = $data;

        $assignedDataProvider = new ArrayDataProvider
        ([
            'allModels' => $filterData,
            'pagination' => false,
        ]);
        return $this->render('index', [
            'assignedDataProvider' => $assignedDataProvider
        ]);

    }
}