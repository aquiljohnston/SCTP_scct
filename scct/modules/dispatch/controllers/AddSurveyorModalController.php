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

            if (Yii::$app->request->post()) {
                $workCenterFilterVal = "";
                $searchFilterVal = "";
                $data = Yii::$app->request->post();

                //todo: need to review if we need those filter, otherwise should be removed
                /*if (!empty($data["mapplat"])) {
                    foreach ($data["mapplat"] as $key) {
                        $MapPlatArr[] = $key;
                        Yii::Trace("MapPlatElement : " . $key);
                    }
                    foreach ($data["IRUID"] as $key) {
                        $IRUIDArr[] = $key;
                        Yii::Trace("IRUIDElement : " . $key);
                    }
                }

                if (!empty($data["workCenterFilterVal"])) {
                    $workCenterFilterVal = $data["workCenterFilterVal"];
                }*/
                if (!empty($data["searchFilterVal"])) {
                    $searchFilterVal = $data["searchFilterVal"];
                }

                //todo: need to be replaced with API route
                // Reading the response from the the api and filling the surveyorGridView
                $getUrl = 'dispatch%2Fget-surveyors&' . http_build_query([
                        'filter' => $searchFilterVal,
                    ]);
                //$surveyorUrl = 'pge%2Fdispatch%2Fget-surveyors&filter=' . $searchFilterVal . '&workCenter=' . $workCenterFilterVal;
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
            } else {
                throw new \yii\web\BadRequestHttpException;
            }
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