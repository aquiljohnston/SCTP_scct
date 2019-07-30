<?php

namespace app\controllers;

use Yii;
use yii\base\ErrorException;
use yii\data\ArrayDataProvider;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\ServerErrorHttpException;
use yii\web\UnauthorizedHttpException;
use Exception;
use yii\filters\VerbFilter;
use yii\grid\GridView;
use linslin\yii2\curl;
use app\constants\Constants;

/**
 * HomeController implements the CRUD actions for home model.
 */
class HomeController extends BaseController
{

    public $notificationInfo;
    public $timeCardInfo;
    public $mileageCardInfo;

    /**
     * Lists all home models.
     * @return mixed
     */
    public function actionIndex()
    {
        try {
            //guest redirect
            if (Yii::$app->user->isGuest) {
                return $this->redirect(['/login']);
            }
			
			//Check if user has permissions
			self::requirePermission('notificationsGet');
		
            // Reading the response from the the api and filling the GridView
            $url = 'notification%2Fget-notifications';
            $response = Parent::executeGetRequest($url, Constants::API_VERSION_3);
            //Passing data to the dataProvider and formatting it in an associative array
            $dataProvider = json_decode($response, true);

            $this->notificationInfo = [];
            $this->timeCardInfo = [];
            $this->mileageCardInfo = [];

            try {
                if ($dataProvider['notifications'] != null) {
                    $this->notificationInfo = $dataProvider['notifications'];
                }
                if ($dataProvider['timeCards'] != null) {
                    $this->timeCardInfo = $dataProvider['timeCards'];
                }
				if ($dataProvider['mileageCards'] != null) {
                    $this->mileageCardInfo = $dataProvider['mileageCards'];
                }
            } catch (ErrorException $error) {
                //Continue - Unable to retrieve notifications
            }

            $notificationProvider = new ArrayDataProvider([
                'allModels' => $this->notificationInfo,
                'pagination' => false,
				//add data key for sending read update
				'key' => function ($data) {
					return array(
						//check for nulls to avoid error on total row
						'ProjectID' => array_key_exists('ProjectID', $data) ? $data['ProjectID'] : null,
						'NotificationType' => array_key_exists('NotificationType', $data) ? $data['NotificationType'] : null,
						'StartDate' => array_key_exists('StartDate', $data) ? $data['StartDate'] : null,
						'EndDate' => array_key_exists('EndDate', $data) ? $data['EndDate'] : null,
					);
				}
            ]);

            $timeCardProvider = new ArrayDataProvider([
                'allModels' => $this->timeCardInfo,
                'pagination' => false
            ]);
			
			$mileageCardProvider = new ArrayDataProvider([
                'allModels' => $this->mileageCardInfo,
                'pagination' => false
            ]);
			
            return $this->render('index', [
                'notificationProvider' => $notificationProvider,
                'timeCardProvider' => $timeCardProvider,
				'mileageCardProvider' => $mileageCardProvider]);
        } catch (UnauthorizedHttpException $e){
            Yii::$app->response->redirect(['login/index']);
        } catch (ForbiddenHttpException $e) {
            throw $e;
        } catch (ErrorException $e) {
            throw new \yii\web\HttpException(400);
        } catch (Exception $e) {
            throw new ServerErrorHttpException();
        }
    }
	
	public function actionSetNotificationRead(){
		try{
			$data = Yii::$app->request->post();
			$jsonData = json_encode($data);
			
			//put url
			$putUrl = 'notification%2Fread';
			$putResponse = Parent::executePutRequest($putUrl, $jsonData,Constants::API_VERSION_3); // indirect rbac
			$obj = json_decode($putResponse, true);	
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

        foreach ($this->timeCardInfo as $value) {
            if (!($value['ProjectName'] === 'Total')) { // Make sure we do not enter 'Total' into our search for all unapproved item(s)
                if ($allProjectsString === '') { // The first string does not need to have a '|' character before concatenating the string
                    $allProjectsString = $this->trimString($value['ProjectName']);
                } else {
                    $allProjectsString = $allProjectsString . ', ' . $this->trimString($value['ProjectName']);
                }
            }
        }

        return $allProjectsString;
    }
}
