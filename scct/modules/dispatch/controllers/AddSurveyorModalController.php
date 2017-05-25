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
                Yii::trace("ADD SURVEYOR MODAL CALLED");
                $MapPlatArr = [];
                $IRUIDArr = [];
                $workCenterFilterVal = "";
                $searchFilterVal = "";

                $data = Yii::$app->request->post();

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
                }
                if (!empty($data["searchFilterVal"])) {
                    $searchFilterVal = $data["searchFilterVal"];
                }*/

                //todo: need to be replaced with API route
                // Reading the response from the the api and filling the surveyorGridView
                /*$surveyorUrl = 'pge%2Fdispatch%2Fget-surveyors&filter=' . $searchFilterVal . '&workCenter=' . $workCenterFilterVal;
                Yii::trace("surveyors " . $surveyorUrl);
                $surveyorsResponse = Parent::executeGetRequest($surveyorUrl); // indirect rbac

                Yii::trace("Surveyors response " . $surveyorsResponse);
                $surveyorsResponse = json_decode($surveyorsResponse, true);

                // get surveyorWorkCenter from the api and filling surveyorWorkCenter dropdown
                $surveyorWorkCenterUrl = 'pge%2Fdropdown%2Fget-user-work-center-dropdown';
                $surveyorWorkCenterResponse = Parent::executeGetRequest($surveyorWorkCenterUrl); // indirect rbac
                $surveyorWorkCenterList = json_decode($surveyorWorkCenterResponse, true);*/

                //todo: delete hard code value
                $surveyorsResponse['users'] = [];
                $surveyorWorkCenterList = [];

                $dataProvider = new ArrayDataProvider
                ([
                    'allModels' => $surveyorsResponse['users'],
                    'pagination' => false,
                ]);

                //if (!empty($surveyorsResponse['UserUID'])) {
                $dataProvider->key = 'UserUID';
                //}

                return $this->render('add_surveyor_modal', [
                    'addSurveyorsDataProvider' => $dataProvider,
                    'surveyorWorkCenterList' => $surveyorWorkCenterList,
                    /*'MapPlat' => $MapPlatArr,
                    'IRUID' => $IRUIDArr,*/
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