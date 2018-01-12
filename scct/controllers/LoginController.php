<?php

namespace app\controllers;

use Yii;
use app\models\User;
use app\models\Activity;
use app\models\TimeEntry;
use yii\base\Exception;
use yii\filters\AccessControl;
use app\controllers\BaseController;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use linslin\yii2\curl;
use yii\web\UnauthorizedHttpException;
use app\constants\Constants;

class LoginController extends BaseController
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionIndex()
    {
		try {
			$isGuest = \Yii::$app->user->isGuest;
		} catch (UnauthorizedHttpException $exception) {
			$isGuest = true;
		}
		/*if (!$isGuest) {
			return $this->redirect(['home/index']);
		}else{	*/
			$loginError = false;
			$model = new LoginForm();
			//$geoLocationData = [];
					
			if($postData = Yii::$app->request->post())		
			{
				$model->username = $postData['username'];
				$model->password = $postData['password'];
				//$geoLocationData = $postData['GeoData'];
						
				if ($user = $model->login()) {	
					//set session variables			
					Yii::$app->session->set('token', $user['AuthToken']);
					Yii::$app->session->set('userID', $user['AuthUserID']);
					Yii::$app->session->set('UserFirstName', $user['UserFirstName']);
					Yii::$app->session->set('UserLastName', $user['UserLastName']);
					//call helper method to set additional session values
					self::getSessionData();
					
					$userIdentity = new User();
					$userIdentity->UserID = $user['AuthUserID'];
				
					//call function to create/send login activity
					//self::logActivity('WebLoginActivity', $geoLocationData);
					
					Yii::$app->user->login($userIdentity);
					Yii::trace("ProjectLandingPage: " . $user['ProjectLandingPage'] );
					if($user['ProjectLandingPage'] != null)
					{
						return $this->redirect([$user['ProjectLandingPage']]);
					}
					else
					{
						return $this->redirect(['home/index']);
					}
					//return $this->redirect('home');
				} else {
					$loginError = true;
				}
				// Clear the fields
				$model = new LoginForm();
				return $this->renderAjax('index', [
					'model' => $model,
					'loginError' => $loginError
				]);	
			};	
			return $this->render('index', [
				'model' => $model,
				'loginError' => $loginError
			]);	
		//}
	}

    public function actionUserLogout()
    {	
		$id = Yii::$app->session['userID'];
		
		try {
			//call function to create/send logout activity
			self::logActivity('WebLogoutActivity');

            $url = 'login%2Fuser-logout';
            $version = "v2";
            $response = Parent::executeGetRequest($url, $version);
            Yii::$app->user->logout();
        } catch(UnauthorizedHttpException $exception) {
            return $this->redirect(['index']);
        } catch(Exception $exception){
            return $this->redirect(['index']);
        }
        return $this->redirect(['index']);
        //return $this->redirect(['login/index']);
    }
	
	public function logActivity($activityTitle, $geoLocationData=null)
    {
        try {
            //create models
            $activity = new Activity();
            $timeEntry = new TimeEntry();

            //populate activity data
            $activity->ActivityUID = BaseController::generateUID($activityTitle);
            $activity->ActivityStartTime = BaseController::getDate();
            $activity->ActivityEndTime = BaseController::getDate();
            $activity->ActivitySrcDTLT = BaseController::getDate();
            $activity->ActivityTitle = $activityTitle;
            $activity->ActivityCreateDate = BaseController::getDate();
            $activity->ActivityCreatedUserUID = Yii::$app->session['userID'];
            $activity->ActivityAppVersion = 'Web_' . Constants::DEFAULT_VERSION;
            $activity->ActivityAppVersionName = 'Web_' . BaseController::urlPrefix() . '_' . Constants::DEFAULT_VERSION;
            //loop and format geolocation data
            if (is_array($geoLocationData)) {
                /*
                Geo Location Data Key
                0 => 'Latitude'
                1 => 'Longitude'
                2 => 'Accuracy'
                3 => 'Altitude'
                4 => 'AltitudeAccuracy'
                5 => 'Heading'
                6 => 'Speed'
                7 => 'Timestamp'
                */
                $activity->ActivityLatitude = $geoLocationData[0];
                $activity->ActivityLongitude = $geoLocationData[1];
                $activity->ActivityFixQuality = $geoLocationData[2];
                $activity->ActivityAltitudemetersAboveMeanSeaLevel = $geoLocationData[3];
                $activity->ActivityBearing = $geoLocationData[5];
                $activity->ActivitySpeed = $geoLocationData[6];
                $activity->ActivityGPSTime = $geoLocationData[7];
            } else {
                $activity->ActivityComments = $geoLocationData;
            }

            //populate timeEntry data
            $timeEntry->TimeEntryUserID = Yii::$app->session['userID'];
            $timeEntry->TimeEntryStartTime = BaseController::getDate();
            $timeEntry->TimeEntryEndTime = BaseController::getDate();
            $timeEntry->TimeEntryActiveFlag = "1";
            $timeEntry->TimeEntryTimeCardID = Yii::$app->session['userTimeCard'];
            $timeEntry->TimeEntryCreateDate = BaseController::getDate();
            $timeEntry->TimeEntryCreatedBy = Yii::$app->session['userID'];

            //build post json
            $postData = [];
            $activityArray = [];
            $timeEntryArray = [];

            //populate post data
            $timeEntryArray[] = $timeEntry;
            $activity['timeEntry'] = $timeEntryArray;
            $activityArray[] = $activity;
            $postData['activity'] = $activityArray;

            //execute post request
            $response = BaseController::executePostRequest('activity%2Fcreate', json_encode($postData), Constants::API_VERSION_2);
        } catch(UnauthorizedHttpException $exception) {
            // This is reached when the user is logging out with an expired token.
            return $this->redirect(['index']);
        } catch (Exception $e){
            return $this->redirect(['index']);
        }
	}
	
	//helper method for login action to make api calls to retrieve session data
	//and save values in session variables. 
	private static function getSessionData()
	{
		//get users time card and store in session data
		// $timeCardResponse = BaseController::executeGetRequest('time-card%2Fget-card&userID=' . Yii::$app->session['userID']);
		// $userTimeCard = json_decode($timeCardResponse, true);
		// if(is_array($userTimeCard) && array_key_exists('TimeCardID', $userTimeCard))
		// {
			// Yii::$app->session->set('userTimeCard', $userTimeCard['TimeCardID']);
		// }
		
		//get web dropdowns and store in sesssion data
		$dropdownResponse = BaseController::executeGetRequest('dropdown%2Fget-web-drop-downs', Constants::API_VERSION_2);
		$dropdowns = json_decode($dropdownResponse, true);
		if(is_array($dropdowns) && array_key_exists('WebDropDowns', $dropdowns))
		{
			Yii::$app->session->set('webDropDowns', $dropdowns['WebDropDowns']);
		}
	}
}
