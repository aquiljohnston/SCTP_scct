<?php

namespace app\controllers;

use Yii;
use app\models\User;
use yii\filters\AccessControl;
use app\controllers\BaseController;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use linslin\yii2\curl;
use yii\web\UnauthorizedHttpException;

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
        //guest redirect
        try {
            if (Yii::$app->user->isGuest) {
                return $this->redirect(['login/login']);
            } else {
                return $this->redirect(['home/index']);
            }
		} catch (UnauthorizedHttpException $exception) {
            return $this->redirect(['login/login']);
        }

        // $model = new LoginForm();
        // return $this->render('index', [
        //     'model' => $model,
        // ]);
    }

    public function actionLogin()
    {
        try {
            $isGuest = \Yii::$app->user->isGuest;
        } catch (UnauthorizedHttpException $exception) {
            $isGuest = true;
        }
        if (!$isGuest) {
				return $this->redirect(['home/index']);
		}else{

			$loginError = false;
			$model = new LoginForm();
			if ($model->load(Yii::$app->request->post()) && $user = $model->login()) {
				Yii::$app->session->set('token', $user['AuthToken']);
				Yii::$app->session->set('userID', $user['AuthUserID']);
				Yii::$app->session->set('UserFirstName', $user['UserFirstName']);
				Yii::$app->session->set('UserLastName', $user['UserLastName']);
				Yii::Trace("session user id: ".Yii::$app->session['userID']);
				$userIdentity = new User();
				$userIdentity->UserID = $user['AuthUserID'];
				Yii::$app->user->login($userIdentity);
				Yii::Trace("identity user id: ".Yii::$app->user->getId());
				return $this->redirect(['home/index']);
			} else {
				if(Yii::$app->request->isPost) {
					$loginError = true;
				}
				// Clear the fields
				$model = new LoginForm();
			}
			return $this->render('index', [
				'model' => $model,
				'loginError' => $loginError
			]);
			
		}	
    }

    public function actionUserLogout()
    {	
		Yii::Trace("User Logout.");
		$id = Yii::$app->session['userID'];
		
		try {
		    Yii::$app->user->logout();
            $url = 'login%2Fuser-logout&userID='.$id;
            $response = Parent::executeGetRequest($url);
        } catch(\Exception $exception) {
            // This is reached when the user is logging out with an expired token.
		}

        return $this->redirect(['login/index']);
    }
}
