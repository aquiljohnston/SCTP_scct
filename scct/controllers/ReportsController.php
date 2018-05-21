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
use app\constants\Constants;

/**
 * UserController implements the CRUD actions for user model.
 */
class ReportsController extends BaseController
{
    /**
     * Lists all user models.
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws Exception
     */
    public function actionIndex()
    {
        try {
            //guest redirect
            if (Yii::$app->user->isGuest) {
                return $this->redirect(['/login']);
            }

            //Check if user has permission to reports page
            self::requirePermission("viewReportsMenu");

            $model = new Report();
            return $this->render('index', [
                    'model' => $model
                ]
            );
        } catch (ForbiddenHttpException $e) {
            Yii::$app->response->redirect(['login/index']);
        } catch (UnauthorizedHttpException $e){
            Yii::$app->response->redirect(['login/index']);
        }
	}
		 
    /**
     * Displays a single report.
     * @return mixed
     */
    public function actionView()
    {
        $data = self::executeGetRequest("reports%2Fget-stub", Constants::API_VERSION_2);
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
     * Export Report Table To Excel File
     * @param $url
     */
    public function requestAndOutputCsv($url){
        Yii::$app->response->format = Response::FORMAT_RAW;
        $fp = fopen('php://temp','w');
        header('Content-Type: text/csv;charset=UTF-8');
        header('Pragma: no-cache');
        header('Expires: 0');
        Parent::executeGetRequestToStream($url,$fp, Constants::API_VERSION_2);
        rewind($fp);
        echo stream_get_contents($fp);
        fclose($fp);
    }

    /**
     * Get Dropdowns returns all reports and project in
     * the both tables. No filters are used.
     * @return JSON
     */
    public function actionGetDropdownsData(){
        $dropdownsURL = 'reports%2Fget-report-drop-down';
        $response["dropdowns"] = json_decode(Parent::executeGetRequest($dropdownsURL, Constants::API_VERSION_2)); // indirect rbac         
        // $projectsURL = 'project%2Fget-project-dropdowns';
        // $response["projects"] = json_decode(Parent::executeGetRequest($projectsURL, Constants::API_VERSION_2)); // indirect rbac
        return json_encode($response, true);
    }
    
    /**
     * Get Report returns report data with the given parameters
     * @param ReportName {String} report name, ReportType {String} type of report view or sp, 
     *        StartDate {Date} report start, EndDate {Date} report end, Project {String} project id 
     * @return JSON
     * @throws Exception
     */
    public function actionGetReport(){
        try {
            $response = "{}";
            if (isset($_POST['ReportName']) && isset($_POST['ReportType'])) {
                // Todo: move URL to constants
                $url = 'reports%2Fget-report&'. http_build_query([
                    "reportType" => $_POST['ReportType'],
                    "reportName" => $_POST['ReportName'],
                    "startDate" => $_POST['StartDate'],
                    "endDate" => $_POST['EndDate'],
                    "ParmInspector" => $_POST['Project'],
                    "parm" => $_POST['Mapgrid']
                ]);
                $response = Parent::executeGetRequest($url, Constants::API_VERSION_2);
            } 
            echo $response;
        } catch(ForbiddenHttpException $e) {
            throw new ForbiddenHttpException;
        } catch(\Exception $e) {
            Yii::$app->runAction('login/user-logout');
        }
    }
}
