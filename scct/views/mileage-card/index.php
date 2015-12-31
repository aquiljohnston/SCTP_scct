<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'MileageCard';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h3><?= Html::encode($this->title) ?></h3>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create MileageCard', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'MileageCardEmpID',
            'MileageCardTechID',
            'MileageCardProjectID',
            'MileageCardType',

            //['class' => 'yii\grid\ActionColumn'],
			
			['class' => 'yii\grid\ActionColumn',
                             /* 'buttons'=>[
                              'View' => function ($url, $model) { 
                                return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [
                                        'title' => Yii::t('yii', 'View'),
                                ]);                                
            
                              }
                          ], */
							  'urlCreator' => function ($action, $model, $key, $index) {
								  //var_dump($model["UserID"]);
											if ($action === 'view') {
											$url ='index.php?r=mileage-card%2Fview&id='.$model["MileageCardID"];
											return $url;
											}
											if ($action === 'update') {
											$url ='index.php?r=mileage-card%2Fupdate&id='.$model["MileageCardID"];
											return $url;
											}
											if ($action === 'delete') {
											$url ='index.php?r=mileage-card%2Fupdate&id='.$model["MileageCardID"];
											return $url;
											}
										}						  
                            ],
			],
    ]); ?>

</div>
