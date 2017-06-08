<?php

namespace app\modules\dispatch\controllers;

use Yii;
use yii\data\ArrayDataProvider;

class AddSurveyorModalController extends \app\controllers\BaseController {

    /**
     * Modal view for assigning work to surveyors
     * @return string|\yii\web\Response
     * @throws ForbiddenHttpException
     */
    public function actionAddSurveyorModal()
    {
        try {
            if (Yii::$app->user->isGuest) {
                return $this->redirect(['/login']);
            }

            $model = new \yii\base\DynamicModel([
                'surveyorWorkcenter', 'modalSearch'
            ]);
            $model->addRule('surveyorWorkcenter', 'string', ['max' => 32])
                ->addRule('modalSearch', 'string', ['max' => 32]);

                $workCenterFilterVal = "";
                $searchFilterVal = "";
            if (Yii::$app->request->post()){
                $data = Yii::$app->request->post();

                if (!empty($data["searchFilterVal"])) {
                    $searchFilterVal = $data["searchFilterVal"];
                }
            }

            //todo: need to be replaced with API route
            // Reading the response from the the api and filling the surveyorGridView
            $getUrl = 'dispatch%2Fget-surveyors&' . http_build_query([
                    'filter' => $searchFilterVal,
                ]);
            Yii::trace("surveyors " . $getUrl);
            $surveyorsResponse = json_decode(Parent::executeGetRequest($getUrl, self::API_VERSION_2), true); // indirect rbac
            Yii::trace("Surveyors response " . json_encode($surveyorsResponse));

            //todo: delete hard code value
            $surveyorWorkCenterList = [];

            $dataProvider = new ArrayDataProvider
            ([
                'allModels' => $surveyorsResponse['users'],
                'pagination' => false,
            ]);

            $dataProvider->key = 'UserID';

            return $this->render('add_surveyor_modal', [
                'addSurveyorsDataProvider' => $dataProvider,
                'surveyorWorkCenterList' => $surveyorWorkCenterList,
                'model' => $model,
                'searchFilterVal' => $searchFilterVal,
                'workCenterFilterVal' => $workCenterFilterVal,
            ]);
        }catch(ForbiddenHttpException $e)
        {
            throw new ForbiddenHttpException;
        }
        catch(\Exception $e)
        {
            Yii::$app->runAction('login/user-logout');
        }
    }
}