<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
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
//        if (Yii::$app->user->isGuest) {
//            return $this->redirect(['login/login']);
//        }
        //RBAC permissions check
        if (Yii::$app->user->can('viewEquipmentIndex'))
        {
            // Reading the response from the the api and filling the GridView
            $url = 'http://api.southerncrossinc.com/index.php?r=notification%2Fget-notifications&userID='.Yii::$app->session['userID'];
            $response = Parent::executeGetRequest($url);

            //Passing data to the dataProvider and formatting it in an associative array
            $dataProvider = json_decode($response, true);

            $firstName = $dataProvider["firstName"];
            $lastName = $dataProvider["lastName"];

            Yii::trace("Tao".$firstName);
            Yii::trace("Zhang".$lastName);

            $i = 0;
            $equipmentInfo = [];
            $timeCardInfo = [];
            $mileageCardInfo = [];
            foreach ($dataProvider as $dataArray) {
                if ($dataProvider["equipment"]!=null) {
                    $equipmentInfo[$i] = $dataProvider["equipment"][$i];
                }
                if ($dataProvider["timeCards"]!=null) {
                    $timeCardInfo[$i] = $dataProvider["timeCards"][$i];
                }
                if ($dataProvider["mileageCards"]!=null) {
                    $mileageCardInfo[$i] = $dataProvider["mileageCards"][$i];
                }
                $i++;
                if ($i==10) {
                    break;
                }
            }

            $equipmentProvider = new ArrayDataProvider([
                'allModels' => $equipmentInfo,
                'pagination' => [
                    'pageSize' => 10,
                ]
            ]);

            $timeCardProvider = new ArrayDataProvider([
                'allModels' => $timeCardInfo,
                'pagination' => [
                    'pageSize' => 10,
                ]
            ]);

            $mileageCardProvider = new ArrayDataProvider([
                'allModels' => $mileageCardInfo,
                'pagination' => [
                    'pageSize' => 10,
                ]
            ]);

            GridView::widget
            ([
                'dataProvider' => $equipmentProvider,
            ]);

            return $this -> render('index', ['firstName' => $firstName,
                                             'lastName' => $lastName,
                                             'equipmentProvider' => $equipmentProvider,
                                             'timeCardProvider' => $timeCardProvider,
                                             'mileageCardProvider' => $mileageCardProvider]);
            //return $this->render('index');
        }
        else
        {
            return $this->render('index');
        }
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
