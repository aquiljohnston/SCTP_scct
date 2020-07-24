<?php

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\grid\GridView;
use yii\bootstrap\Modal;
use yii\widgets\Pjax;
use app\assets\TimeCardAsset;
use app\controllers\BaseController;

//register assets
TimeCardAsset::register($this);

/* @var $this yii\web\View */
/* @var $model app\models\time-card */
?>
<style type="text/css">
[data-key="0"] 
{
    display:none;
}
</style>

<div class="time-card-entries">

    <input id="SundayDate" type="hidden" name="SundayDate" value=<?php echo $SundayDateFull; ?>>
    <input id="SaturdayDate" type="hidden" name="SaturdayDate" value=<?php echo $SaturdayDateFull; ?>>
    <input id="TimeCardProjectID" type="hidden" name="TimeCardProjectID" value=<?php echo $timeCardProjectID; ?>>
  
    <div class="lightBlueBar">
		<h3> <?= $projectName.' Week '.$from.' - '.$to.': '.$lName.', '.$fName; ?></h3>
		<p>
			<?= Html::a('Back', ['index'], ['class' => 'btn btn-primary']) ?>
			<?php
				// if(in_array($timeCardProjectID, BaseController::getTestProjects())){
					if($canApprove){
						echo Html::button('Approve', [
							'class' => 'btn btn-primary',
							'disabled' => $isApproved || $isAccountant,
							'id' => 'approve_timeCard_btn_id',
							'style' => ['margin' => '.2%']
						]);
					}
					echo Html::button('Deactivate', [
						'class' => 'btn btn-primary',
						'disabled' => true,
						'id' => 'deactive_timeEntry_btn_id',
						'style' => ['margin' => '.2%']
					]);
					echo Html::button('Add Task', [
						'class' => 'btn btn-primary add_task_btn',
						'disabled' => (($isPMApproved || ($isApproved && !$isProjectManager)) && !$isAccountant),
						'id' => 'add_task_btn_id',
						'style' => ['margin' => '.2%']
					]);
				// }				
			?>
		</p>
		<br>
    </div>
    <?php Pjax::begin(['id' => 'ShowTimeEntriesView', 'timeout' => false]) ?>
		<h3>Time</h3>
		<?= GridView::widget([
			'id' => 'allTaskEntries',
			'dataProvider' => $task,
			'export' => false,
			'pjax' => true,
			'summary' => '',
			'caption' => '',
			'columns' => [
				[
					'label' => 'Sunday ' . $SundayDate,
					'attribute' => 'Date1',
					'headerOptions' => ['class'=>$SundayDateFull]
				],[
					'label' => 'Monday '. $MondayDate,
					'attribute' => 'Date2',
					'headerOptions' => ['class'=>$MondayDateFull],
				],[
					'label' => 'Tuesday '. $TuesdayDate,
					'attribute' => 'Date3',
					'headerOptions' => ['class'=>$TuesdayDateFull],
				],[
					'label' => 'Wednesday '. $WednesdayDate,
					'attribute' => 'Date4',
					'headerOptions' => ['class'=>$WednesdayDateFull],
				],[
					'label' => 'Thursday '. $ThursdayDate,
					'attribute' => 'Date5',
					'headerOptions' => ['class'=>$ThursdayDateFull],
				],[
					'label' => 'Friday '. $FridayDate,
					'attribute' => 'Date6',
					'headerOptions' => ['class'=>$FridayDateFull],
				],[
					'label' => 'Saturday '. $SaturdayDate,
					'attribute' => 'Date7',
					'headerOptions' => ['class'=>$SaturdayDateFull],
				],[
					'label' => 'Total',
					'attribute' => 'Total',
				]
			]
		]);
		?>
		<br>
		<!--<h3>Miscellaneous</h3>-->
		<h3>Lunch</h3>
		<?= GridView::widget([
			'id' => 'allLunchEntries',
			'dataProvider' => $lunch,
			'export' => false,
			'pjax' => true,
			'summary' => '',
			'caption' => '',
			'columns' => [
			//for future use if other types of activities are added
				// [
					// 'label' => 'Type',
					// 'attribute' => 'ActivityTitle',
				// ],
				[
					'label' => 'Sunday ' . $SundayDate,
					'attribute' => 'Date1',
					'headerOptions' => ['class'=>$SundayDateFull]
				],[
					'label' => 'Monday '. $MondayDate,
					'attribute' => 'Date2',
					'headerOptions' => ['class'=>$MondayDateFull],
				],[
					'label' => 'Tuesday '. $TuesdayDate,
					'attribute' => 'Date3',
					'headerOptions' => ['class'=>$TuesdayDateFull],
				],[
					'label' => 'Wednesday '. $WednesdayDate,
					'attribute' => 'Date4',
					'headerOptions' => ['class'=>$WednesdayDateFull],
				],[
					'label' => 'Thursday '. $ThursdayDate,
					'attribute' => 'Date5',
					'headerOptions' => ['class'=>$ThursdayDateFull],
				],[
					'label' => 'Friday '. $FridayDate,
					'attribute' => 'Date6',
					'headerOptions' => ['class'=>$FridayDateFull],
				],[
					'label' => 'Saturday '. $SaturdayDate,
					'attribute' => 'Date7',
					'headerOptions' => ['class'=>$SaturdayDateFull],
				],[
					'label' => 'Total',
					'attribute' => 'Total'
				]
			]
		]);
		?>
		<br>
		<h3>Hours Overview</h3>
		<?= GridView::widget([
			'id' => 'hoursOverviewEntries',
			'dataProvider' => $hoursOverview,
			'export' => false,
			'bootstrap' => false,
			'pjax' => true,
			'summary' => '',
			'caption' => '',
			'emptyText' => 'No entries exist for this week.',
			'columns' => [
				[
					'label' => 'Task',
					'attribute' => 'Task',
					'headerOptions' => ['class' => 'text-center'],
					'contentOptions' => ['class' => 'text-center'],
				],
				[
					'label' => 'Date',
					'attribute' => 'Date',
					'headerOptions' => ['class' => 'text-center'],
					'contentOptions' => ['class' => 'text-center'],
				],
				[
					'label' => 'Start Time',
					'attribute' => 'Start Time',
					'headerOptions' => ['class' => 'text-center'],
					'contentOptions' => ['class' => 'text-center'],
				],
				[
					'label' => 'End Time',
					'attribute' => 'End Time',
					'headerOptions' => ['class' => 'text-center'],
					'contentOptions' => ['class' => 'text-center'],
				],
				[
					'label' => 'Total Time',
					'attribute' => 'Total Time',
					'headerOptions' => ['class' => 'text-center'],
					'contentOptions' => ['class' => 'text-center'],
				]
			]
		]);
		?>
		
		<input type="hidden" value=<?php echo Yii::$app->getRequest()->getQueryParam('id') ?> name="timeCardId" id="timeCardId">
		<input type="hidden" value=<?php echo $isProjectManager ?> id="isProjectManager">
		<input type="hidden" value=<?php echo $isAccountant ?> id="isAccountant">
		<input type="hidden" value=<?php echo $isApproved ?> id="isApproved">
		<input type="hidden" value=<?php echo $isPMApproved ?> id="isPMApproved">
		<input type="hidden" value=<?php echo $isSubmitted ?> id="isSubmitted">
		<input type="hidden" value=<?php echo $inOvertime ?> id="inOvertime">
    <?php Pjax::end() ?>
    <?php
    Pjax::begin(['id' => 'showTime', 'timeout' => false]);
		Modal::begin([
			'header' => '<h4>ADD TASK</h4>',
			'id' => 'addTaskModal',
			'size' => 'modal-lg',
		]);
		echo "<div id='modalAddTask'><span id='modalContentSpan'></span></div>";
		Modal::end();
    Pjax::end();
    ?>
	<?php
    Pjax::begin(['id' => 'timeReason', 'timeout' => false]);
		Modal::begin([
			'header' => '<h4>DEACTIVATE REASON</h4>',
			'id' => 'timeReasonModal',
			//no size defaults to medium
		]);
		echo "<div id='modalTimeReason'><span id='timeReasonModalContentSpan'></span></div>";
		Modal::end();
	Pjax::end();
    ?>
</div>
