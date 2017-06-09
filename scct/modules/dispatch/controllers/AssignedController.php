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
            'assignedfilter', 'pagesize'
        ]);
        $model->addRule('assignedfilter', 'string', ['max' => 32])
            ->addRule('pagesize', 'string', ['max' => 32]);

        //check request
        if ($model->load(Yii::$app->request->queryParams)) {

            Yii::trace("assignedfilter " . $model->assignedfilter);
            Yii::trace("pagesize " . $model->pagesize);
            $divisionParams = $model->division;
            $assignedPageSizeParams = $model->pagesize;
            $assignedFilterParams = $model->assignedfilter;
        } else {
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
                'canUnassign' => $canUnassign,
                'canAddSurveyor' => $canAddSurveyor,
                'divisionParams' => $divisionParams,
                'assignedPageSizeParams' => $assignedPageSizeParams,
                'assignedFilterParams' => $assignedFilterParams,
            ]);
        }
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
                $data = Yii::$app->request->post();
                $data = self::GenerateUnassignedData($data['MapGrid'], $data['AssignedToIDs']);
                $json_data = json_encode($data);
                Yii::trace("Unassigned Data: ".$json_data);

                // post url
                $deleteUrl = 'dispatch%2Funassign';
                $deleteResponse = Parent::executeDeleteRequest($deleteUrl, $json_data, self::API_VERSION_2); // indirect rbac
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

    public function GenerateUnassignedData(array $mapGridArr, array $assignedToIDs){
        $unassignedArr = [];
        for ($i = 0; $i < count($mapGridArr); $i++){
            $data = array(
                'MapGrid' => $mapGridArr[$i],
                'AssignedUserID' => $assignedToIDs[$i],
            );
            array_push($unassignedArr, $data);
        }
        $unassignedArr['data'] = $unassignedArr;
        return $unassignedArr;
    }
}