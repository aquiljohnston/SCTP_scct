<?php

namespace app\controllers;

use Yii;
use app\models\user;
use app\models\UserSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use linslin\yii2\curl;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;

class BaseController extends Controller
{
	public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['delete'],
                ],
            ],
        ];
    }
	
	//function generates and executes a "GET" request and returns the response
	public static function executeGetRequest($url)
	{
		//set headers
		$headers = array(
			'X-Client:CometTracker',
			'Accept:application/json',
			'Content-Type:application/json',
			'Authorization: Basic '. base64_encode(Yii::$app->session['token'].': ')
			);
		//init new curl
		$curl = curl_init();
		//set curl options
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		//execute curl
		$response = curl_exec ($curl);
		curl_close ($curl);
		
		return $response;
	}
	
	//function generates and executes a "POST" request and returns the response
    public static function executePostRequest($url, $postData)
	{
		//set headers
		$headers = array(
			'X-Client:CometTracker',
			'Accept:application/json',
			'Content-Type:application/json',
			'Content-Length: ' . strlen($postData),
			'Authorization: Basic '. base64_encode(Yii::$app->session['token'].': ')
			);
		//init new curl
		$curl = curl_init();
		//set curl options
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS,$postData);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		//execute curl
		$response = curl_exec ($curl);
		curl_close ($curl);
		
		return $response;
	}
	
	//function generates and executes a "PUT" request and returns the response
	public static function executePutRequest($url, $putData)
	{
		//set headers
		$headers = array(
			'X-Client:CometTracker',
			'Accept:application/json',
			'Content-Type:application/json',
			'Content-Length: ' . strlen($putData),
			'Authorization: Basic '. base64_encode(Yii::$app->session['token'].': '),
			);
		//init new curl
		$curl = curl_init();
		//set curl options
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
		curl_setopt($curl, CURLOPT_POSTFIELDS,$putData);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		//execute put
		$response = curl_exec ($curl);
		curl_close ($curl);
		
		return $response;
	}
	
	//function generates and executes a "Delete" request and returns the response
	public static function executeDeleteRequest($url)
	{
		//set headers
		$headers = array(
			'X-Client:CometTracker',
			'Accept:application/json',
			'Content-Type:application/json',
			'Authorization: Basic '. base64_encode(Yii::$app->session['token'].': ')
			);
		//init new curl
		$curl = curl_init();
		//set curl options
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		//execute delete
		$response = curl_exec ($curl);
		curl_close ($curl);
		
		return $response;
	}
}
