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
        $getSurveyorUrl = 'dispatch%2Fget-surveyors&' . http_build_query([
                'filter' => '',
            ]);
        $getSurveyorsResponse = json_decode(Parent::executeGetRequest($getSurveyorUrl, Constants::API_VERSION_2), true); // indirect rbac

        $getUrl = "assets%2Fget&id=$id";
        $data = json_decode(Parent::executeGetRequest($getUrl, Constants::API_VERSION_2), true); //indirect RBAC
        $data = self::reGenerateAssetsData($data['assets'], $getSurveyorsResponse['users']);//$data["assets"];
        Yii::trace("reGenerateAssetsData " . json_encode($data));

        $assetsDataProvider = new ArrayDataProvider([
            'allModels' => $data,
            'pagination' => false
        ]);

        return $this->render('assets', [
            'id' => $id,
            'assetsDataProvider' => $assetsDataProvider
        ]);
    }

    private function reGenerateAssetsData($assetsData, $surveyorList){
        foreach ($assetsData as $item){
            $item['userList'] = $surveyorList;
        }
        return $assetsData;
    }
}
