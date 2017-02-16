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
use yii\web\UnauthorizedHttpException;

class BaseController extends Controller
{

    // VERSION contains the string for the SCAPI version that one wishes to target.
    const API_VERSION_1 = "v1";
    const API_VERSION_2 = "v2";

    // arbitrary numbers (can't be equal to each other)
    // the numbers are prime for no reason whatsoever
    const API_SERVER_LOCALHOST = 1;
    const API_SERVER_DEV = 2;
    const API_SERVER_STAGE = 3;
    const API_SERVER_PRODUCTION = 7;

    /*
     * Modify these constants in order to set up your SCCT install.
     */
    // Legacy support. Version 1 calls do not specify which version to use, so we use a default value.
    const DEFAULT_VERSION = self::API_VERSION_1;
    // Pick the server you want to target. See constants above.
    const TARGET_API_SERVER = self::API_SERVER_LOCALHOST; //TODO: Detect this setting with a config file or environment scanning (i.e. domain detection)
    // X-Client corresponds to constants in SCAPI that indicate which database to point to.
    // It is sent in the header of api calls
    const XClient = "apidev";

    const UNAUTH_MESSAGE = "Please log in again. Your session has expired. Redirecting...";

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
		// (Modified from SO code to handle multiple parameters);
        // We split by a pipe delimiter in order to allow multiple search terms.
		$terms = explode("|", Yii::$app->request->getQueryParam($param));
		// Trim whitespace from every item
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

    public static function prependURL($path, $version = self::DEFAULT_VERSION) {
	    //STOP!
        //To modify which server is targeted, change the constant "TARGET_API_SERVER" at the top of the class
        if(self::TARGET_API_SERVER == self::API_SERVER_PRODUCTION) {
            return "http://api.southerncrossinc.com/index.php?r=$version%2F$path";
        } else if(self::TARGET_API_SERVER == self::API_SERVER_STAGE) {
            return "http://apistage.southerncrossinc.com/index.php?r=$version%2F$path";
        } else if(self::TARGET_API_SERVER == self::API_SERVER_DEV) {
            return "http://apidev.southerncrossinc.com/index.php?r=$version%2F$path";
        } else if(self::TARGET_API_SERVER == self::API_SERVER_LOCALHOST) {
            return "http://localhost:9090/index.php?r=$version%2F$path";
        }
    }

    public static function urlPrefix()
    {
        $url = explode(".", $_SERVER['SERVER_NAME']);

        // if the servername contains the string local in the name ( localhost or apidev.local )
        // or it is in a local class of IPs
        if(YII_ENV_DEV && (strpos($_SERVER['SERVER_NAME'],'local')!==false
                ||  $_SERVER['SERVER_NAME'] === '0.0.0.0'
                || strpos($_SERVER['SERVER_NAME'],'192.168.')===0)
        )
        {
            $prefix = "apidev";
        }
        else {
            $prefix = $url[0];
        }

        return $prefix;
    }

    //function generates and executes a "GET" request and returns the response
	public static function executeGetRequest($url, $version = self::DEFAULT_VERSION)
	{
        $url = self::prependURL($url, $version);
		//set headers
		$headers = array(
			'X-Client:' . self::urlPrefix(),
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
//            $url = ['login/user-logout'];
//            Yii::$app->getResponse()->redirect($url)->send();
            throw new UnauthorizedHttpException(self::UNAUTH_MESSAGE);
		}
		else if($httpCode == 403) // Inadequate permissions.
		{
			throw new ForbiddenHttpException('You do not have adequate permissions to perform this action.');
		}
		curl_close ($curl);
		
		return $response;
	}
	
	//function generates and executes a "POST" request and returns the response
    public static function executePostRequest($url, $postData, $version = self::DEFAULT_VERSION)
	{
        $url = self::prependURL($url, $version);
		//set headers
		$headers = array(
			'X-Client:' . self::urlPrefix(),
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
//            $url = ['login/user-logout'];
//            Yii::$app->getResponse()->redirect($url)->send();
            throw new UnauthorizedHttpException("Please log in again. Your session has expired.");
		}
		else if($httpCode == 403) // Inadequate permissions.
		{
			throw new ForbiddenHttpException(self::UNAUTH_MESSAGE);
		}
		curl_close ($curl);
		
		return $response;
	}
	
	//function generates and executes a "PUT" request and returns the response
	public static function executePutRequest($url, $putData, $version = self::DEFAULT_VERSION)
	{
        $url = self::prependURL($url, $version);
		//set headers
		$headers = array(
			'X-Client:' . self::urlPrefix(),
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
//            $url = ['login/user-logout'];
//            Yii::$app->getResponse()->redirect($url)->send();
            throw new UnauthorizedHttpException("Please log in again. Your session has expired.");
		}
		else if($httpCode == 403) // Inadequate permissions.
		{
			throw new ForbiddenHttpException(self::UNAUTH_MESSAGE);
		}
		curl_close ($curl);
		
		return $response;
	}
	
	//function generates and executes a "Delete" request and returns the response
	public static function executeDeleteRequest($url, $version = self::DEFAULT_VERSION)
	{
        $url = self::prependURL($url, $version);
		//set headers
		$headers = array(
			'X-Client:' . self::urlPrefix(),
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
//            $url = ['login/user-logout'];
//            Yii::$app->getResponse()->redirect($url)->send();
            throw new UnauthorizedHttpException("Please log in again. Your session has expired.");
		}
		else if($httpCode == 403) // Inadequate permissions.
		{
			throw new ForbiddenHttpException(self::UNAUTH_MESSAGE);
		}
		curl_close ($curl);
		
		return $response;
	}

    // Universal Permission Check
    public static function requirePermission($permission) {
        //API RBAC permissions check
        //Will throw 403 if current user doesn't have permission
        // Modify this commented out code to work with your specific client
        // See PermissionDictionary.php
        /*
        if (PermissionDictionary::permissionIsPGE($permission)) {
            self::executeGetRequest("pge%2Fpge-permissions%2Fcheck-permission&permission=$permission");
        } else */ if (PermissionDictionary::permissionIsCT($permission)) {
            self::executeGetRequest("permissions%2Fcheck-permission&permission=$permission");
        } else {
            throw new ForbiddenHttpException();
        }
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

    /**
     * Get Nav menu Json
     * It will return nav menu in json format
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionGetNavMenu($id)
    {

        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();

            $navMenuUrl = "menu%2Fget&project=$id"; //scct"; //Switch for localhost
            //get nav menu by calling API route
            $mavMenuResponse = self::executeGetRequest($navMenuUrl); // indirect rbac

            //set up response data type
            Yii::$app->response->format = 'json';

            return ['navMenu' => $mavMenuResponse];
        }
    }
}
