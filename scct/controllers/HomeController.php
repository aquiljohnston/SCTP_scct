<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use linslin\yii2\curl;

/**
 * HomeController implements the CRUD actions for home model.
 */
class HomeController extends Controller
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
		if(Yii::$app->user->isGuest){
			return $this->redirect(['login/login']);			
		}else{
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
        
		return $this -> render('view', ['model' => json_decode($response)]);
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
