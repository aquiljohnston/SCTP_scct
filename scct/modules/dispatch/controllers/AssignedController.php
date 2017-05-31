<?php

namespace app\modules\dispatch\controllers;

use Yii;
use yii\data\ArrayDataProvider;
use yii\data\Pagination;

class AssignedController extends \app\controllers\BaseController
{
    public function actionIndex()
    {
        // Verify logged in
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['/login']);
        }

        $model = new \yii\base\DynamicModel([
            'division', 'assignedfilter', 'pagesize'
        ]);
        $model->addRule('division', 'string', ['max' => 32])
            ->addRule('assignedfilter', 'string', ['max' => 32])
            ->addRule('pagesize', 'string', ['max' => 32]);

        //check request
        if ($model->load(Yii::$app->request->queryParams)) {

            Yii::trace("division " . $model->division);
            Yii::trace("assignedfilter " . $model->assignedfilter);
            Yii::trace("pagesize " . $model->pagesize);
            $divisionParams = $model->division;
            $assignedPageSizeParams = $model->pagesize;
            $assignedFilterParams = $model->assignedfilter;
        } else {
            $divisionParams = "";
            $assignedPageSizeParams = 10;
            $assignedFilterParams = "";
        }

        // get the page number for assigned table
        if (isset($_GET['userPage'])) {
            $pageAt = $_GET['userPage'];
        } else {
            $pageAt = 1;
        }

        $getUrl = 'dispatch%2Fget-assigned&' . http_build_query([
                'filter' => $assignedFilterParams,
                'listPerPage' => $assignedPageSizeParams,
                'page' => $pageAt
            ]);
        $getAssignedDataResponse = json_decode(Parent::executeGetRequest($getUrl, self::API_VERSION_2), true); //indirect RBAC
        $assignedData = $getAssignedDataResponse['assets'];

        // get divisionList from the api and filling division dropdown
        /*$divisionUrl = 'pge%2Fdropdown%2Fget-assigned-division-dropdown';
        $divisionListResponse = Parent::executeGetRequest($divisionUrl); // indirect rbac
        $divisionList = json_decode($divisionListResponse, true);*/
        $divisionList = [];

        //todo: check permission to un-assign work
        $canUnassign = 1;
        $canAddSurveyor = 1;

        //todo: set default value or callback value
        $divisionParams = "";

        //set paging on assigned table
        $pages = new Pagination($getAssignedDataResponse['pages']);

        $assignedDataProvider = new ArrayDataProvider
        ([
            'allModels' => $assignedData,
            'pagination' => false,
        ]);

        $assignedDataProvider->key = 'MapGrid';

        if (Yii::$app->request->isAjax) {
            return $this->render('index', [
                'assignedDataProvider' => $assignedDataProvider,
                'model' => $model,
                'pages' => $pages,
                'divisionList' => $divisionList,
                'canUnassign' => $canUnassign,
                'canAddSurveyor' => $canAddSurveyor,
                'divisionParams' => $divisionParams,
                'assignedPageSizeParams' => $assignedPageSizeParams,
                'assignedFilterParams' => $assignedFilterParams,
            ]);
        } else {
            return $this->render('index', [
                'assignedDataProvider' => $assignedDataProvider,
                'model' => $model,
                'pages' => $pages,
                'divisionList' => $divisionList,
                'canUnassign' => $canUnassign,
                'canAddSurveyor' => $canAddSurveyor,
                'divisionParams' => $divisionParams,
                'assignedPageSizeParams' => $assignedPageSizeParams,
                'assignedFilterParams' => $assignedFilterParams,
            ]);
        }
    }

    //todo: need to see if dependent dropdown list is needed
    // get workCenter dependent dropdown list
    /*public function actionGetworkcenter()
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
    }*/

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
                // loop the data array to get all MapGrid's.
                //Yii::trace("unassignWorkQueue ".json_encode($data["AssignedWorkQueueUID"]));
                foreach ($data["MapGrid"] as $key) {

                    Yii::Trace("MapGrid ; " . $key);
                    $mapGridArr[] = $key;
                }
                // loop the data array to get all assignedUserID's.
                foreach ($data["AssignedUserID"] as $key) {
                    Yii::Trace("AssignedUserID ; " . $key);
                    $assignedUserIDs[] = $key;
                }

                $data = array(
                    'MapGrid' => $mapGridArr,
                    'AssignedUserID' => $assignedUserIDs,
                );
                $json_data = json_encode($data);

                // post url
                $deleteUrl = 'dispatch%2Funassign';
                $deleteResponse = Parent::executeDeleteRequest($deleteUrl, $json_data); // indirect rbac
                Yii::trace("unassignputResponse " . $deleteResponse);

            } else {
                throw new \yii\web\BadRequestHttpException;
            }
        } catch (ForbiddenHttpException $e) {
            throw new ForbiddenHttpException;
        } catch (\Exception $e) {
            Yii::$app->runAction('login/user-logout');
        }
    }
}