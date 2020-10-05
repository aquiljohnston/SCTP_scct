<?php

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\grid\GridView;
use yii\bootstrap\Modal;
use yii\widgets\Pjax;
use app\assets\EmployeeApprovalAsset;
use app\controllers\BaseController;

//register assets
EmployeeApprovalAsset::register($this);

/* @var $this yii\web\View */
$this->title = 'Employee Detail';

?>
<style type="text/css">
/* [data-key="0"] 
 {
     display:none;
 } */
</style>

<div class="report-summary-employee-detail index-div">  
    <div class="lightBlueBar" style="height: 60px; padding: 10px;">
		<p>
			<?= Html::a('Back', ['index'], ['class' => 'btn btn-primary']) ?>
			<?php
				if(!$canAddTask) {
					echo Html::button('Add Task', [
						'class' => 'btn btn-primary add_time_btn',
						'disabled' => true, 
						'id' => 'add_task_btn_id',
						'style' => ['margin' => '.2%']
					]);
				} else {
					echo Html::button('Add Task', [
						'class' => 'btn btn-primary add_time_btn',
						'id' => 'add_task_btn_id',
						'style' => ['margin' => '.2%']
					]);
				}			
			?>
		</p>
		<br>
    </div>
	
    <?php Pjax::begin(['id' => 'EmployeeDetailView', 'timeout' => false]) ?>
		<div class="project-hours-container">
			<h4><span><?= 'Technician Name: ' . $totalData['Tech']?></span><span style="float:right"><?=' Weekly Total Hours: ' . $totalData['WeeklyTotal']; ?></span></h4>
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
						'attribute' => 'Label',
						'contentOptions' => ['class' => 'text-left'],
					],[
						'label' => 'Value',
						'attribute' => 'Value',
						'contentOptions' => ['class' => 'text-center'],
					]
				]
			]);
			?>
		</div>
		<?= GridView::widget([
			'id' => 'DailyBreakdownHours',
			'dataProvider' => $breakdownDataProvider,
			'export' => false,
			'pjax' => true,
			'summary' => '',
			'caption' => '',
			'columns' => [
				[
					'label' => 'RowID',
					'attribute' => 'RowID',
					'hidden' => true
				],[
					'label' => 'ProjectID',
					'attribute' => 'ProjectID',
					'hidden' => true
				],[
					'label' => 'Project',
					'attribute' => 'Project',
					'headerOptions' => ['class' => 'text-left'],
					'contentOptions' => ['class' => 'text-left'],
				],[
					'label' => 'Task',
					'attribute' => 'Task',
					'headerOptions' => ['class' => 'text-left'],
					'contentOptions' => ['class' => 'text-left'],
				],[
					'label' => 'Start Time',
					'attribute' => 'Start Time',
					'headerOptions' => ['class' => 'text-center'],
					'contentOptions' => ['class' => 'text-center'],
				],[
					'label' => 'End Time',
					'attribute' => 'End Time',
					'headerOptions' => ['class' => 'text-center'],
					'contentOptions' => ['class' => 'text-center'],
				],[
					'label' => 'Time On Task',
					'attribute' => 'Time On Task',
					'headerOptions' => ['class' => 'text-center'],
					'contentOptions' => ['class' => 'text-center'],
				]
			]
		]);
		?>
		<p style="float:right; text-align:right;">
			<?= $totalData['Total'];?>
			<br>
			<?= $totalData['TotalNoLunch']; ?>
		</p>
		<p style="float:right; text-align:right; font-weight:bold; margin-right:5px;">
			Total:
			<br>
			Total w/out lunch:
		</p>
    <?php Pjax::end() ?>
    <?php
    Pjax::begin(['id' => 'editTime', 'timeout' => false]);
		Modal::begin([
			'header' => '<h4>Edit Time</h4>',
			'id' => 'editTimeModal',
			'size' => 'modal-lg',
		]);
		echo "<div id='editTimeModal'><span id='editTimeModalContentSpan'></span></div>";
		Modal::end();
    Pjax::end();
    ?>

    <?php
    // add task
    Pjax::begin(['id' => 'addTask', 'timeout' => false]);
    Modal::begin([
        'header' => '<h4>Add Task</h4>',
        'id' => 'addTaskModal',
        'size' => 'modal-lg',
    ]);
    echo "<div id='addTaskModal'><span id='addTaskModalContentSpan'></span></div>";
    Modal::end();
    Pjax::end();
    ?>
</div>
<input type="hidden" value="<?php echo $userID?>" id="userID">
<input type="hidden" value="<?php echo @$_GET['date']; ?>" id="timeCardDate">
