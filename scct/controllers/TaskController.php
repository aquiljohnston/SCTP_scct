<?php
namespace app\controllers;

/**
 * Created by PhpStorm.
 * User: tzhang
 * Date: 12/19/2017
 * Time: 9:29 AM
 */

use Yii;
use app\models\task;
use app\controllers\BaseController;
use yii\data\Pagination;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\ForbiddenHttpException;
use yii\data\ArrayDataProvider;
use linslin\yii2\curl;
use app\constants\Constants;

/**
 * TaskController implements the CRUD actions for task model.
 */
class TaskController extends BaseController
{
    /**
     * Lists all tasks and users
     * @return mixed
     */
    public function actionIndex()
    {
        //guest redirect
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['/login']);
        }

        //Check if user has permission to view task page
        //self::requirePermission("viewTaskMgmt");

        $model = new \yii\base\DynamicModel([
            'taskfilter','userfilter', 'pagesize'
        ]);
        $model->addRule('taskfilter', 'string', ['max' => 32])
            ->addRule('userfilter', 'string', ['max' => 32])
            ->addRule('pagesize', 'string', ['max' => 32]);//get page number and records per page

        // check if type was post, if so, get value from $model
        if ($model->load(Yii::$app->request->get())) {
            $listPerPageParam = 50;
            $taskFilterParam = $model->taskfilter;
            $userFilterParam = $model->userfilter;
        } else {
            $listPerPageParam = 50;
            $taskFilterParam = null;
            $userFilterParam = null;
        }

        $pageParam = 1;

        // Reading the response from the the api and filling the Task GridView
        $url = "task%2Fget-all-task&"
            . http_build_query(
                [
                    'filter' => $taskFilterParam,
                    'listPerPage' => $listPerPageParam,
                    'page' => $pageParam
                ]);
        $response = Parent::executeGetRequest($url, Constants::API_VERSION_2); // indirect rbac
        Yii::trace("TASK OUT: ".$response);
        $resultData = json_decode($response, true);
        //$taskPages = new Pagination($resultData['pages']);

        //Passing data to the dataProvider and format it in an associative array
        $taskDataProvider = new ArrayDataProvider([
            'allModels' => $resultData['assets'],
            'pagination' => [
                'pageSize' => 100,
            ],
        ]);

        // Sorting Task table
        $taskDataProvider->sort = [
            'attributes' => [
                'TaskName' => [
                    'asc' => ['TaskName' => SORT_ASC],
                    'desc' => ['TaskName' => SORT_DESC]
                ],
            ]
        ];

        // Reading the response from the the api and filling the surveyorGridView
        $getUrl = 'dispatch%2Fget-surveyors&' . http_build_query([
                'filter' => $userFilterParam,
            ]);
        $surveyorsResponse = json_decode(Parent::executeGetRequest($getUrl, Constants::API_VERSION_2), true); // indirect rbac
        $userDataProvider = new ArrayDataProvider
        ([
            'allModels' => $surveyorsResponse['users'],
            'pagination' => false,
        ]);

        $userDataProvider->key = 'UserID';

        return $this->render('index', [
            'taskDataProvider' => $taskDataProvider,
            'userDataProvider' => $userDataProvider,
            'model' => $model,
            //'taskPages' => $taskPages
        ]);

    }
}
