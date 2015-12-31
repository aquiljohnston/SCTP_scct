<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Time Cards';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="time-card-index">

    <h3><?= Html::encode($this->title) ?></h3>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Time Card', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'TimeCardStartDate',
            'TimeCardEndDate',
            'TimeCardHoursWorked',
            'TimeCardApproved:datetime',
            'TimeCardSupervisorName',
            'TimeCardComment',
            'TimeCardCreateDate',

            ['class' => 'yii\grid\ActionColumn',
							/* 'buttons'=>[
                              'View' => function ($url, $model) { 
                                return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [
                                        'title' => Yii::t('yii', 'View'),
                                ]);                                
            
                              }
                          ], */
							  'urlCreator' => function ($action, $model, $key, $index) {
											if ($action === 'view') {
											$url ='index.php?r=time-card%2Fview&id='.$model["TimeCardID"];
											return $url;
											}
											if ($action === 'update') {
											$url ='index.php?r=time-card%2Fupdate&id='.$model["TimeCardID"];
											return $url;
											}
											if ($action === 'delete') {
											$url ='index.php?r=time-card%2Fdelete&id='.$model["TimeCardID"];
											return $url;
											}
										}
							],
        ],
    ]); ?>

</div>
