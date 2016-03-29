<?php

namespace app\controllers;

use Yii;
use app\controllers\BaseController;
use yii\base\ErrorException;
use yii\data\ArrayDataProvider;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\filters\VerbFilter;
use yii\grid\GridView;
use linslin\yii2\curl;
use kartik\sortinput\SortableInput;

/**
 * ProjectLandingController
 */
class ProjectLandingController extends BaseController
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all project models.
     * @return mixed
     */
    public function actionIndex()
    {
		 //guest redirect
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['login/login']);
        }
        // Reading the response from the the api and filling the GridView
        $url = 'http://api.southerncrossinc.com/index.php?r=user%2Fget-all-projects&userID='.Yii::$app->session['userID'];
        $response = Parent::executeGetRequest($url);

        //Passing data to the dataProvider and formatting it in an associative array
        $projectProvider = json_decode($response, true);

        /*$firstName = $dataProvider["firstName"];
        $lastName = $dataProvider["lastName"];

        Yii::trace("Tao".$firstName);
        Yii::trace("Zhang".$lastName);*/

        $projects = [];
        /*$timeCardInfo = [];
        $mileageCardInfo = [];*/

        try {
            if ($projectProvider!=null) {
                $projects = $projectProvider;
            }
        } catch(ErrorException $error) {
            //Continue - Unable to retrieve equipment item
        }

        $projectLandingProvider = new ArrayDataProvider([
            'allModels' => $projects,
            'pagination' => [
                'pageSize' => 10,
            ]
        ]);

        GridView::widget
        ([
            'dataProvider' => $projectLandingProvider,
        ]);

        return $this -> render('index', [
                                         'projectLandingProvider' => $projectLandingProvider,
										 'model' => $projectProvider,
										]);
    }
	
	/**
     * Displays a single project .
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
		//guest redirect
		if (Yii::$app->user->isGuest)
		{
			return $this->redirect(['login/login']);
		}
		//RBAC permissions check
		if (Yii::$app->user->can('viewProject'))
		{
			$url = 'http://api.southerncrossinc.com/index.php?r=project%2Fview&id='.$id;
			$response = Parent::executeGetRequest($url);

			return $this -> render('view', ['model' => json_decode($response), true]);
		}
		else
		{
			throw new ForbiddenHttpException('You do not have adequate permissions to perform this action.');
		}
    }
	
	public function actionAddUser($id)
	{
		//guest redirect
		if (Yii::$app->user->isGuest)
		{
			return $this->redirect(['login/login']);
		}
		//RBAC permissions check
		if (Yii::$app->user->can('viewProject'))
		{
			$url = 'http://api.southerncrossinc.com/index.php?r=project%2Fget-user-relationships&projectID='.$id;
			$projectUrl = 'http://api.southerncrossinc.com/index.php?r=project%2Fview&id='.$id;
			$response = Parent::executeGetRequest($url);
			$projectResponse = Parent::executeGetRequest($projectUrl);
			$users = json_decode($response,true);
			$project = json_decode($projectResponse);
			
			//load get data into variables
			$unassignedData = $users["unassignedUsers"];
			$assignedData = $users["assignedUsers"];
			
			//create model for active form
			$model = new \yii\base\DynamicModel([
				'UnassignedUsers', 'AssignedUsers' ]);
			$model->addRule('UnassignedUsers', 'string')
					 ->addRule('AssignedUsers',  'string');
			
			
		
			if ($model->load(Yii::$app->request->post()))
			{
				//prepare arrays for post request
				//explode strings from active form into arrays
				$unassignedUsersArray = explode(',',$model->UnassignedUsers);
				$assignedUsersArray = explode(',',$model->AssignedUsers);
				//array diff new arrays with arrays previous to submission to get changes
				$usersAdded = array_diff($assignedUsersArray,array_keys($assignedData));
				$usersRemoved = array_diff($unassignedUsersArray,array_keys($unassignedData));
				//load arrays of changes into post data
				$data = [];
				$data["usersRemoved"] = $usersRemoved;
				$data["usersAdded"] = $usersAdded; 
				
				//encode data
				$jsonData = json_encode($data);
				
				//set post url
				$postUrl = 'api.southerncrossinc.com/index.php?r=project%2Fadd-remove-users&projectID='.$id;
				//execute post request
				$postResponse = Parent::executePostRequest($postUrl, $jsonData);
				//refresh page
				return $this->redirect(['add-user', 'id' => $project->ProjectID]);
			}
			else
			{
			return $this -> render('add_user', [
											'project' => $project,
											'model' => $model,
											'unassignedData' => $unassignedData,
											'assignedData' => $assignedData,
									]);
			}	
		}
		else
		{
			throw new ForbiddenHttpException('You do not have adequate permissions to perform this action.');
		}
	}

}
