<?php

namespace app\controllers;

use Yii;
use app\models\user;
use app\models\UserSearch;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use linslin\yii2\curl;
use yii\data\ArrayDataProvider;

class BaseController extends Controller
{

    const VERSION = "v1";
    const XClient = "apidev";
    
	public function filterColumn($resultData, $column, $param) {
		if($resultData == null) {
			return null;
		}
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

	public function filterColumnMultiple($resultData, $column, $param) {
		if($resultData == null) {
			return null;
		}
		// http://stackoverflow.com/a/28452101
		// This code has been modified from its original version. It has been formatted to fit your TV.
		// (Modified from SA code to handle multiple parameters);
		$terms = explode("|", Yii::$app->request->getQueryParam($param));
		$terms = array_map('trim', $terms);
		if(count($terms)==0) {
			return $resultData;
		}
		$filteredResultData = array_filter($resultData, function($item) use ($column, $terms) {
			foreach($terms as $term) {
				if (strlen($term) > 0) {
					if (stripos($item[$column], $term) !== false) {
						return true;
					}
				} else {
					return true;
				}
			}
			return false;
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

    public static function prependURL($path) {
        return "http://apidev.southerncrossinc.com/index.php?r=" . self::VERSION . "%2F$path";
        //return "http://localhost:9090/index.php?r=" . self::VERSION . "%2F$path";
    }

    //function generates and executes a "GET" request and returns the response
	public static function executeGetRequest($url)
	{
        $url = self::prependURL($url);
		//set headers
		$headers = array(
			'X-Client:' . self::XClient,
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
		if($httpCode == 401) // Not authenticated
		{
			//should be able to check response for error message at this point if we end up having more unauthorized cases
            $url = ['login/user-logout'];
            Yii::$app->getResponse()->redirect($url)->send();
		}
		else if($httpCode == 403) // Inadequate permissions.
		{
			throw new ForbiddenHttpException('You do not have adequate permissions to perform this action.');
		}
		curl_close ($curl);
		
		return $response;
	}
	
	//function generates and executes a "POST" request and returns the response
    public static function executePostRequest($url, $postData)
	{
        $url = self::prependURL($url);
		//set headers
		$headers = array(
			'X-Client:' . self::XClient,
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
            $url = ['login/user-logout'];
            Yii::$app->getResponse()->redirect($url)->send();
		}
		else if($httpCode == 403) // Inadequate permissions.
		{
			throw new ForbiddenHttpException('You do not have adequate permissions to perform this action.');
		}
		curl_close ($curl);
		
		return $response;
	}
	
	//function generates and executes a "PUT" request and returns the response
	public static function executePutRequest($url, $putData)
	{
        $url = self::prependURL($url);
		//set headers
		$headers = array(
			'X-Client:' . self::XClient,
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
            $url = ['login/user-logout'];
            Yii::$app->getResponse()->redirect($url)->send();
		}
		else if($httpCode == 403) // Inadequate permissions.
		{
			throw new ForbiddenHttpException('You do not have adequate permissions to perform this action.');
		}
		curl_close ($curl);
		
		return $response;
	}
	
	//function generates and executes a "Delete" request and returns the response
	public static function executeDeleteRequest($url)
	{
        $url = self::prependURL($url);
		//set headers
		$headers = array(
			'X-Client:' . self::XClient,
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
            $url = ['login/user-logout'];
            Yii::$app->getResponse()->redirect($url)->send();
		}
		else if($httpCode == 403) // Inadequate permissions.
		{
			throw new ForbiddenHttpException('You do not have adequate permissions to perform this action.');
		}
		curl_close ($curl);
		
		return $response;
	}

	public static function requirePermission($permission) {
		//API RBAC permissions check
		//Will throw 403 if current user doesn't have permission
		self::executeGetRequest("permissions%2Fcheck-permission&permission=$permission");
		return true;
	}
	
	public static function can($permission) {
		try {
			self::requirePermission($permission);
			return true;
		} catch(ForbiddenHttpException $e) {
			return false;
		}
	}
}
