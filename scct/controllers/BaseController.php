<?php

namespace app\controllers;

use app\dictionaries\PermissionDictionary;
use Yii;
use app\models\user;
use app\models\UserSearch;
use yii\web\BadRequestHttpException;
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
	const DATE_FORMAT = 'Y-m-d H:i:s';

    //strings to be matched against url prefix, except prod which will be when no match occurs.
    const SERVER_LOCALHOST = 'local';
    const SERVER_DEV = 'dev';
    const SERVER_STAGE = 'stage';
	//prod has no additional distinguishing characters
    const SERVER_PRODUCTION = '';
	
	//api url for different environments
	const API_LOCAL_URL = 'http://localhost:8080/index.php?r=';
	const API_DEV_URL = 'http://apidev.southerncrossinc.com/index.php?r=';
	const API_STAGE_URL = 'http://apistage.southerncrossinc.com/index.php?r=';
	const API_PROD_URL = 'http://api.southerncrossinc.com/index.php?r=';

    // Legacy support. Version 1 calls do not specify which version to use, so we use a default value.
    const DEFAULT_VERSION = self::API_VERSION_1;

    const UNAUTH_MESSAGE = "Please log in again. Your session has expired. Redirecting...";

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
		$prefix = self::urlPrefix();
	    //check if url prefix contains api target
        if(strpos($prefix, self::SERVER_LOCALHOST) !== false) {
            return self::API_LOCAL_URL . "$version%2F$path";
		//checks for demo in dev check because name does not follow the standard convention
		} else if(strpos($prefix, self::SERVER_DEV) !== false || strpos($prefix, 'demo') !== false) {
            return self::API_DEV_URL . "$version%2F$path";
        } else if(strpos($prefix, self::SERVER_STAGE) !== false){
            return self::API_STAGE_URL . "$version%2F$path";
        } else {
			//if not distinguishing characters are present defaults to production
			return self::API_PROD_URL . "$version%2F$path";
        }
    }

	//pull url prefix to determine environment 
    public static function urlPrefix()
    {
        $url = explode(".", $_SERVER['SERVER_NAME']);
		return $url[0];
    }
	
	//get xclient value based on url prefix
	public static function getXClient()
    {
        //if the servername contains the string local in the name ( localhost or apidev.local )
        //or it is in a local class of IPs
        if(YII_ENV_DEV && (strpos($_SERVER['SERVER_NAME'],'local')!==false
                ||  $_SERVER['SERVER_NAME'] === '0.0.0.0'
                || strpos($_SERVER['SERVER_NAME'],'192.168.')===0)
        )
        {
            return "apidev";
        }
        else {
            return self::urlPrefix();
        }
    }

    //function generates and executes a "GET" request and returns the response
	public static function executeGetRequest($url, $version = self::DEFAULT_VERSION)
	{		
        $url = self::prependURL($url, $version);
		//set headers
		$headers = array(
			'X-Client:' . self::getXClient(),
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

    //function generates and executes a "GET" request and places the response in the stream/filepointer given by $fp
    public static function executeGetRequestToStream($url, $fp=null, $version = self::DEFAULT_VERSION, $token = null)
    {
        if($token === null) {
            $token = Yii::$app->session['token'];
        }
        if (null==$fp) {
            throw new Exception('Invalid file pointer');
        }

        $url = self::prependURL($url, $version);
        $prefix = self::getXClient();
        //set headers
        $headers = array(
            'X-Client:'.$prefix,
            'Accept:application/json',
            'Content-Type:application/json',
            'Authorization: Basic '. base64_encode($token.': ')
        );

        //init new curl
        $curl = curl_init();
        //set curl options
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_FILE, $fp);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($curl, CURLOPT_TIMEOUT, 180);
//        curl_setopt($curl, CURLOPT_WRITE, $fp);
        //execute curl
        curl_exec ($curl);
        //check authorization, logout and redirect to login if unauthorized
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        Yii::trace("HTTP CODE: $httpCode");
        if($httpCode == 401) // Not authenticated
        {
            //should be able to check response for error message at this point if we end up having more unauthorized cases
            $url = '/login/user-logout';
            Yii::$app->getResponse()->redirect($url)->send();
        }
        else if($httpCode == 403) // Inadequate permissions.
        {
            throw new ForbiddenHttpException('You do not have adequate permissions to perform this action.');
        }
        else if ($httpCode == 400) // Bad Request
        {
            throw new BadRequestHttpException('You sent an invalid request.');
        }
        else if ($httpCode === 0 || $httpCode === null) {
            throw new \Exception('Most probably the request timed out');
        }
        curl_close ($curl);
//        fclose($fp); if we were to close it here we would not be able to read the contents
    }
	
	//function generates and executes a "POST" request and returns the response
    public static function executePostRequest($url, $postData, $version = self::DEFAULT_VERSION)
	{
        $url = self::prependURL($url, $version);
		//set headers
		$headers = array(
			'X-Client:' . self::getXClient(),
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
		else if ($httpCode == 400){
            throw new BadRequestHttpException();
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
			'X-Client:' . self::getXClient(),
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
	public static function executeDeleteRequest($url, $putData, $version = self::DEFAULT_VERSION)
	{
        $url = self::prependURL($url, $version);
		//set headers
		$headers = array(
			'X-Client:' . self::getXClient(),
			'Accept:application/json',
			'Content-Type:application/json',
            'Content-Length: ' . strlen($putData),
			'Authorization: Basic '. base64_encode(Yii::$app->session['token'].': ')
			);
		//init new curl
		$curl = curl_init();
		//set curl options
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($curl, CURLOPT_POSTFIELDS,$putData);
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
            self::executeGetRequest("permissions%2Fcheck-permission&permission=$permission", self::API_VERSION_2);
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

            $navMenuUrl = "menu%2Fget";//Switch for localhost
            //get nav menu by calling API route
            $mavMenuResponse = self::executeGetRequest($navMenuUrl, self::API_VERSION_2); // indirect rbac

            Yii::trace("JSONRESPONSE:".json_encode($mavMenuResponse));
            //set up response data type
            Yii::$app->response->format = 'json';

            return ['navMenu' => $mavMenuResponse];
        }
    }
	
	//returns a date time in the const format
	public static function getDate()
	{
		return date(SELF::DATE_FORMAT);
	}
	
	//type: type of data the UID will be associated with such as User, breadcrumb, activty, etc.
	public static function generateUID($type)
	{
		//generate random number
		$random = rand(10000000, 99999999);
		
		//get current date time in format YmdHis
		$date = date("YmdHis");
		
		//concat values into string and return the resulting UID
		return "{$type}_{$random}_{$date}_WEB";
	}
	
	// guest default redirect action
	protected function isGuestUser() {
		if (Yii::$app->user->isGuest) {
			return $this->redirect(['/login']);
		}
	}
}
