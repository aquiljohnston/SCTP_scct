<?php

namespace app\controllers;

use Yii;
use app\models\TimeCard;
use app\models\TimeCardSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\grid\GridView;
use yii\data\ArrayDataProvider;
use linslin\yii2\curl;
use yii\web\Request;

/**
 * TimeCardController implements the CRUD actions for TimeCard model.
 */
class TimeCardController extends Controller
{
	private $model;
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all TimeCard models.
     * @return mixed
     */
    public function actionIndex()
    {
		// create curl for restful call.
		$curl = new curl\Curl();
		
		// get response from api 
		$response = $curl->get('http://api.southerncrossinc.com/index.php?r=time-card%2Findex');
		
        //$searchModel = new TimeCardSearch();
        // passing decode data into dataProvider
		$dataProvider = new ArrayDataProvider([
		'allModels' => json_decode($response, true),]);

		// fill gridview by applying data provider
		GridView::widget([
			'dataProvider' => $dataProvider,
			]);
			
		//calling index page to pass dataProvider.
        return $this->render('index', [
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * Displays a single TimeCard model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
		$curl = new curl\Curl();
		//var_dump($id);
		//get response
		$response = $curl->get('http://api.southerncrossinc.com/index.php?r=time-card%2Fview&id='.$id);
		//var_dump($response);
		return $this -> render('view', ['model' => json_decode($response, true)]);
		
		// return $this->render('view', [
            // 'model' => $this->findModel($id),
        // ]);
    }

    /**
     * Creates a new TimeCard model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new \yii\base\DynamicModel([
			'TimeCardStartDate', 'TimeCardEndDate', 'TimeCardCreateDate', 'TimeCardModifiedDate', 'TimeCardHoursWorked', 'TimeCardProjectID', 
			'TimeCardTechID', 'TimeCardApproved', 'TimeCardSupervisorName', 'TimeCardComment', 'TimeCardCreatedBy', 'TimeCardModifiedBy', 'isNewRecord'
		]);
		
		$model->addRule('TimeCardStartDate', 'safe')
			  ->addRule('TimeCardEndDate', 'safe')
			  ->addRule('TimeCardCreateDate', 'safe')
			  ->addRule('TimeCardModifiedDate', 'safe')
			  ->addRule('TimeCardHoursWorked', 'number')
			  ->addRule('TimeCardProjectID', 'integer')
			  ->addRule('TimeCardTechID', 'integer')
			  ->addRule('TimeCardApproved', 'integer')
			  ->addRule('TimeCardSupervisorName', 'string')
			  ->addRule('TimeCardComment', 'string')
			  ->addRule('TimeCardCreatedBy', 'string')
			  ->addRule('TimeCardModifiedBy', 'string');
		
		// create curl object
		$curl = new curl\Curl();
		
		// post url
		$url_send = "http://api.southerncrossinc.com/index.php?r=time-card%2Fcreate";
		
        if ($model->load(Yii::$app->request->post())) {
			
			$data = array(
				'TimeCardStartDate' => $model->TimeCardStartDate,
				'TimeCardEndDate' => $model->TimeCardEndDate,
				'TimeCardHoursWorked' => $model->TimeCardHoursWorked,
				'TimeCardProjectID' => $model->TimeCardProjectID,
				'TimeCardTechID' => $model->TimeCardTechID,
				'TimeCardApproved' => $model->TimeCardApproved,
				'TimeCardSupervisorName' => $model->TimeCardSupervisorName,
				'TimeCardComment' => $model->TimeCardComment,
				'TimeCardCreateDate' => $model->TimeCardCreateDate,
				'TimeCardCreatedBy' => $model->TimeCardCreatedBy,
				'TimeCardModifiedDate' => $model->TimeCardModifiedDate,
				'TimeCardModifiedBy' => $model->TimeCardModifiedBy,
			);
			
			$json_data = json_encode($data);		
			$ch = curl_init($url_send);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"POST");
			curl_setopt($ch, CURLOPT_POSTFIELDS,$json_data);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Content-Type: application/json',
				'Content-Length: ' . strlen($json_data))
			);			
			$result = curl_exec($ch);
			curl_close($ch);
			//var_dump($result);
			$obj = (array)json_decode($result);
			//var_dump($obj["TimeCardID"]);
            return $this->redirect(['view', 'id' => $obj["TimeCardID"]]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing TimeCard model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->TimeCardID]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing TimeCard model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the TimeCard model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return TimeCard the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TimeCard::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
