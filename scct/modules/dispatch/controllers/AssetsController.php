<?php
/**
 * Created by PhpStorm.
 * User: jpatton
 * Date: 2/21/2017
 * Time: 1:18 PM
 */

namespace app\modules\dispatch\controllers;
use Yii;
use yii\data\ArrayDataProvider;
use app\constants\Constants;

class AssetsController extends \app\controllers\BaseController {
    public function actionIndex($id)
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['/login']);
        }

        $getUrl = "assets%2Fget&id=$id";
        $data = json_decode(Parent::executeGetRequest($getUrl, Constants::API_VERSION_2), true); //indirect RBAC
        $data = $data["assets"];

        $assetsDataProvider = new ArrayDataProvider([
            'allModels' => $data,
            'pagination' => false
        ]);

        return $this->render('assets', [
            'id' => $id,
            'assetsDataProvider' => $assetsDataProvider
        ]);
    }
}
