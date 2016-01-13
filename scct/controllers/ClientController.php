<?php

namespace app\controllers;

use Yii;
use app\models\client;
use app\models\ClientSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\ForbiddenHttpException;

/**
 * ClientController implements the CRUD actions for client model.
 */
class ClientController extends Controller
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
     * Lists all client models.
     * @return mixed
     */
    public function actionIndex()
    {
		//guest redirect
		if (Yii::$app->user->isGuest)
		{
			return $this->redirect(['login/login']);
		}
		//RBAC permissions check
		if (Yii::$app->user->can('viewClientIndex'))
		{
			$searchModel = new ClientSearch();
			$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
			$dataProvider->pagination->pagesize=100;

			return $this->render('index', [
				'searchModel' => $searchModel,
				'dataProvider' => $dataProvider,
			]);
		}
		else
		{
			throw new ForbiddenHttpException('You do not have adequate permissions to perform this action.');
		}
    }

    /**
     * Displays a single client model.
     * @param integer $id
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
		if (Yii::$app->user->can('viewClient'))
		{
			return $this->render('view', [
				'model' => $this->findModel($id),
			]);
		}
		else
		{
			throw new ForbiddenHttpException('You do not have adequate permissions to perform this action.');
		}
    }

    /**
     * Creates a new client model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
		//guest redirect
		if (Yii::$app->user->isGuest)
		{
			return $this->redirect(['login/login']);
		}
		//RBAC permissions check
		if (Yii::$app->user->can('createClient'))
		{
			$model = new client();

			if ($model->load(Yii::$app->request->post()) && $model->save()) {
				return $this->redirect(['view', 'id' => $model->ClientID]);
			} else {
				return $this->render('create', [
					'model' => $model,
				]);
			}
		}
		else
		{
			throw new ForbiddenHttpException('You do not have adequate permissions to perform this action.');
		}
    }

    /**
     * Updates an existing client model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
		//guest redirect
		if (Yii::$app->user->isGuest)
		{
			return $this->redirect(['login/login']);
		}
		//RBAC permissions check
		if (Yii::$app->user->can('updateClient'))
		{
			$model = $this->findModel($id);

			if ($model->load(Yii::$app->request->post()) && $model->save()) {
				return $this->redirect(['view', 'id' => $model->ClientID]);
			} else {
				return $this->render('update', [
					'model' => $model,
				]);
			}
		}
		else
		{
			throw new ForbiddenHttpException('You do not have adequate permissions to perform this action.');
		}
    }

    /**
     * Deletes an existing client model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
		//guest redirect
		if (Yii::$app->user->isGuest)
		{
			return $this->redirect(['login/login']);
		}
		//RBAC permissions check
		if (Yii::$app->user->can('deleteUser'))
		{
			$this->findModel($id)->delete();

			return $this->redirect(['index']);
		}
		else
		{
			throw new ForbiddenHttpException('You do not have adequate permissions to perform this action.');
		}
    }

    /**
     * Finds the client model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return client the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = client::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
