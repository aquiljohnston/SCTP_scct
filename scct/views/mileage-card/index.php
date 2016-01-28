<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\controllers\MileageCard;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'MileageCard';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h3><?= Html::encode($this->title) ?></h3>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p class="white_space">
        
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
										},
										'buttons' => [
											'delete' => function ($url, $model, $key) {
												$url ='/index.php?r=mileage-card%2Fdelete&id='.$model["MileageCardID"];       
													$options = [
													'title' => Yii::t('yii', 'Delete'),
													'aria-label' => Yii::t('yii', 'Delete'),
													'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
													'data-method' => 'Delete',
													'data-pjax' => '0',
													];
													return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, $options);
											},
										]											
                            ],
			],
    ]); ?>

</div>
