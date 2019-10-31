<?php

namespace app\controllers;

use Yii;
use app\controllers\BaseController;
use yii\data\Pagination;
use yii\filters\VerbFilter;
use yii\data\ArrayDataProvider;
use linslin\yii2\curl;
use yii\web\Request;
use \DateTime;
use yii\base\Exception;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\ServerErrorHttpException;
use yii\web\UnauthorizedHttpException;
use yii\base\Model;
use yii\web\Response;
use app\constants\Constants;

/**
 * TimeCardController implements the CRUD actions for TimeCard model.
 */
class BaseCardController extends BaseController
{	
	const TYPE_TIME = 'time-card';
	const TYPE_MILEAGE = 'mileage-card';

    /**
     * Approve an existing TimeCard or MileageCard.
     * If approve is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     * @throws \yii\web\HttpException
     */
	public function actionApprove($id){
		try{
			//get requesting controller type
			$requestType = self::getRequestType();
			
			//guest redirect
			if(Yii::$app->user->isGuest){
				return $this->redirect(['/login']);
			}
		
			$cardIDArray[] = $id;
			$data = array(
				'cardIDArray' => $cardIDArray,
			);
			$json_data = json_encode($data);

			//post url
			$putUrl = $requestType.'%2Fapprove-cards';
			$putResponse = Parent::executePutRequest($putUrl, $json_data, Constants::API_VERSION_3); // indirect rbac
			$obj = json_decode($putResponse, true);
			
			//currently nothing is being done with response 4/5/19
			if($requestType == self::TYPE_TIME){
				return $obj[0]['TimeCardID'];
			}elseif($requestType == self::TYPE_MILEAGE){
				return $obj[0]['MileageCardID'];
			}
		} catch (UnauthorizedHttpException $e){
            Yii::$app->response->redirect(['login/index']);
        } catch(ForbiddenHttpException $e) {
            throw $e;
        } catch(ErrorException $e) {
            throw new \yii\web\HttpException(400);
        } catch(Exception $e) {
            throw new ServerErrorHttpException();
        }
	}

    /**
     * Approve Multiple existing TimeCards or MileageCards.
     * If approve is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     * @throws \yii\web\BadRequestHttpException
     * @throws \yii\web\HttpException
     * @internal param string $id
     *
     */
	public function actionApproveMultiple(){
		try{
			if (Yii::$app->request->isAjax) {
				//get requesting controller type
				$requestType = self::getRequestType();
				$data = Yii::$app->request->post();					
				// loop the data array to get all id's.	
				foreach ($data as $key) {
					foreach($key as $keyitem){
					   $cardIDArray[] = $keyitem;
					}
				}
				
				$data = array(
						'cardIDArray' => $cardIDArray,
					);		
				$json_data = json_encode($data);
				
				// post url
				$putUrl = $requestType.'%2Fapprove-cards';
				$putResponse = Parent::executePutRequest($putUrl, $json_data, Constants::API_VERSION_3); // indirect rbac
				//Handle API response if we want to do more robust error handling
			} else {
			  throw new \yii\web\BadRequestHttpException;
			}
		} catch (UnauthorizedHttpException $e){
			Yii::$app->response->redirect(['login/index']);
		} catch(ForbiddenHttpException $e) {
			throw $e;
		} catch(ErrorException $e) {
			throw new \yii\web\HttpException(400);
		} catch(Exception $e) {
			throw new ServerErrorHttpException();
		}
	}

	public function actionPMSubmit(){
		try{
			if (Yii::$app->request->isAjax) {
				//get requesting controller type
				$requestType = self::getRequestType();				
				$data = Yii::$app->request->post();			
				// set body data
				$body = array(
					'projectIDArray' => $data['projectIDArray'],
					'dateRangeArray' => $data['dateRangeArray'],
				);		
				$json_data = json_encode($body);
				$url = $requestType.'%2Fp-m-submit';
				$putResponse = Parent::executePutRequest($url, $json_data, Constants::API_VERSION_3); // indirect rbac
				//Handle API response if we want to do more robust error handling
			} else {
				throw new \yii\web\BadRequestHttpException;
			}
		} catch (UnauthorizedHttpException $e){
			Yii::$app->response->redirect(['login/index']);
		} catch(ForbiddenHttpException $e) {
			throw $e;
		} catch(ErrorException $e) {
			throw new \yii\web\HttpException(400);
		} catch(Exception $e) {
			throw new ServerErrorHttpException();
		}
	}
	
	public function actionPMResetRequest(){
		try{
			$requestType = self::getRequestType();
			$data = Yii::$app->request->post();	
			$body = array(
				'projectIDArray' => $data['projectIDArray'],
				'dateRangeArray' => $data['dateRangeArray'],
				'requestType' => $requestType,
			);		
			$jsonData = json_encode($body);
			
			//post url
			$postUrl = $requestType.'%2Fp-m-reset-request';
			$postResponse = Parent::executePostRequest($postUrl, $jsonData,Constants::API_VERSION_3); // indirect rbac
			$response = json_decode($postResponse, true);
			
			return $response['success'];
		} catch (UnauthorizedHttpException $e){
            Yii::$app->response->redirect(['login/index']);
        } catch(ForbiddenHttpException $e) {
            throw $e;
        } catch(ErrorException $e) {
            throw new \yii\web\HttpException(400);
        } catch(Exception $e) {
            throw new ServerErrorHttpException();
        }		
	}

    public function actionAccountantSubmit(){
        try{
			//get requesting controller type
			$requestType = self::getRequestType();	
        	$data = Yii::$app->request->post();	
			
			$response = [];
            $params['params'] = [
				'projectIDArray' => json_encode($data['projectIDs']),
				'startDate' => $data['weekStart'],
				'endDate' => $data['weekEnd']
			];
			$jsonBody = json_encode($params);
			
            $url = $requestType.'%2Faccountant-submit';
			
			$submitResponse = json_decode(Parent::executePutRequest($url, $jsonBody, Constants::API_VERSION_3), true);
			
			if($submitResponse['success'] == 1)
			{
				$response['success'] = TRUE; 
				$response['message'] = 'Successfully Completed Submission Process.'; 
				return json_encode($response);
			} else {
				$response['success'] = FALSE; 
                $response['message'] = 'Exception'; 
                return json_encode($response);
			}
		} catch (UnauthorizedHttpException $e){
            Yii::$app->response->redirect(['login/index']);
        } catch(ForbiddenHttpException $e) {
            throw $e;
        } catch (\Exception $e) {
            $response['success'] = FALSE; 
            $response['message'] = 'Exception occurred.'; 
			return json_encode($response);
        }
    }

	/**
	 * Execute API request to get status for submit button
	 * @param int $projectID id of currently selected project
	 * @param array $projectDropDown array of dropdown key value pairs
	 * @param string $startDate start of date range
	 * @param string $endDate end of date range
	 * @param boolean $isAccountant is current user of role type accountant
	 * returns boolean status for submit button
	 */
	protected static function getSubmitButtonStatus($projectID, $projectDropDown, $startDate, $endDate, $isAccountant)
	{
		//get requesting controller type
		$requestType = self::getRequestType();
		$projArray = array();
		$keys = array_keys($projectDropDown);
		$keysCount = count($keys);
		if($projectID != NULL){
			$projArray[0] = $projectID;
		}elseif($keysCount == 1) {
			$projArray[0] = $keys[0];
		}else{
			for($i=0;$i<$keysCount; $i++) {
				if($keys[$i] !== "") {
					$projArray[] = $keys[$i];
				}
			}
		}

		//build post body
		$submitCheckData['submitCheck'] = array(
			'ProjectName' => $projArray,
			'StartDate' => $startDate,
			'EndDate' => $endDate,
			'isAccountant' => $isAccountant
		);
		$json_data = json_encode($submitCheckData);
	
		//execute api request
		$url = $requestType.'%2Fcheck-submit-button-status';
		$response  = Parent::executePostRequest($url, $json_data, Constants::API_VERSION_3);
		$decodedResponse = json_decode($response, true);
		// get submit button status
		$readyStatus = $decodedResponse['SubmitReady'] == "1" ? true : false;
		return $readyStatus;
	}
	
	//get controller for requested action to determine type
	private function getRequestType(){
		$requestString = Yii::$app->requestedRoute;
		$requestArray = explode('/', $requestString);
		return $requestArray[0];
	}
}
