<?php

namespace app\controllers;

use Yii;
use yii\base\ErrorException;
use yii\data\ArrayDataProvider;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\grid\GridView;
use linslin\yii2\curl;

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
			Yii::Trace("project details are : ".$response);
			$projectProvider = json_decode($response, true);
			/*$project[] = [];
			
		try {
            if ($projectProvider!=null) {
                $project = $projectProvider;
            }
        } catch(ErrorException $error) {
            //Continue - Unable to retrieve equipment item
        }*/

        $singleprojectProvider = new ArrayDataProvider([
            'allModels' => $projectProvider,//$project,
			/*'pagination' => [
					'pageSize' => 1,
				],*/
        ]);

        GridView::widget
        ([
            'dataProvider' => $singleprojectProvider,
        ]);

			return $this -> render('view', [
                                         'singleprojectProvider' => $singleprojectProvider,
										]);
		}
		else
		{
			throw new ForbiddenHttpException('You do not have adequate permissions to perform this action.');
		}
    }

}
