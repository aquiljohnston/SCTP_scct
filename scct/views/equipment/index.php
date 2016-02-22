<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\controllers\Equipment;

/* @var $this yii\web\View */
/* @var $searchModel app\models\EquipmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Equipment Management';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="equipment-index">

    <h1 class="title"><?= Html::encode($this->title) ?></h3>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Equipment', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

			//'EquipmentID',
            'Name',
            'Serial Number',
            'Details',
            'Type',
			'Client Name',
			'Project Name',
			'Accepted Flag',

            ['class' => 'yii\grid\ActionColumn',
			
			'urlCreator' => function ($action, $model, $key, $index) {
											if ($action === 'view') {
											$url ='index.php?r=equipment%2Fview&id='.$model["EquipmentID"];
											return $url;
											}
											if ($action === 'update') {
											$url ='index.php?r=equipment%2Fupdate&id='.$model["EquipmentID"];
											return $url;
											}
											if ($action === 'delete') {
											$url ='index.php?r=equipment%2Fdelete&id='.$model["EquipmentID"];
											return $url;
											}
										},
										'buttons' => [
											'delete' => function ($url, $model, $key) {
												$url ='/index.php?r=equipment%2Fdelete&id='.$model["EquipmentID"];       
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
