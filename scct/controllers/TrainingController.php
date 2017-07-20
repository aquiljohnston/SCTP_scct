<?php
namespace app\controllers;
use Yii;
/**
 * TrainingController class is used to display videos on the website
 * The training videos will be part of the core application for user reference. 
 * @author Jose Pinott <jpinott@southerncrossinc.com>
 * 
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
        }
    }
}
