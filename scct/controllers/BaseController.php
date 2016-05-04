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

class BaseController extends Controller
{
	public function filterColumn($resultData, $column, $param) {
		// http://stackoverflow.com/a/28452101
		$filteredResultData = array_filter($resultData, function($item) use ($column, $param) {
			$nameFilterParam = Yii::$app->request->getQueryParam($param, '');
			if (strlen($nameFilterParam) > 0) {
				if (stripos($item[$column], $nameFilterParam) !== false) {
					return true;
				} else {
					return false;
				}
			} else {
				return true;
			}
		});
		return $filteredResultData;
	}
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
		//check authorization, logout and redirect to login if unauthorized
		$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		if($httpCode == 401)
		{
			//should be able to check response for error message at this point if we end up having more unauthorized cases
			Parent::redirect("http://scct.southerncrossinc.com/index.php?r=login%2Fuser-logout");
		}
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
		//check authorization, logout and redirect to login if unauthorized
		$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		if($httpCode == 401)
		{
			Parent::redirect("http://scct.southerncrossinc.com/index.php?r=login%2Fuser-logout");
		}
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
		//check authorization, logout and redirect to login if unauthorized
		$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		if($httpCode == 401)
		{
			Parent::redirect("http://scct.southerncrossinc.com/index.php?r=login%2Fuser-logout");
		}
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
		//check authorization, logout and redirect to login if unauthorized
		$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		if($httpCode == 401)
		{
			Parent::redirect("http://scct.southerncrossinc.com/index.php?r=login%2Fuser-logout");
		}
		curl_close ($curl);
		
		return $response;
	}
}
