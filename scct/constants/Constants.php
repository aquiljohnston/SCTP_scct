<?php 

namespace app\constants;

final class Constants
{
	// VERSION contains the string for the SCAPI version that one wishes to target.
    const API_VERSION_1 = "v1";
    const API_VERSION_2 = "v2";
    const API_VERSION_3 = "v3";
	const DATE_FORMAT = 'Y-m-d H:i:s';
	
	//strings to be matched against url prefix, except prod which will be when no match occurs.
    const SERVER_LOCALHOST = 'local';
    const SERVER_DEV = 'dev';
    const SERVER_STAGE = 'stage';
	//prod has no additional distinguishing characters
    const SERVER_PRODUCTION = '';
	
	//api url for different environments
	const WEB_WITH_LOCAL_API_PORT = 8888;
	const WEB_WITH_DEV_API_PORT = 8989;
	const API_LOCAL_URL = 'http://localhost:8000/index.php?r=';
	const API_DEV_URL = 'http://apidev.southerncrossinc.com/index.php?r=';
	const API_STAGE_URL = 'http://apistage.southerncrossinc.com/index.php?r=';
	const API_PROD_URL = 'http://azureapi.southerncrossinc.com/index.php?r=';
	
	const DEFAULT_VERSION = self::API_VERSION_2;

    const UNAUTH_MESSAGE = "Please log in again. Your session has expired. Redirecting...";

    const OASIS = "OASIS";
	const QUICKBOOKS = "QB";
	const ADP = "ADP";
 
	//const values to determine active week when redirecting from home screen
	const CURRENT_WEEK = 'Current Week';
	const PRIOR_WEEK = 'Prior Week';
	
	private function __construct()
	{
		throw new Exception("Can't get an instance of Constants.");
	}
}