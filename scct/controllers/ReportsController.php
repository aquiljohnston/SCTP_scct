<?php

/**
 * Created by tzhang on 05/30/2016.
 */
namespace app\controllers;

use Yii;
use app\controllers\BaseController;
use app\models\Report;
use yii\base\Exception;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\Response;

/**
 * UserController implements the CRUD actions for user model.
 */
class ReportsController extends BaseController
{
    /**
     * Lists all user models.
     * @return mixed
     */
    public function actionIndex()
    {
		//guest redirect
		if (Yii::$app->user->isGuest)
		{
			return $this->redirect(['/login']);
		}
		
		//Check if user has permission to reports page
		self::requirePermission("viewReportsMenu");
		
		$model = new Report();
		return $this -> render('index', [
				'model' => $model,
			]
		);
	}

    /**
     * Get Report Drop Down List
     * @return mixed
     * @throws Exception
     */
    public function actionBuildDropDown(){

        // Reading the response from the the api and filling the report drop down
        $reportsUrl = 'reports%2Fget-report-drop-down';
        $reportsUrlListResponse = Parent::executeGetRequest($reportsUrl, self::API_VERSION_2); // indirect rbac
        $reportsList = $reportsUrlListResponse;//json_decode($reportsUrlListResponse, true);
        echo $reportsList;
    }
		 
    /**
     * Displays a single report.
     * @return mixed
     */
    public function actionView()
    {
        $data = self::executeGetRequest("reports%2Fget-stub", self::API_VERSION_2);
        $data = json_decode($data);
        return $this -> render('view', [
                'data' => $data
            ]
        );
    }

    /**
     * Get Reports
     * @return mixed
     * @throws Exception
     */
    public function actionGetReportsData(){
        try {

            // post url
            $url = 'reports%2Fget-report-export-data&reportType='.urlencode($_POST['ReportType']).'&reportName='.urlencode($_POST['ReportName']).'&reportID='.urlencode($_POST['Parm']).'&parm='.urlencode($_POST['ParmVar']).'&startDate='.urlencode($_POST['BeginDate']).'&endDate='.urlencode($_POST['EndDate']);
            Yii::trace("GetReportDataURL: ".$url);
            header('Content-Disposition: attachment; filename="report_'.date('Y-m-d_h_i_s').'.csv"');
            $this->requestAndOutputCsv($url);

        } catch(ForbiddenHttpException $e)
        {
            throw new ForbiddenHttpException;
        }
        catch(\Exception $e)
        {
            Yii::$app->runAction('login/user-logout');
        }
    }

    /**
     * Get Reports
     * @return mixed
     * @throws Exception
     */
    public function actionGetReports(){
        try {
            if ($_POST['ParmVar'] == ""){
                $ParmInspector = "none";
            }elseif($_POST['ParmVar'] == "null"){
                $ParmInspector = null;
            }else{
                $ParmInspector = $_POST['ParmVar'];
            }
            // post url
            $url = 'reports%2Fget-report&reportType='.urlencode($_POST['ReportType']).'&reportName='.urlencode($_POST['ReportName']).'&reportID='.urlencode($_POST['Parm']).'&ParmInspector='.urlencode($ParmInspector).'&startDate='.urlencode($_POST['BeginDate']).'&endDate='.urlencode($_POST['EndDate']);
            Yii::trace("reportUrl " . $url);
            $response = Parent::executeGetRequest($url, self::API_VERSION_2);
            Yii::trace("GetReportResponse " . $response);
            echo $response;

        } catch(ForbiddenHttpException $e)
        {
            throw new ForbiddenHttpException;
        }
        catch(\Exception $e)
        {
            Yii::$app->runAction('login/user-logout');
        }
    }

    /**
     * Export Report Table To Excel File
     * @param $url
     */
    public function requestAndOutputCsv($url){
        Yii::$app->response->format = Response::FORMAT_RAW;
        $fp = fopen('php://temp','w');
        header('Content-Type: text/csv;charset=UTF-8');
        header('Pragma: no-cache');
        header('Expires: 0');
        Parent::executeGetRequestToStream($url,$fp, self::API_VERSION_2);
        rewind($fp);
        echo stream_get_contents($fp);
        fclose($fp);
    }

    /**
     * Get Parm Drop Down
     * @return mixed
     * @throws Exception
     */
    public function actionGetParmDropDown(){
        if (isset($_POST['ViewName'])){
            // Reading the response from the the api and filling Parm Drop Down
            $getParmDropDownUrl = 'reports%2Fget-parm-dropdown&viewName='.urlencode($_POST['ViewName']);
            $getParmDropDownResponse = Parent::executeGetRequest($getParmDropDownUrl, self::API_VERSION_2); // indirect rbac
            Yii::trace("MAP GRID RESPONSE: ".$getParmDropDownResponse);
            $ParmDropDownList = $getParmDropDownResponse;//json_decode($reportsUrlListResponse, true);
            echo $ParmDropDownList;
        }else{
            echo "";
        }
    }

    /**
     * Get Inspector Drop Down
     * Use vUser view to pop inspector drop down list
     * @return mixed
     * @throws Exception
     */
    public function actionGetInspectorDropDown(){
        if (isset($_POST['Parm'])){
            // Reading the response from the the api and filling Inspector Drop Down
            $getInspectorDropDownUrl = 'reports%2Fget-inspector-dropdown&viewName=vUsers';
            $getInspectorDropDownResponse = Parent::executeGetRequest($getInspectorDropDownUrl, BaseController::API_VERSION_2); // indirect rbac
            $InspectorDropDownList = $getInspectorDropDownResponse;
            echo $InspectorDropDownList;
        }else{
            echo "";
        }
    }
}
