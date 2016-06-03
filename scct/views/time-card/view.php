<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use app\controllers\TimeCard;
use yii\bootstrap\Modal;
use yii\widgets\Pjax;
use \DateTime;
/* @var $this yii\web\View */
/* @var $model app\models\time-card */

//$this->title = $model->TimeCardID;
$this->params['breadcrumbs'][] = ['label' => 'Time Cards', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="timecard-view" approved = <?php echo $ApprovedFlag; ?>>

	<?php if($duplicateFlag == 1){?>
		<script>alert("The current Time Entry already exists, please try again.");</script>
	<?php }?>
		
    <h1><?= Html::encode($this->title) ?></h1>

	<?php 
			// check start date for this timecard.
			$approveUrl = urldecode(Url::to(['time-card/approve', 'id' => $model["TimeCardID"]]));
			//disactive TimeEntry
			//$disApproveUrl = urldecode(Url::to(['time-card/approve', 'id' => $model["TimeCardID"]]));
			if($model["TimeCardApprovedFlag"] === "Yes"){
				$approve_status = true;
			}else{
				$approve_status = false;
			}
	?>
	<p>
		<?= Html::a('Back', ['index'], ['class' => 'btn btn-primary']) ?>
		<?php if($model['TimeCardApprovedFlag']=='Yes' ||$Total_Hours_Current_TimeCard ==.0){ ?>
			<?= Html::button('Approve', [
												 'class' => 'btn btn-primary', 
												 'disabled' => true,
												 'id' => 'disable_single_approve_btn_id_timecard',
												 /*'data' => [
															'confirm' => 'Are you sure you want to approve this item?']*/
												])?>
			<?= Html::button('Deactivate', [
												 'class' => 'btn btn-primary', 
												 'disabled' => true,
												 'id' => 'deactive_timeEntry_btn_id',
												 /*'data' => [
															'confirm' => 'Are you sure you want to deactivate this item?']*/
												])?>									
		<?php }else{ ?>	
			<?= Html::a('Approve', $approveUrl, [
												 'class' => 'btn btn-primary', 
												 'disabled' => false,
												 'id' => 'enable_single_approve_btn_id_timecard',
												/* 'data' => [
															'confirm' => 'Are you sure you want to approve this item?']*/
												])?>
			<?= Html::button('Deactivate', [
												 'class' => 'btn btn-primary', 
												 'disabled' => false,
												 'id' => 'deactive_timeEntry_btn_id',
												/* 'data' => [
															'confirm' => 'Are you sure you want to deactivate this item?']*/
												])?>									
		<?php } ?>											
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
			'TimeEntryCreatedBy',
			'TimeEntryHours',
			'TimeEntryActiveFlag',
			
			[
				'class' => 'yii\grid\CheckboxColumn',
				'checkboxOptions' => function ($model, $key, $index, $column) {
					return ['timecardid' => $model["TimeEntryTimeCardID"], 'timeEntryID' => $model["TimeEntryID"], 'activeStatus' =>$model["TimeEntryActiveFlag"] ];
				}
				/*'pageSummary' => true,
                'rowSelectedClass' => GridView::TYPE_SUCCESS,
                'contentOptions'=>['style'=>'width: 0.5%'],*/
			],

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
			// get current TimeCard's Date
			$TimeCardStartDate = new DateTime($model["TimeCardStartDate"]);
			$SundayStr = $TimeCardStartDate->format('Y-m-d');
			
			$url = urldecode(Url::to(['time-card/createe', 'id' => $model["TimeCardID"], 'TimeCardTechID' => $model["TimeCardTechID"], 'TimeEntryDate' => $SundayStr ]));
	?>
	<p>
		<?= Html::button('Create New', ['value'=>$url, 'class' => 'btn btn-success', 'id' => 'modalButtonSunday', 'disabled' =>$approve_status]) ?>
		 
		<?php	if($Total_Hours_Sun > 0){ ?>
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
			'TimeEntryCreatedBy',
			'TimeEntryHours',
			'TimeEntryActiveFlag',
			
			[
				'class' => 'yii\grid\CheckboxColumn',
				'checkboxOptions' => function ($model, $key, $index, $column) {
					return ['timecardid' => $model["TimeEntryTimeCardID"], 'timeEntryID' => $model["TimeEntryID"], 'activeStatus' =>$model["TimeEntryActiveFlag"] ];
				}
				/*'pageSummary' => true,
                'rowSelectedClass' => GridView::TYPE_SUCCESS,
                'contentOptions'=>['style'=>'width: 0.5%'],*/
			],
		],
	])?>
	
	<?php Pjax::end();?>
	
	<?php 
			// get Monday's date for current TimeCard
			//$TimeCardStartDate = new DateTime($model["TimeCardStartDate"]);
			$MondayDate = $TimeCardStartDate->modify('+1 day');	
			$MondayStr = $MondayDate->format('Y-m-d');
			
			$Monurl = urldecode(Url::to(['time-card/createe', 'id' => $model["TimeCardID"], 'TimeCardTechID' => $model["TimeCardTechID"], 'TimeEntryDate' => $MondayStr ]));
	?>
	<p>
		<?= Html::button('Create New', ['value'=>$Monurl, 'class' => 'btn btn-success', 'id' => 'modalButtonMonday', 'disabled' =>$approve_status]) ?>

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
			'TimeEntryCreatedBy',
			'TimeEntryHours',
			'TimeEntryActiveFlag',
			
			[
				'class' => 'yii\grid\CheckboxColumn',
				'checkboxOptions' => function ($model, $key, $index, $column) {
					return ['timecardid' => $model["TimeEntryTimeCardID"], 'timeEntryID' => $model["TimeEntryID"], 'activeStatus' =>$model["TimeEntryActiveFlag"] ];
				}
				/*'pageSummary' => true,
                'rowSelectedClass' => GridView::TYPE_SUCCESS,
                'contentOptions'=>['style'=>'width: 0.5%'],*/
			],
		]
	])?>
	
	<?php Pjax::end();?>
	
	<?php 
			// get Tuesday's date for current TimeCard
			//$TimeCardStartDate = new DateTime($model["TimeCardStartDate"]);
			$TuesdayDate = $MondayDate->modify('+1 day');	
			$TuesdayStr = $TuesdayDate->format('Y-m-d');
			
			$Tueurl = urldecode(Url::to(['time-card/createe', 'id' => $model["TimeCardID"], 'TimeCardTechID' => $model["TimeCardTechID"], 'TimeEntryDate' => $TuesdayStr ]));
	?>
	<p>
		<?= Html::button('Create New', ['value'=>$Tueurl, 'class' => 'btn btn-success', 'id' => 'modalButtonTuesday', 'disabled' =>$approve_status]) ?>

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
			'TimeEntryCreatedBy',
			'TimeEntryHours',
			'TimeEntryActiveFlag',
			
			[
				'class' => 'yii\grid\CheckboxColumn',
				'checkboxOptions' => function ($model, $key, $index, $column) {
					return ['timecardid' => $model["TimeEntryTimeCardID"], 'timeEntryID' => $model["TimeEntryID"], 'activeStatus' =>$model["TimeEntryActiveFlag"] ];
				}
				/*'pageSummary' => true,
                'rowSelectedClass' => GridView::TYPE_SUCCESS,
                'contentOptions'=>['style'=>'width: 0.5%'],*/
			],
		]
	])?>
	
	<?php Pjax::end();?>
	
	<?php 
			// get Wednesday's date for current TimeCard
			//$TimeCardStartDate = new DateTime($model["TimeCardStartDate"]);
			$WednesdayDate = $TuesdayDate->modify('+1 day');	
			$WednesdayStr = $WednesdayDate->format('Y-m-d');
			
			$Wedurl = urldecode(Url::to(['time-card/createe', 'id' => $model["TimeCardID"], 'TimeCardTechID' => $model["TimeCardTechID"], 'TimeEntryDate' => $WednesdayStr ]));
	?>
	<p>
		<?= Html::button('Create New', ['value'=>$Wedurl, 'class' => 'btn btn-success', 'id' => 'modalButtonWednesday', 'disabled' =>$approve_status]) ?>
		
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
			'TimeEntryCreatedBy',
			'TimeEntryHours',
			'TimeEntryActiveFlag',
			
			[
				'class' => 'yii\grid\CheckboxColumn',
				'checkboxOptions' => function ($model, $key, $index, $column) {
					return ['timecardid' => $model["TimeEntryTimeCardID"], 'timeEntryID' => $model["TimeEntryID"], 'activeStatus' =>$model["TimeEntryActiveFlag"] ];
				}
				/*'pageSummary' => true,
                'rowSelectedClass' => GridView::TYPE_SUCCESS,
                'contentOptions'=>['style'=>'width: 0.5%'],*/
			],
		]
	])?>
	
	<?php Pjax::end();?>
	
	<?php 
			// get Thursday's date for current TimeCard
			//$TimeCardStartDate = new DateTime($model["TimeCardStartDate"]);
			$ThursdayDate = $WednesdayDate->modify('+1 day');	
			$ThursdayStr = $ThursdayDate->format('Y-m-d');
			
			$Thururl = urldecode(Url::to(['time-card/createe', 'id' => $model["TimeCardID"], 'TimeCardTechID' => $model["TimeCardTechID"], 'TimeEntryDate' => $ThursdayStr ]));
	?>
	<p>
		<?= Html::button('Create New', ['value'=>$Thururl, 'class' => 'btn btn-success', 'id' => 'modalButtonThursday', 'disabled' =>$approve_status]) ?>
		
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
			'TimeEntryCreatedBy',
			'TimeEntryHours',
			'TimeEntryActiveFlag',
			
			[
				'class' => 'yii\grid\CheckboxColumn',
				'checkboxOptions' => function ($model, $key, $index, $column) {
					return ['timecardid' => $model["TimeEntryTimeCardID"], 'timeEntryID' => $model["TimeEntryID"], 'activeStatus' =>$model["TimeEntryActiveFlag"] ];
				}
				/*'pageSummary' => true,
                'rowSelectedClass' => GridView::TYPE_SUCCESS,
                'contentOptions'=>['style'=>'width: 0.5%'],*/
			],
		]
	])?>
	
	<?php Pjax::end();?>
	
	<?php 
			// get Friday's date for current TimeCard
			//$TimeCardStartDate = new DateTime($model["TimeCardStartDate"]);
			$FridayDate = $ThursdayDate->modify('+1 day');	
			$FridayStr = $FridayDate->format('Y-m-d');
			
			$Friurl = urldecode(Url::to(['time-card/createe', 'id' => $model["TimeCardID"], 'TimeCardTechID' => $model["TimeCardTechID"], 'TimeEntryDate' => $FridayStr ]));
	?>
	<p>
		<?= Html::button('Create New', ['value'=>$Friurl, 'class' => 'btn btn-success', 'id' => 'modalButtonFriday', 'disabled' =>$approve_status]) ?>
		
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
			'TimeEntryCreatedBy',
			'TimeEntryHours',
			'TimeEntryActiveFlag',
			
			[
				'class' => 'yii\grid\CheckboxColumn',
				'checkboxOptions' => function ($model, $key, $index, $column) {
					return ['timecardid' => $model["TimeEntryTimeCardID"], 'timeEntryID' => $model["TimeEntryID"], 'activeStatus' =>$model["TimeEntryActiveFlag"] ];
				}
				/*'pageSummary' => true,
                'rowSelectedClass' => GridView::TYPE_SUCCESS,
                'contentOptions'=>['style'=>'width: 0.5%'],*/
			],
		]
	])?>
	
	<?php Pjax::end();?>
	
	<?php 
			// get Saturday's date for current TimeCard
			//$TimeCardStartDate = new DateTime($model["TimeCardStartDate"]);
			$SaturdayDate = $FridayDate->modify('+1 day');	
			$SaturdayStr = $SaturdayDate->format('Y-m-d');
			
			$Saturl = urldecode(Url::to(['time-card/createe', 'id' => $model["TimeCardID"], 'TimeCardTechID' => $model["TimeCardTechID"], 'TimeEntryDate' => $SaturdayStr ]));
	?>
	<p>
		<?= Html::button('Create New', ['value'=>$Saturl, 'class' => 'btn btn-success', 'id' => 'modalButtonSaturday', 'disabled' =>$approve_status]) ?>
		
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

</div>
