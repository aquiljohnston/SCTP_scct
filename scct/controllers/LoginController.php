<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use app\controllers\BaseController;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use linslin\yii2\curl;

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
        return $this->redirect(['login']);

        // $model = new LoginForm();
        // return $this->render('index', [
        //     'model' => $model,
        // ]);
    }

    public function actionLogin()
    {
        // if (!\Yii::$app->user->isGuest) {
        //     return $this->goHome();
        // }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $user = $model->login()) {
            Yii::$app->session->set('token', $user['AuthToken'].': ');
            return $this->redirect('index.php?r=home&token='. $user['AuthToken']);
        }
        return $this->render('index', [
            'model' => $model,
        ]);
    }

    public function actionUserLogout()
    {	
		Yii::Trace("User Logout.");
		$url = 'http://api.southerncrossinc.com/index.php?r=login%2Fuser-logout';
		Yii::Trace("Request url: ".$url);
		$response = Parent::executeGetRequest($url);
		Yii::Trace("Request Response: ". $response);
		//$curl = new curl\Curl();
        //$response = $curl->get('http://api.southerncrossinc.com/index.php?r=login%2Fuser-logout');
		
		//Yii::$app->user->logout();
		
        return $this->redirect(['login']);
    }
}
