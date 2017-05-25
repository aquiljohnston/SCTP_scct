<?php

namespace app\modules\dispatch\controllers;

use Yii;
use yii\data\ArrayDataProvider;

class AssignedController extends \app\controllers\BaseController {
    public function actionIndex() {
        // Verify logged in
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['/login']);
        }

        $model = new \yii\base\DynamicModel([
            'division', 'workcenter', 'pagesize'
        ]);
        $model->addRule('division', 'string', ['max' => 32])
            ->addRule('workcenter', 'string', ['max' => 32])
            ->addRule('pagesize', 'string', ['max' => 32]);

        $getUrl = 'assigned%2Fget';
        $data = json_decode(Parent::executeGetRequest($getUrl, self::API_VERSION_2), true); //indirect RBAC
        $data = $data["Maps"];

        // get divisionList from the api and filling division dropdown
        $divisionUrl = 'pge%2Fdropdown%2Fget-assigned-division-dropdown';
        $divisionListResponse = Parent::executeGetRequest($divisionUrl); // indirect rbac
        $divisionList = json_decode($divisionListResponse, true);

        //TODO: Filter
        $filterData = $data;

        //todo: check permission to un-assign work
        $canUnassign = 1;
        $canAddSurveyor = 1;

        //todo: set default value or callback value
        $divisionParams = "";
        $workCenterParams = "";
        $assignedPageSizeParams = 10;

        $assignedDataProvider = new ArrayDataProvider
        ([
            'allModels' => $filterData,
            'pagination' => false,
        ]);

        if (Yii::$app->request->isAjax) {
            return $this->render('index', [
                'assignedDataProvider' => $assignedDataProvider,
                'model' => $model,
                'divisionList' => $divisionList,
                'canUnassign' => $canUnassign,
                'canAddSurveyor' => $canAddSurveyor,
                'divisionParams' => $divisionParams,
                'workCenterParams' => $workCenterParams,
                'assignedPageSizeParams' => $assignedPageSizeParams
            ]);
        } else {
            return $this->render('index', [
                'assignedDataProvider' => $assignedDataProvider,
                'model' => $model,
                'divisionList' => $divisionList,
                'canUnassign' => $canUnassign,
                'canAddSurveyor' => $canAddSurveyor,
                'divisionParams' => $divisionParams,
                'workCenterParams' => $workCenterParams,
                'assignedPageSizeParams' => $assignedPageSizeParams
            ]);
        }
    }

    // get workCenter dependent dropdown list
    public function actionGetworkcenter()
    {
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $ids = $_POST['depdrop_parents'];
            $division_id = empty($ids[0]) ? null : $ids[0];
            if ($division_id != null) {
                // get workCenter from the api and filling workCenter dropdown
                $url = 'pge%2Fdropdown%2Fget-assigned-work-center-dropdown&division=' . urlencode($division_id);
                $workCenterListResponse = Parent::executeGetRequest($url); // indirect rbac
                Yii::trace("workcenterassign " . $workCenterListResponse);
                $workCenterList = json_decode($workCenterListResponse, true);

                echo json_encode(['output' => $workCenterList, 'selected' => '']);
                return;
            }
        }
        echo json_encode(['output' => '', 'selected' => '']);
    }

    /**
     * Unassign work function
     * @return \yii\web\Response
     * @throws ForbiddenHttpException
     */
    public function actionUnassign()
    {
        try {
            if (Yii::$app->request->isAjax) {
                Yii::trace("call Unassign");
                //try{
                $data = Yii::$app->request->post();
                // loop the data array to get all id's.
                //Yii::trace("unassignWorkQueue ".json_encode($data["AssignedWorkQueueUID"]));
                foreach ($data["AssignedWorkQueueUID"] as $key) {

                    Yii::Trace("unAssignedWorkQueueUIDis ; " . $key);
                    $unassignArr[] = $key;
                }

                $data = array(
                    'Unassign' => $unassignArr,
                );
                $json_data = json_encode($data);

                // post url
                $deleteUrl = 'pge%2Fdispatch%2Funassign';
                $deleteResponse = Parent::executePutRequest($deleteUrl, $json_data); // indirect rbac
                Yii::trace("unassignputResponse " . $deleteResponse);

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

    /**
     * CheckExistingWorkCenter function
     * @param $divisionDefaultVal
     * @param $workCenterDefaultVal
     * @param $ErrorMsg
     * @return array
     */
    public function CheckExistingDivision($divisionDefaultVal = null, $workCenterDefaultVal = null, $ErrorMsg = null)
    {

        $divisionDefaultSelectedUrl = 'pge%2Fdropdown%2Fget-default-filter&screen=assigned';
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