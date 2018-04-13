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

        // Sorting User Table
        $userDataProvider->sort = [
            'attributes' => [
                'UserName' => [
                    'asc' => ['UserName' => SORT_ASC],
                    'desc' => ['UserName' => SORT_DESC]
                ],
            ]
        ];

        return $this->render('index', [
            'taskDataProvider' => $taskDataProvider,
            'userDataProvider' => $userDataProvider,
            'model' => $model,
            //'taskPages' => $taskPages
        ]);

    }
	
	/**
     * Add New Task Entry.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param $TimeCardID
     * @param $SundayDate
     * @param null $SaturdayDate
     * @param $timeCardProjectID
     * @return string
     * @throws \yii\web\HttpException
     * @internal param $SatudayDate
     */

    public function actionAddTaskEntry($weekStart = null, $weekEnd = null,$TimeCardID = null, $SundayDate = null, $SaturdayDate = null, $timeCardProjectID = null)
    {
        //guest redirect
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['/login']);
        }

        // convert SundayDate and SaturdayDate to MM/dd/YYYY format
        $SundayDate = date( "m/d/Y", strtotime(str_replace('-', '/', $SundayDate)));
        $SaturdayDate = date( "m/d/Y", strtotime(str_replace('-', '/', $SaturdayDate)));

        self::requirePermission("timeEntryCreate");

        $model = new \yii\base\DynamicModel([
            'TimeCardID',
            'TaskName',
            'Date',
            'StartTime',
            'EndTime',
            'ChargeOfAccountType'
        ]);
        $model -> addRule('TimeCardID', 'string', ['max' => 100], 'required');
        $model -> addRule('TaskName', 'string', ['max' => 100], 'required');
        $model -> addRule('Date', 'string', ['max' => 32], 'required');
        $model -> addRule('StartTime', 'string', ['max' => 100], 'required');
        $model -> addRule('EndTime', 'string', ['max' => 100], 'required');
        $model -> addRule('ChargeOfAccountType', 'string', ['max' => 100], 'required');

        try {
			
			$getAllTaskUrl = 'task%2Fget-all-task&timeCardProjectID='.$timeCardProjectID;
			$getAllTaskResponse = Parent::executeGetRequest($getAllTaskUrl, Constants::API_VERSION_2);
			$allTask = json_decode($getAllTaskResponse, true);
            $allTask = $allTask['assets'] != null ? $this->FormatTaskData($allTask['assets']): $allTask['assets'];

            //get chartOfAccountType for form dropdown
            $getAllChartOfAccountTypeUrl = 'task%2Fget-charge-of-account-type';
            $getAllChartOfAccountTypeResponse = Parent::executeGetRequest($getAllChartOfAccountTypeUrl, Constants::API_VERSION_2);
            $chartOfAccountType = json_decode($getAllChartOfAccountTypeResponse, true);

            if ($model->load(Yii::$app->request->queryParams) && $model->validate()) {
				
				// convert to 24 hour format
				$startTime24 = date("H:i", strtotime($model->StartTime));
				$endTime24 = date("H:i", strtotime($model->EndTime));
			
                $task_entry_data = array(
                    'TimeCardID' => $model->TimeCardID,
                    'TaskName' => 'Task ' . $model->TaskName,
                    'Date' => $model->Date,
                    'StartTime' => $startTime24, 
                    'EndTime' => $endTime24,
                    'CreatedByUserName' => Yii::$app->session['UserName'],
					'ChargeOfAccountType' => $model->ChargeOfAccountType,
                );


                //date must be on or between week start and week end dates

                $start = str_replace('-','/',$weekStart);
                $end = str_replace('-','/',$weekEnd);

                $testDate = date( "Y-m-d", strtotime(str_replace('-', '/',$model->Date)));

                 // make sure date within range
                if (strtotime($testDate) < strtotime($weekStart) || strtotime($testDate) > strtotime($weekEnd)) {
						 throw new \yii\web\HttpException(400);

                }
				
				//probably dont need this check here because there is a validation check on the form
                // check difference between startTime and endTime 
                if ($endTime24 >= $startTime24) {
                    $json_data = json_encode($task_entry_data);
                    try {
                        // post url
                        $url = 'task%2Fcreate-task-entry';
                        $response = Parent::executePostRequest($url, $json_data, Constants::API_VERSION_2);
                        $obj = json_decode($response, true);

                    } catch (\Exception $e) {
                        //return $this->redirect(['show-entries', 'id' => $model->TimeCardID]);
                    }
                } else {
                    //return $this->redirect(['show-entries', 'id' => $model->TimeCardID]);
                }
            } else {
                if (Yii::$app->request->isAjax) {
                    return $this->renderAjax('create_task_entry', [
                        'model' => $model,
                        'allTask' => $allTask,
                        'chartOfAccountType' => $chartOfAccountType,
                        'timeCardID' => $TimeCardID,
                        'SundayDate' => $SundayDate,
                        'SaturdayDate' => $SaturdayDate
                    ]);
                } else {
                    return $this->render('create_task_entry', [
                        'model' => $model,
                        'allTask' => $allTask,
                        'chartOfAccountType' => $chartOfAccountType,
                        'timeCardID' => $TimeCardID,
                        'SundayDate' => $SundayDate,
                        'SaturdayDate' => $SaturdayDate
                    ]);
                }
            }

        } catch (ErrorException $e) {
            throw new \yii\web\HttpException(400);
        }
    }

    /**Format Task Array
     * @param $data
     * @return array
     */
    private function FormatTaskData($data){
        $namePairs = [];

        // check which key exist TaskName or FilterName
        $TaskName = array_key_exists('TaskName', $data[0]) ? 'TaskName' : 'FilterName';

        if ($data != null) {
            $codesSize = count($data);

            for ($i = 0; $i < $codesSize; $i++) {
                $namePairs[$data[$i][$TaskName]] = $data[$i][$TaskName];
            }
        }
        return $namePairs;
    }
}
