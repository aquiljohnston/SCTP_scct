<?php

namespace app\controllers;

use Yii;
use yii\base\ErrorException;
use yii\data\ArrayDataProvider;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use Exception;
use yii\filters\VerbFilter;
use yii\grid\GridView;
use linslin\yii2\curl;

/**
 * TrainingController class is used to display videos on the website
 * The training videos will be part of the core application for user reference. 
 */
class TrainingController extends BaseController {
    /**
     * Default controller action
     * @returns the index view for controller
     */
    public function actionIndex() {
        try {
            //guest redirect
            if (Yii::$app->user->isGuest) {
                return $this->redirect(['/login']);
            }
            return $this->render('index');
        } catch (ForbiddenHttpException $e) {
            Yii::$app->runAction('login/user-logout');
        } catch (Exception $e) {
            Yii::$app->runAction('login/user-logout');
        }
    }
}
