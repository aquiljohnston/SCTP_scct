<?php 

namespace app\constants;

final class Constants
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
	const API_LOCAL_URL = 'http://localhost:8888/index.php?r=';
	//const API_DEV_URL = 'http://localhost:8888/index.php?r=';
	const API_DEV_URL = 'http://apidev.southerncrossinc.com/index.php?r=';
	const API_STAGE_URL = 'http://apistage.southerncrossinc.com/index.php?r=';
	//const API_PROD_URL = 'http://api.southerncrossinc.com/index.php?r=';
	const API_PROD_URL = 'http://azureapi.southerncrossinc.com/index.php?r=';
	
	const DEFAULT_VERSION = self::API_VERSION_1;

    const UNAUTH_MESSAGE = "Please log in again. Your session has expired. Redirecting...";
    //PRODUCTION FTP CREDENTIALS
    const PROD_FTP_SERVER_ADDRESS = "sftp.southerncrossinc.com";
    const PROD_FTP_USERNAME = "ctfiletransfer";
    const PROD_FTP_PASSWORD = "lLiamiLENsCOPhAnceLLadwil";
    const PROD_DEFAULT_FTP_PATH = "C:\\Inetpub\\wwwroot\\";

    //DEV_FTP_CREDENTIALS
    const DEV_FTP_SERVER_ADDRESS = "10.100.10.10";
    const DEV_FTP_USERNAME = "ftpdev.southerncrosslighthouse.com|eigyan";
    const DEV_FTP_PASSWORD = "";
    //FTP_DEFAULT_PATH
    const DEV_DEFAULT_FTP_PATH = "C:\\Inetpub\\wwwroot\\";
 
	
	private function __construct()
	{
		throw new Exception("Can't get an instance of Constants.");
	}
}