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

	<?php 
			$approveUrl = urldecode(Url::to(['time-card/approve', 'id' => $model["TimeCardID"]]));
	?>
	<p>
		<?= Html::a('Back', ['index'], ['class' => 'btn btn-primary']) ?>
		<?= Html::a('Approve', $approveUrl, [
											 'class' => 'btn btn-primary', 
											 'data' => [
														'confirm' => 'Are you sure you want to approve this item?']
													])?>
	</p>

	<!--Sunday TableView-->
	<h2 class="time_entry_header">Sunday</h2>

	<?php Pjax::begin(['id'=>'SundayEntry']); ?>
	<?= GridView::widget([
		'dataProvider' => $SundayProvider,
		'columns' => [
			'TimeEntryStartTime',
			'TimeEntryEndTime',
			'TimeEntryDate',
			'TimeEntryComment',
			'TimeEntryCreateDate',
			'TimeEntryCreateBy',
			'TimeEntryHours',

			// [   
				// 'class' => 'yii\grid\ActionColumn', 
				// 'template' => '{view} {update}',
				// 'headerOptions' => ['width' => '5%', 'class' => 'activity-view-link',],        
					// 'contentOptions' => ['class' => 'padding-left-5px'],

				// 'buttons' => [
					// 'view' => function ($url, $model, $key) {
						// return Html::a('<span class="glyphicon glyphicon-eye-open"></span>','#', [
								// 'id' => 'activity-view-link',
								// 'title' => Yii::t('yii', 'View'),
								// 'data-toggle' => 'modal',
								// 'data-target' => '#activity-modal',
								// 'data-id' => $key,
								// 'data-pjax' => '0',

						// ]);
					// },
				// ],
			// ],
			
		],
	]);	
	?>
	<?php Pjax::end();?>
	
	<?php 
			$url = urldecode(Url::to(['time-card/createe', 'id' => $model["TimeCardID"]]));
	?>
	<p>
		<?= Html::button('Create New', ['value'=>$url, 'class' => 'btn btn-success', 'id' => 'modalButtonSunday']) ?>
		 
		<?php	if($Total_Hours_Sun != 0){ ?>
						<span class="totalhours"><?php echo "Total hours is : ".$Total_Hours_Sun?></span>
		<?php	}else{ ?>
						<span class="no_totalhours"></span>
		<?php   } ?> 
	</p>
	
	<?php
		Modal::begin([
			'header' => '<h4>Sunday</h4>',
			'id' => 'modalSunday',
			'size' => 'modal-lg',
		]);

		echo "<div id='modalContentSunday'></div>";

		Modal::end();
	?>
	<br />     

	<?php  

        // JS: Update response handling
        $this->registerJs(
			'jQuery(document).ready(function($){
				$(document).ready(function () {
					$("body").on("beforeSubmit", "form#SundayEntry", function () {
						var form = $(this);
						// return false if form still have some validation errors
						if (form.find(".has-error").length) {
							return false;
						}
						// submit form
						$.ajax({
							url    : form.attr("action"),
							type   : "post",
							data   : form.serialize(),
							success: function (response) {
								$("#modalSunday").modal("toggle");
								$.pjax.reload({container:"#SundayEntry"}); //for pjax update
							},
							error  : function () {
								console.log("internal server error");
							}
						});
						return false;
					});
				});
			});'
		); ?>
	
	<!--Monday TableView-->
	<h2 class="time_entry_header">Monday</h2>
	
	<?php Pjax::begin(['id'=>'MondayEntry']); ?>
	<?= GridView::widget([
		'dataProvider' => $MondayProvider,
		'columns' => [
			'TimeEntryStartTime',
			'TimeEntryEndTime',
			'TimeEntryDate',
			'TimeEntryComment',
			'TimeEntryCreateDate',
			'TimeEntryCreateBy',
			'TimeEntryHours',
		],
		'showFooter' => true,
	])?>
	
	<?php Pjax::end();?>
	
	<?php 
			$Monurl = urldecode(Url::to(['time-card/createe', 'id' => $model["TimeCardID"]]));
	?>
	<p>
		<?= Html::button('Create New', ['value'=>$Monurl, 'class' => 'btn btn-success', 'id' => 'modalButtonMonday']) ?>

		<?php	if($Total_Hours_Mon != 0){ ?>
						<span class="totalhours"><?php echo "Total hours is : ".$Total_Hours_Mon?></span>
		<?php	}else{ ?>
						<span class="no_totalhours"></span>
		<?php   } ?> 
	</p>
	
	<?php
		Modal::begin([
			'header' => '<h4>Monday</h4>',
			'id' => 'modalMonday',
			'size' => 'modal-lg',
		]);

		echo "<div id='modalContentMonday'></div>";

		Modal::end();
	?>
	<br />     

	<?php  

        // JS: Update response handling
        $this->registerJs(
			'jQuery(document).ready(function($){
				$(document).ready(function () {
					$("body").on("beforeSubmit", "form#MondayEntry", function () {
						var form = $(this);
						// return false if form still have some validation errors
						if (form.find(".has-error").length) {
							return false;
						}
						// submit form
						$.ajax({
							url    : form.attr("action"),
							type   : "post",
							data   : form.serialize(),
							success: function (response) {
								$("#modalMonday").modal("toggle");
								$.pjax.reload({container:"#MondayEntry"}); //for pjax update
							},
							error  : function () {
								console.log("internal server error");
							}
						});
						return false;
					});
				});
			});'
		); ?>
	
	<!--Tuesday TableView-->
	<h2 class="time_entry_header">Tuesday</h2>
	
	<?php Pjax::begin(['id'=>'TuesdayEntry']); ?>
	<?= GridView::widget([
		'dataProvider' => $TuesdayProvider,
		'columns' => [
			'TimeEntryStartTime',
			'TimeEntryEndTime',
			'TimeEntryDate',
			'TimeEntryComment',
			'TimeEntryCreateDate',
			'TimeEntryCreateBy',
			'TimeEntryHours',
		]
	])?>
	
	<?php Pjax::end();?>
	
	<?php 
			$Tueurl = urldecode(Url::to(['time-card/createe', 'id' => $model["TimeCardID"]]));
	?>
	<p>
		<?= Html::button('Create New', ['value'=>$Tueurl, 'class' => 'btn btn-success', 'id' => 'modalButtonTuesday']) ?>

		<?php	if($Total_Hours_Tue != 0){ ?>
						<span class="totalhours"><?php echo "Total hours is : ".$Total_Hours_Tue?></span>
		<?php	}else{ ?>
						<span class="no_totalhours"></span>
		<?php   } ?> 
	</p>
	
	<?php
		Modal::begin([
			'header' => '<h4>Tuesday</h4>',
			'id' => 'modalTuesday',
			'size' => 'modal-lg',
		]);

		echo "<div id='modalContentTuesday'></div>";

		Modal::end();
	?>
	<br />     

	<?php  

        // JS: Update response handling
        $this->registerJs(
			'jQuery(document).ready(function($){
				$(document).ready(function () {
					$("body").on("beforeSubmit", "form#TuesdayEntry", function () {
						var form = $(this);
						// return false if form still have some validation errors
						if (form.find(".has-error").length) {
							return false;
						}
						// submit form
						$.ajax({
							url    : form.attr("action"),
							type   : "post",
							data   : form.serialize(),
							success: function (response) {
								$("#modalTuesday").modal("toggle");
								$.pjax.reload({container:"#TuesdayEntry"}); //for pjax update
							},
							error  : function () {
								console.log("internal server error");
							}
						});
						return false;
					});
				});
			});'
		); ?>
	
	<!--Wednesday TableView-->
	<h2 class="time_entry_header">Wednesday</h2>
	
	<?php Pjax::begin(['id'=>'WednesdayEntry']); ?>
	<?= GridView::widget([
		'dataProvider' => $WednesdayProvider,
		'columns' => [
			'TimeEntryStartTime',
			'TimeEntryEndTime',
			'TimeEntryDate',
			'TimeEntryComment',
			'TimeEntryCreateDate',
			'TimeEntryCreateBy',
			'TimeEntryHours',
		]
	])?>
	
	<?php Pjax::end();?>
	
	<?php 
			$Wedurl = urldecode(Url::to(['time-card/createe', 'id' => $model["TimeCardID"]]));
	?>
	<p>
		<?= Html::button('Create New', ['value'=>$Wedurl, 'class' => 'btn btn-success', 'id' => 'modalButtonWednesday']) ?>
		
		<?php	if($Total_Hours_Wed != 0){ ?>
						<span class="totalhours"><?php echo "Total hours is : ".$Total_Hours_Wed?></span>
		<?php	}else{ ?>
						<span class="no_totalhours"></span>
		<?php   } ?> 
	</p>
	
	<?php
		Modal::begin([
			'header' => '<h4>Wednesday</h4>',
			'id' => 'modalWednesday',
			'size' => 'modal-lg',
		]);

		echo "<div id='modalContentWednesday'></div>";

		Modal::end();
	?>
	<br />     

	<?php  

        // JS: Update response handling
        $this->registerJs(
			'jQuery(document).ready(function($){
				$(document).ready(function () {
					$("body").on("beforeSubmit", "form#WednesdayEntry", function () {
						var form = $(this);
						// return false if form still have some validation errors
						if (form.find(".has-error").length) {
							return false;
						}
						// submit form
						$.ajax({
							url    : form.attr("action"),
							type   : "post",
							data   : form.serialize(),
							success: function (response) {
								$("#modalWednesday").modal("toggle");
								$.pjax.reload({container:"#WednesdayEntry"}); //for pjax update
							},
							error  : function () {
								console.log("internal server error");
							}
						});
						return false;
					});
				});
			});'
		); ?>
	
	<!--Thursday TableView-->
	<h2 class="time_entry_header">Thursday</h2>
	
	<?php Pjax::begin(['id'=>'ThursdayEntry']); ?>
	<?= GridView::widget([
		'dataProvider' => $ThursdayProvider,
		'columns' => [
			'TimeEntryStartTime',
			'TimeEntryEndTime',
			'TimeEntryDate',
			'TimeEntryComment',
			'TimeEntryCreateDate',
			'TimeEntryCreateBy',
			'TimeEntryHours',
		]
	])?>
	
	<?php Pjax::end();?>
	
	<?php 
			$Thururl = urldecode(Url::to(['time-card/createe', 'id' => $model["TimeCardID"]]));
	?>
	<p>
		<?= Html::button('Create New', ['value'=>$Thururl, 'class' => 'btn btn-success', 'id' => 'modalButtonThursday']) ?>
		
		<?php	if($Total_Hours_Thu != 0){ ?>
						<span class="totalhours"><?php echo "Total hours is : ".$Total_Hours_Thu?></span>
		<?php	}else{ ?>
						<span class="no_totalhours"></span>
		<?php   } ?>
	</p>
	
	<?php
		Modal::begin([
			'header' => '<h4>Thursday</h4>',
			'id' => 'modalThursday',
			'size' => 'modal-lg',
		]);

		echo "<div id='modalContentThursday'></div>";

		Modal::end();
	?>
	<br />     

	<?php  

        // JS: Update response handling
        $this->registerJs(
			'jQuery(document).ready(function($){
				$(document).ready(function () {
					$("body").on("beforeSubmit", "form#ThursdayEntry", function () {
						var form = $(this);
						// return false if form still have some validation errors
						if (form.find(".has-error").length) {
							return false;
						}
						// submit form
						$.ajax({
							url    : form.attr("action"),
							type   : "post",
							data   : form.serialize(),
							success: function (response) {
								$("#modalThursday").modal("toggle");
								$.pjax.reload({container:"#ThursdayEntry"}); //for pjax update
							},
							error  : function () {
								console.log("internal server error");
							}
						});
						return false;
					});
				});
			});'
	); ?>
	
	<!--Friday TableView-->
	<h2 class="time_entry_header">Friday</h2>
	
	<?php Pjax::begin(['id'=>'FridayEntry']); ?>
	<?= GridView::widget([
		'dataProvider' => $FridayProvider,
		'columns' => [
			'TimeEntryStartTime',
			'TimeEntryEndTime',
			'TimeEntryDate',
			'TimeEntryComment',
			'TimeEntryCreateDate',
			'TimeEntryCreateBy',
			'TimeEntryHours',
		]
	])?>
	
	<?php Pjax::end();?>
	
	<?php 
			$Friurl = urldecode(Url::to(['time-card/createe', 'id' => $model["TimeCardID"]]));
	?>
	<p>
		<?= Html::button('Create New', ['value'=>$Friurl, 'class' => 'btn btn-success', 'id' => 'modalButtonFriday']) ?>
		
		<?php	if($Total_Hours_Fri != 0){ ?>
						<span class="totalhours"><?php echo "Total hours is : ".$Total_Hours_Fri?></span>
		<?php	}else{ ?>
						<span class="no_totalhours"></span>
		<?php   } ?>
	</p>
	
	<?php
		Modal::begin([
			'header' => '<h4>Friday</h4>',
			'id' => 'modalFriday',
			'size' => 'modal-lg',
		]);

		echo "<div id='modalContentFriday'></div>";

		Modal::end();
	?>
	<br />     

	<?php  

        // JS: Update response handling
        $this->registerJs(
			'jQuery(document).ready(function($){
				$(document).ready(function () {
					$("body").on("beforeSubmit", "form#FridayEntry", function () {
						var form = $(this);
						// return false if form still have some validation errors
						if (form.find(".has-error").length) {
							return false;
						}
						// submit form
						$.ajax({
							url    : form.attr("action"),
							type   : "post",
							data   : form.serialize(),
							success: function (response) {
								$("#modalFriday").modal("toggle");
								$.pjax.reload({container:"#FridayEntry"}); //for pjax update
							},
							error  : function () {
								console.log("internal server error");
							}
						});
						return false;
					});
				});
			});'
		); ?>
	
	<!--Saturday TableView-->
	<h2 class="time_entry_header">Saturday</h2>
	
	<?php Pjax::begin(['id'=>'SaturdayEntry']); ?>
	<?= GridView::widget([
		'dataProvider' => $SaturdayProvider,
		'columns' => [
			'TimeEntryStartTime',
			'TimeEntryEndTime',
			'TimeEntryDate',
			'TimeEntryComment',
			'TimeEntryCreateDate',
			'TimeEntryCreateBy',
			'TimeEntryHours',
		]
	])?>
	
	<?php Pjax::end();?>
	
	<?php 
			$Saturl = urldecode(Url::to(['time-card/createe', 'id' => $model["TimeCardID"]]));
	?>
	<p>
		<?= Html::button('Create New', ['value'=>$Saturl, 'class' => 'btn btn-success', 'id' => 'modalButtonSaturday']) ?>
		
		<?php	if($Total_Hours_Sat != 0){ ?>
						<span class="totalhours"><?php echo "Total hours is : ".$Total_Hours_Sat?></span>
		<?php	}else{ ?>
						<span class="no_totalhours"></span>
		<?php   } ?>
	</p>
	
	<?php
		Modal::begin([
			'header' => '<h4>Saturday</h4>',
			'id' => 'modalSaturday',
			'size' => 'modal-lg',
		]);

		echo "<div id='modalContentSaturday'></div>";

		Modal::end();
	?>
	<br />     

	<?php  

        // JS: Update response handling
        $this->registerJs(
			'jQuery(document).ready(function($){
				$(document).ready(function () {
					$("body").on("beforeSubmit", "form#SaturdayEntry", function () {
						var form = $(this);
						// return false if form still have some validation errors
						if (form.find(".has-error").length) {
							return false;
						}
						// submit form
						$.ajax({
							url    : form.attr("action"),
							type   : "post",
							data   : form.serialize(),
							success: function (response) {
								$("#modalSaturday").modal("toggle");
								$.pjax.reload({container:"#SaturdayEntry"}); //for pjax update
							},
							error  : function () {
								console.log("internal server error");
							}
						});
						return false;
					});
				});
			});'
		); ?>

</div>
