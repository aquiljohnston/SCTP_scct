<?php

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\grid\GridView;
use yii\bootstrap\Modal;
use yii\widgets\Pjax;
use app\assets\ReportSummaryAsset;
use app\controllers\BaseController;

//register assets
ReportSummaryAsset::register($this);

/* @var $this yii\web\View */
?>
<style type="text/css">
/* [data-key="0"] 
 {
     display:none;
 } */
</style>

<div class="report-summary-employee-detail">  
    <div class="lightBlueBar">
		<h3></h3>
		<p>
			<?= Html::a('Back', ['index'], ['class' => 'btn btn-primary']) ?>
			<?php
				echo Html::button('Deactivate', [
					'class' => 'btn btn-primary',
					'disabled' => true,
					'id' => 'deactive_time_btn_id',
					'style' => ['margin' => '.2%']
				]);
				echo Html::button('Add Task', [
					'class' => 'btn btn-primary add_time_btn',
					'disabled' => true, //TODO determine when should be disabled(($isPMApproved || ($isApproved && !$isProjectManager)) && !$isAccountant),
					'id' => 'add_time_btn_id',
					'style' => ['margin' => '.2%']
				]);				
			?>
		</p>
		<br>
    </div>
	
    <?php Pjax::begin(['id' => 'EmployeeDetailView', 'timeout' => false]) ?>
		<h4> <?= 'Technician Name: ' . $totalData['Tech'] .' Weekly Total Hours ' . $totalData['WeeklyTotal']; ?></h4>
		<?= GridView::widget([
			'id' => 'DailyProjectHours',
			'dataProvider' => $projectDataProvider,
			'showHeader'=> false,
			'export' => false,
			'pjax' => true,
			'summary' => '',
			'caption' => '',
			'columns' => [
				[
					'label' => 'Label',
					'attribute' => 'Label'
				],[
					'label' => 'Value',
					'attribute' => 'Value'
				]
			]
		]);
		?>
		<br>
		<?= GridView::widget([
			'id' => 'DailyBreakdownHours',
			'dataProvider' => $breakdownDataProvider,
			'export' => false,
			'pjax' => true,
			'summary' => '',
			'caption' => '',
			'columns' => [
				[
					'label' => 'Project',
					'attribute' => 'Project'
				],[
					'label' => 'Task',
					'attribute' => 'Task'
				],[
					'label' => 'Start Time',
					'attribute' => 'Start Time',
				],[
					'label' => 'End Time',
					'attribute' => 'End Time',
				],[
					'label' => 'Time On Task',
					'attribute' => 'Time On Task',
				]
			]
		]);
		?>
		<h4><?= 'Total: ' . $totalData['Total'];?></h4>
		<h4><?= 'Total w/out lunch: ' . $totalData['TotalNoLunch']; ?></h4>
    <?php Pjax::end() ?>
    <!--<?php
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
    ?>-->
</div>
