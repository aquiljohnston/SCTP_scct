<?php
namespace app\controllers;
use app\controllers\BaseController;

class ExpenseController extends BaseController {
    /**
     * Default controller action
     * @returns the index view for controller
     */
    public function actionIndex() {
        try {
            $this->isGuestUser();
			
			//Check if user has permissions
			self::requirePermission('viewExpenseMgmt');
			
            return $this->render('index');
        } catch (UnauthorizedHttpException $e){
            Yii::$app->response->redirect(['login/index']);
        } catch(ForbiddenHttpException $e) {
            throw $e;
        } catch(ErrorException $e) {
            throw new \yii\web\HttpException(400);
        } catch(Exception $e) {
            throw new ServerErrorHttpException();
        }
    }
}