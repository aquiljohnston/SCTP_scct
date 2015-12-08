<?php

namespace app\controllers;

use Yii;
use app\models\user;
use app\models\UserSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use linslin\yii2\curl;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;

/**
 * UserController implements the CRUD actions for user model.
 */
class UserController extends Controller
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
     * Lists all user models.
     * @return mixed
     */
    public function actionIndex()
    {
		// Reading the response from the the api and filling the GridView
		$curl = new curl\Curl();
 
        //get http://example.com/
        $response = $curl->get('http://api.southerncrossinc.com/index.php?r=user%2Findex');
		
		//Passing data to the dataProvider and formating it in an associative array
		$dataProvider = new ArrayDataProvider([
        'allModels' => json_decode($response,true),
		]);
		
				GridView::widget([
			'dataProvider' => $dataProvider,
		]);
		
		return $this -> render('index', ['dataProvider' => $dataProvider]);
    }

    /**
     * Displays a single user model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
		$curl = new curl\Curl();
 
        //get http://example.com/
        $response = $curl->get('http://api.southerncrossinc.com/index.php?r=user%2Fview&id='.$id);
        /* return $this->render('view', [
            'model' => $this->findModel($id),
        ]); */
		
		$dataProvider = new ArrayDataProvider([
        'allModels' => json_decode($response,true),
		]);
		var_dump($dataProvider);
		
		//return $this -> render('view', ['model' => $dataProvider]);
    }

    /**
     * Creates a new user model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
		$curl = new curl\Curl();
 
        //get http://example.com/
        $response = $curl->get('http://api.southerncrossinc.com/index.php?r=user%2Fcreate');
        var_dump(json_decode($response));
		
        $model = new user();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->UserID]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing user model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
		$curl = new curl\Curl();
 
        //get http://example.com/
        $response = $curl->get('http://api.southerncrossinc.com/index.php?r=user%2Fupdate&id='.$id);
        var_dump(json_decode($response));
		
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->UserID]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing user model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
		$curl = new curl\Curl();
 
        //get http://example.com/
        $response = $curl->get('http://api.southerncrossinc.com/index.php?r=user%2Fdelete&id='.$id);
        var_dump(json_decode($response));
        $this->findModel($id)->delete();

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
        if (($model = user::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
