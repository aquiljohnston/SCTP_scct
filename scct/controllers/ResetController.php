<?php

namespace app\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;


class ResetController extends BaseController
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
     * Lists all home models.
     * @return mixed
     */
    public function actionIndex()
    {
        throw new NotFoundHttpException("Page not found.");
		//create a new dynamic model for form fields
		$model = new \yii\base\DynamicModel([
			'UserName', 'Password', 'NewPassword' 
		]);
		
		$model->addRule(['UserName', 'Password', 'NewPassword'], 'required')
			->addRule(['UserName', 'Password', 'NewPassword'], 'string');
		
		//if form is posted
		if ($model->load(Yii::$app->request->post()) && $model->UserName != "")
		{
			//encrypt passwords
			$model->Password = self::encrypt($model->Password);
			$model->NewPassword = self::encrypt($model->NewPassword);
			
			//pass data into an array to be sent as a json body
			$data = array(
				'UserName' => $model->UserName,
				'Password' => $model->Password,
				'NewPassword' => $model->NewPassword,
			);
			
			$json_data = json_encode($data);
			
			try
			{	
				$url = 'user%2Freset-password';
				
				$json_response = Parent::executePutRequest($url, $json_data, self::API_VERSION_2);
				
				$response = json_decode($json_response, true);

				//Do nothing with response?

				return $this->redirect(['index']);
			} 
			catch (\Exception $e)
			{
				//if exception occurs clear previous passwords
				$model->Password = '';
				$model->NewPassword = '';

				// Do we indicate to user that the reset was not successful?
				return $this -> render('index', [
					'model' => $model,
                    'failure' => true
				]);
			}
		}
        return $this -> render('index', [
			'model' => $model,
            'failure' => false
		]);
    }
	
	private static function encrypt($string)
	{
		//iv and secret key of openssl
		$iv = "abcdefghijklmnop";
		$secretKey = "sparusholdings12";
		
		$encryptedString = openssl_encrypt($string, 'AES-128-CBC', $secretKey, OPENSSL_RAW_DATA, $iv);
		$encodedString = base64_encode($encryptedString);
		
		return $encodedString;
	}
}