<?php
namespace app\controllers;
use app\controllers\BaseController;
//require 'BaseController.php';
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
            $this->isGuestUser();
			
			//Check if user has permissions
			self::requirePermission("viewTrainingMenu");
			
            return $this->render('index');
        } catch (UnauthorizedHttpException $e){
            Yii::$app->response->redirect(['login/index']);
        } catch (ForbiddenHttpException $e) {
            Yii::$app->runAction('login/user-logout');
        }
    }
}