<?php

namespace app\modules\dispatch\controllers;

use Exception;
use InspectionRequest;
use Yii;
use yii\data\ArrayDataProvider;
use yii\web\ForbiddenHttpException;
use yii\web\Response;
use yii\data\Pagination;

/**
 * Created by PhpStorm.
 * User: jpatton
 * Date: 1/31/2017
 * Time: 2:22 PM
 */
class DispatchController extends \app\controllers\BaseController
{
    public function actionIndex()
    {
        try {
            $model = new \yii\base\DynamicModel([
                'division', 'workcenter',
            ]);
            $model->addRule('division', 'string', ['max' => 32])
                ->addRule('workcenter', 'string', ['max' => 32]);

            // Verify logged in
            if (Yii::$app->user->isGuest) {
                return $this->redirect(['/login']);
            }

            // check if division, mapplat, workcenter, surveytype, compliancemonth is posted
            //if (Yii::$app->request->isAjax){
            if ($model->load(Yii::$app->request->post())) {
                //todo: set posted value to model
            }else{
                //todo: set default value to model
            }
            $getUrl = 'dispatch%2Fget&filter=YORK&listPerPage=10&page=1';
            $getDispatchDataResponse = json_decode(Parent::executeGetRequest($getUrl, self::API_VERSION_2), true); //indirect RBAC
            Yii::trace("DISPATCH DATA: ".json_encode($getDispatchDataResponse));
            $dispatchData = $getDispatchDataResponse['assets'];

            // Put data in data provider
            // render page
            $dispatchDataProvider = new ArrayDataProvider
            ([
                'allModels' => $dispatchData,
                'pagination' => false,
            ]);

            //todo: set paging on both tables
            // set pages to dispatch table
            $pages = new Pagination($getDispatchDataResponse['pages']);
            $divisionList = [];
            $workCenterList = [];

            //todo: check permission to dispatch work
            $can = 1;

            if (Yii::$app->request->isAjax) {
                return $this->render('index', [
                    'dispatchDataProvider' => $dispatchDataProvider,
                    'model' => $model,
                    'can' => $can,
                    'pages' => $pages,
                    'divisionList' => $divisionList,
                    'workCenterList' => $workCenterList,
                ]);
            } else {
                return $this->render('index', [
                    'dispatchDataProvider' => $dispatchDataProvider,
                    'model' => $model,
                    'can' => $can,
                    'pages' => $pages,
                    'divisionList' => $divisionList,
                    'workCenterList' => $workCenterList,
                ]);
            }
        } catch (ForbiddenHttpException $e) {
            throw new ForbiddenHttpException('You do not have adequate permissions to perform this action.');
        } catch (Exception $e) {
            //Yii::$app->runAction('login/user-logout');
        }
    }

    public function actionPost()
    {
        $data = [
            'status' => 'Not implemented'
        ];
        $response = Yii::$app->response;
        $response->format = Response::FORMAT_JSON;
        $response->data = $data;
        return $response;
    }

    // get workCenter dependent dropdown list
    public function actionGetworkcenter()
    {

        if (isset($_POST['depdrop_parents'])) {
            $ids = $_POST['depdrop_parents'];
            $division_id = empty($ids[0]) ? null : $ids[0];
            if ($division_id != null) {
                // get workCenter from the api and filling workCenter dropdown
                $url = 'pge%2Fdropdown%2Fget-dispatch-work-center-dropdown&division=' . urlencode($division_id);
                $workCenterListResponse = Parent::executeGetRequest($url); // indirect rbac
                $workCenterList = json_decode($workCenterListResponse, true);

                echo json_encode(['output' => $workCenterList, 'selected' => '']);
                return;
            }
        }
        echo json_encode(['output' => '', 'selected' => '']);
    }

    /**
     * Dispatch function
     * @throws ForbiddenHttpException
     */
    public function actionDispatch()
    {
        try {
            if (Yii::$app->request->isAjax) {
                $data = Yii::$app->request->post();
                // Indicate that Ajax request sent from dispatch page
                if (!empty($data["InspectionRequestUID"])) {
                    Yii::trace("dispatchdata " . json_encode($data["InspectionRequestUID"]));
                    foreach ($data["InspectionRequestUID"] as $key) {
                        //foreach($key as $keyitem){

                        $InspectionRequestUIDArr[] = $key;
                        Yii::Trace("InspectionRequestUIDis ; " . $key);
                        // }
                    }

                    foreach ($data["UserUID"] as $key) {
                        //foreach($key as $keyitem){

                        $UserUID[] = $key;
                        Yii::Trace("UserUIDis ; " . $key);
                        // }
                    }

                    foreach ($data["InspectionRequestUID"] as $IR) {
                        foreach ($data["UserUID"] as $User) {
                            $individualItem = [];
                            $individualItem["IR"] = $IR;
                            $individualItem["User"] = $User;
                            $dispatchArray[] = $individualItem;

                        }
                    }
                } else {
                    // Indicate that Ajax call sent from Add Surveyor Modal view
                    foreach ($data["IRUID"] as $key) {
                        $InspectionRequestUIDArr[] = $key;
                        Yii::Trace("IRUID ; " . $key);
                    }

                    foreach ($data["UserUID"] as $key) {
                        $UserUID[] = $key;
                        Yii::Trace("UserUIDis ; " . $key);
                    }

                    foreach ($data["IRUID"] as $IR) {
                        foreach ($data["UserUID"] as $User) {
                            $individualItem = [];
                            $individualItem["IR"] = $IR;
                            $individualItem["User"] = $User;
                            $dispatchArray[] = $individualItem;
                        }
                    }
                }

                $data = array(
                    'SourceID' => 'WEB',
                    'Assignments' => $dispatchArray,
                );
                $json_data = json_encode($data);

                // post url
                // TODO UPDATE PUT URL TO DISPATCH WORK
                $putUrl = 'pge%2Fdispatch%2Fdispatch';
                $putResponse = Parent::executePostRequest($putUrl, $json_data); // indirect rbac
                Yii::trace("dispatchputResponse " . $putResponse);

            }
        } catch (ForbiddenHttpException $e) {
            throw new ForbiddenHttpException;
        } catch (Exception $e) {
            Yii::$app->runAction('login/user-logout');
        }
    }

    /**
     * CheckExistingWorkCenter function
     * @param $divisionDefaultVal
     * @param $workCenterDefaultVal
     * @param $ErrorMsg
     * @return array
     */
    public function CheckExistingDivision($divisionDefaultVal = null, $workCenterDefaultVal = null, $ErrorMsg = null)
    {

        $divisionDefaultSelectedUrl = 'pge%2Fdropdown%2Fget-default-filter&screen=dispatch';
        $divisionDefaultSelectedResponse = Parent::executeGetRequest($divisionDefaultSelectedUrl); // indirect rbac
        $divisionDefaultSelection = json_decode($divisionDefaultSelectedResponse, true);

        // check if error key exists in the response
        if (array_key_exists("Error", $divisionDefaultSelection)) {
            $ErrorMsg = $divisionDefaultSelection['Error'];
        } else {
            $divisionDefaultVal = $divisionDefaultSelection[0]['Division'];
            $workCenterDefaultVal = $divisionDefaultSelection[0]['WorkCenter'];
        }
        return array($ErrorMsg, $divisionDefaultVal, $workCenterDefaultVal);
    }
}