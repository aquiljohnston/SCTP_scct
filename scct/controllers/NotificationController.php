<?php

namespace app\controllers;

use Exception;
use InspectionRequest;
use Yii;
use yii\data\ArrayDataProvider;
use yii\web\ForbiddenHttpException;
use yii\web\Response;
use yii\data\Pagination;
use app\constants\Constants;

class NotificationController extends \app\controllers\BaseController
{
    public function actionIndex()
    {
        try {

            // Verify logged in
            if (Yii::$app->user->isGuest) {
                return $this->redirect(['/login']);
            }
			
			//Check if user has permissions
			self::requirePermission("notificationsGet");

            $model = new \yii\base\DynamicModel([
                'notificationfilter', 'pagesize',
            ]);
            $model->addRule('notificationfilter', 'string', ['max' => 32])
                ->addRule('pagesize', 'string', ['max' => 32]);

            //check request
            if ($model->load(Yii::$app->request->queryParams)) {
                $notificationPageSizeParams = $model->pagesize;
                $notificationFilterParams = $model->notificationfilter;
            } else {
                $notificationPageSizeParams = 50;
                $notificationFilterParams = "";
            }

            // get the page number for assigned table
            if (isset($_GET['notificationPageNumber'])) {
                $pageAt = $_GET['notificationPageNumber'];
            } else {
                $pageAt = 1;
            }

            $getUrl = 'notification%2Fget-notification-landing&' . http_build_query([
                    'filter' => $notificationFilterParams,
                    'listPerPage' => $notificationPageSizeParams,
                    'page' => $pageAt,
                ]);
            $getNotificationDataResponse = json_decode(Parent::executeGetRequest($getUrl, Constants::API_VERSION_2), true); //indirect RBAC

            if ($getNotificationDataResponse != null) {
                $notificationData = $getNotificationDataResponse['notification'];
                // set pages to notification table
                $pages = new Pagination($getNotificationDataResponse['pages']);
            }else {
                $notificationData = [];
                $pages = new Pagination();
            }

            // Put data in data provider
            // render page
            $notificationDataProvider = new ArrayDataProvider
            ([
                'allModels' => $notificationData,
                'pagination' => false,
            ]);

            // notification data provider
            $notificationDataProvider->key = 'UserID';


            if (Yii::$app->request->isAjax) {
                return $this->renderAjax('index', [
                    'notificationDataProvider' => $notificationDataProvider,
                    'notificationFilterParams' => $notificationFilterParams,
                    'notificationPageSizeParams' => $notificationPageSizeParams,
                    'model' => $model,
                    'pages' => $pages,
                ]);
            } else {
                return $this->render('index', [
                    'notificationDataProvider' => $notificationDataProvider,
                    'notificationFilterParams' => $notificationFilterParams,
                    'notificationPageSizeParams' => $notificationPageSizeParams,
                    'model' => $model,
                    'pages' => $pages,
                ]);
            }
        } catch (UnauthorizedHttpException $e){
            Yii::$app->response->redirect(['login/index']);
        } catch (ForbiddenHttpException $e) {
            throw new ForbiddenHttpException('You do not have adequate permissions to perform this action.');
        } catch (Exception $e) {
            Yii::$app->runAction('login/user-logout');
        }
    }
}