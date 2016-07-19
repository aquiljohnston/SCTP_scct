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
 * HomeController implements the CRUD actions for home model.
 */
class HomeController extends BaseController
{

    public $equipmentInfo;
    public $timeCardInfo;
    public $mileageCardInfo;

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
     * Lists all home models.
     * @return mixed
     */
    public function actionIndex()
    {
		 //guest redirect
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['login/login']);
        }
        // Reading the response from the the api and filling the GridView
        $url = 'http://api.southerncrossinc.com/index.php?r=notification%2Fget-notifications';
        $response = Parent::executeGetRequest($url);

        //Passing data to the dataProvider and formatting it in an associative array
        $dataProvider = json_decode($response, true);

        $firstName = $dataProvider["firstName"];
        $lastName = $dataProvider["lastName"];

        Yii::trace("Tao".$firstName);
        Yii::trace("Zhang".$lastName);

        $this->equipmentInfo = [];
        $this->timeCardInfo = [];
        $this->mileageCardInfo = [];

        try {
            if ($dataProvider["equipment"]!=null) {
                $this->equipmentInfo = $dataProvider["equipment"];
            }
            if ($dataProvider["timeCards"]!=null) {
                $this->timeCardInfo = $dataProvider["timeCards"];
            }
            if ($dataProvider["mileageCards"]!=null) {
                $this->mileageCardInfo = $dataProvider["mileageCards"];
            }
        } catch(ErrorException $error) {
            //Continue - Unable to retrieve equipment item
        }

        $equipmentProvider = new ArrayDataProvider([
            'allModels' => $this->equipmentInfo,
            'pagination' => [
                'pageSize' => 10,
            ]
        ]);

        $timeCardProvider = new ArrayDataProvider([
            'allModels' => $this->timeCardInfo,
            'pagination' => [
                'pageSize' => 10,
            ]
        ]);

        $mileageCardProvider = new ArrayDataProvider([
            'allModels' => $this->mileageCardInfo,
            'pagination' => [
                'pageSize' => 10,
            ]
        ]);

        GridView::widget
        ([
            'dataProvider' => $equipmentProvider,
        ]);

		//var_dump($timeCardInfo);
		
        return $this -> render('index', [
										 'model' => $this->timeCardInfo,
										 'firstName' => $firstName,
                                         'lastName' => $lastName,
                                         'equipmentProvider' => $equipmentProvider,
                                         'timeCardProvider' => $timeCardProvider,
                                         'mileageCardProvider' => $mileageCardProvider]);
    }

    /**
     * Displays a home model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
		$curl = new curl\Curl();
    }

    /**
     * Creates a new home model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
		
    }

    /**
     * Base method for trimming a project when it has spaces to '+' characters in order to search accordingly
     *
     * @param $string - String that will remove all white spaces and replace them with '+' characters in order for the filter functions
     * to work on the time/mileage card and equipment view pages
     * @return mixed
     */
    public function trimString($string) {
        return preg_replace('/\s+/', '+', $string);
    }

    /**
     * Get all of the projects from the get notifications call. This call is currently based on the projects received from the MileageCard response.
     * IF this needs to be changed in the future, simply add a parameter into the method to filter which projects need to be searched for
     *
     * @return mixed|string - String of all the projects separated by a '|' in order to search for all of the projects on the target index view
     */
    public function getAllProjects() {
        $allProjectsString = '';

        foreach ($this->mileageCardInfo as $value) {
            if (!($value['Project'] === 'Total')) { // Make sure we do not enter 'Total' into our search for all unapproved item(s)
                if ($allProjectsString === '') { // The first string does not need to have a '|' character before concatenating the string
                    $allProjectsString = $this->trimString($value['Project']);
                } else {
                    $allProjectsString = $allProjectsString . '|' . $this->trimString($value['Project']);
                }
            }
        }

        return $allProjectsString;
    }
	
	/**
	 * Get Nav menu Json 
	 * It will return nav menu in json format
	 * @return mixed
	 * @throws ForbiddenHttpException
	 */
    public function actionGetNavMenu()
    {

		if (Yii::$app->request->isAjax) {
			$data = Yii::$app->request->post();
			
			$navMenuUrl = "http://api.southerncrossinc.com/index.php?r=menu%2Fget&projectID=3";
			//get nav menu by calling API route
			$mavMenuResponse = Parent::executeGetRequest($navMenuUrl); // indirect rbac
			//set up response data type
			Yii::$app->response->format = 'json';

			return ['navMenu' => $mavMenuResponse];
		}
	}

    /**
     * Updates an existing home model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
		//$model = $this->findModel($id);
    }

    /**
     * Deletes an existing home model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
		//$this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the user model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return user the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
         // if (($model = user::findOne($id)) !== null) {
            // return $model;
        // } else {
            // throw new NotFoundHttpException('The requested page does not exist.');
        // } 
		
    }
}
