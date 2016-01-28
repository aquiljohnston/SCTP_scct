<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use app\controllers\TimeCard;
use yii\bootstrap\Modal;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $model app\models\time-card */

//$this->title = $model->TimeCardID;
$this->params['breadcrumbs'][] = ['label' => 'Time Cards', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="timecard-view">

    <h1><?= Html::encode($this->title) ?></h1>

   <p>
		<?= Html::a('Back', ['index'], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Update', ['update', 'id' => $model["TimeCardID"]], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model["TimeCardID"]], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'delete',
            ],
        ]) ?>
    </p>
    
	<!--Sunday TableView-->
	<h2 class="time_entry_header">Sunday</h2>
	<?php  
		Modal::begin([
				'header' => '<h4>Sunday</h4>',
				'id' => 'modal',
				'size' => 'modal-lg',
		]);
		
		echo "<div id='modalContent'></div>";
		
		Modal::end();
	?>
	<!--p>
		<?/*= Html::button('Edit', ['value'=>Url::to('index.php?r=time-entry/edit'), 'class' => 'btn btn-success', 'id' => 'modalButton'])*/?>
	</p-->
	<?php Pjax::begin(); ?>
	<?= GridView::widget([
		'dataProvider' => $SundayProvider,
		'columns' => [
			'TimeEntryStartTime',
			'TimeEntryEndTime',
			'TimeEntryDate',
			'TimeEntryComment',
			'TimeEntryCreateDate',
			'TimeEntryCreateBy',
			'TimeEntryModifiedDate',
			'TimeEntryModifiedBy',

			[   
				'class' => 'yii\grid\ActionColumn', 
				'template' => '{view} {update}',
				'headerOptions' => ['width' => '5%', 'class' => 'activity-view-link',],        
					'contentOptions' => ['class' => 'padding-left-5px'],

				'buttons' => [
					'view' => function ($url, $model, $key) {
						return Html::a('<span class="glyphicon glyphicon-eye-open"></span>','#', [
								'id' => 'activity-view-link',
								'title' => Yii::t('yii', 'View'),
								'data-toggle' => 'modal',
								'data-target' => '#activity-modal',
								'data-id' => $key,
								'data-pjax' => '0',

						]);
					},
				],
			],
			
		],
	]);	
	?>
	<?php Pjax::end();?>
	
	<?php

	Modal::begin([
		'header' => '<h4 class="modal-title">Create New</b></h4>',
		'toggleButton' => ['label' => 'Create New'],
		'footer' => '<a href="#" class="btn btn-primary" data-dismiss="modal">Close</a>',
	]);

	echo 'Say hello...';

	Modal::end();
	?>
	<br />     

	<?php $this->registerJs(
		"$('.activity-view-link').click(function() {
			$.get(
				'imgview',         
				{
					id: $(this).closest('tr').data('key')
				},
				function (data) {
					$('.modal-body').html(data);
					$('#activity-modal').modal();
				}  
			);
		});"
	); ?>
	
	<?php Modal::begin([
		'id' => 'activity-modal',
		'header' => '<h4 class="modal-title">View</h4>',
		'footer' => '<a href="#" class="btn btn-primary" data-dismiss="modal">Close</a>',

	]); ?>
	
	<?php Modal::end(); ?>
	
	<!--Monday TableView-->
	<h2 class="time_entry_header">Monday</h2>
	<?php  
		Modal::begin([
				'header' => '<h4>Sunday</h4>',
				'id' => 'modal',
				'size' => 'modal-lg',
		]);
		
		echo "<div id='modalContent'></div>";
		
		Modal::end();
	?>
	
	<?php Pjax::begin(); ?>
	<?= GridView::widget([
		'dataProvider' => $MondayProvider,
		'columns' => [
			'TimeEntryStartTime',
			'TimeEntryEndTime',
			'TimeEntryDate',
			'TimeEntryComment',
			'TimeEntryCreateDate',
			'TimeEntryCreateBy',
			'TimeEntryModifiedDate',
			'TimeEntryModifiedBy',
			
			[   
				'class' => 'yii\grid\ActionColumn', 
				'template' => '{view} {update}',
				'headerOptions' => ['width' => '5%', 'class' => 'activity-view-link',],        
					'contentOptions' => ['class' => 'padding-left-5px'],

				'buttons' => [
					'view' => function ($url, $model, $key) {
						return Html::a('<span class="glyphicon glyphicon-eye-open"></span>','apicall', [
								'id' => 'activity-view-link',
								'title' => Yii::t('yii', 'View'),
								'data-toggle' => 'modal',
								'data-target' => '#activity-modal',
								'data-id' => $key,
								'data-pjax' => '0',

						]);
					},
				],
			],
		],
	])?>
	
	<?php Pjax::end();?>
	
	<?php

	Modal::begin([
		'header' => '<h4 class="modal-title">Create New</b></h4>',
		'toggleButton' => ['label' => 'Create New'],
		'footer' => '<a href="#" class="btn btn-primary" data-dismiss="modal">Close</a>',
	]);

	echo 'Say hello...';

	Modal::end();
	?>
	<br />     

	<?php $this->registerJs(
		"$('.activity-view-link').click(function() {
			$.get(
				'imgview',         
				{
					id: $(this).closest('tr').data('key')
				},
				function (data) {
					$('.modal-body').html(data);
					$('#activity-modal').modal();
				}  
			);
		});"
	); ?>
	
	<?php Modal::begin([
		'id' => 'activity-modal',
		'header' => '<h4 class="modal-title">View</h4>',
		'footer' => '<a href="#" class="btn btn-primary" data-dismiss="modal">Close</a>',

	]); ?>
	<?php Modal::end(); ?>
	
	<!--Tuesday TableView-->
	<h2 class="time_entry_header">Tuesday</h2>
	<?= GridView::widget([
		'dataProvider' => $TuesdayProvider,
		'columns' => [
			'TimeEntryStartTime',
			'TimeEntryEndTime',
			'TimeEntryDate',
			'TimeEntryComment',
			'TimeEntryCreateDate',
			'TimeEntryCreateBy',
			'TimeEntryModifiedDate',
			'TimeEntryModifiedBy'
		]
	])?>
	
	<!--Wednesday TableView-->
	<h2 class="time_entry_header">Wednesday</h2>
	<?= GridView::widget([
		'dataProvider' => $WednesdayProvider,
		'columns' => [
			'TimeEntryStartTime',
			'TimeEntryEndTime',
			'TimeEntryDate',
			'TimeEntryComment',
			'TimeEntryCreateDate',
			'TimeEntryCreateBy',
			'TimeEntryModifiedDate',
			'TimeEntryModifiedBy'
		]
	])?>
	
	<!--Thursday TableView-->
	<h2 class="time_entry_header">Thursday</h2>
	<?= GridView::widget([
		'dataProvider' => $ThursdayProvider,
		'columns' => [
			'TimeEntryStartTime',
			'TimeEntryEndTime',
			'TimeEntryDate',
			'TimeEntryComment',
			'TimeEntryCreateDate',
			'TimeEntryCreateBy',
			'TimeEntryModifiedDate',
			'TimeEntryModifiedBy'
		]
	])?>
	
	<!--Friday TableView-->
	<h2 class="time_entry_header">Friday</h2>
	<?= GridView::widget([
		'dataProvider' => $FridayProvider,
		'columns' => [
			'TimeEntryStartTime',
			'TimeEntryEndTime',
			'TimeEntryDate',
			'TimeEntryComment',
			'TimeEntryCreateDate',
			'TimeEntryCreateBy',
			'TimeEntryModifiedDate',
			'TimeEntryModifiedBy'
		]
	])?>
	
	<!--Saturday TableView-->
	<h2 class="time_entry_header">Saturday</h2>
	<?= GridView::widget([
		'dataProvider' => $SaturdayProvider,
		'columns' => [
			'TimeEntryStartTime',
			'TimeEntryEndTime',
			'TimeEntryDate',
			'TimeEntryComment',
			'TimeEntryCreateDate',
			'TimeEntryCreateBy',
			'TimeEntryModifiedDate',
			'TimeEntryModifiedBy'
		]
	])?>

</div>
