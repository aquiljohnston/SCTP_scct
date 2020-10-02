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
?>
<style type="text/css">
[data-key="0"] 
{
    display:none;
}
</style>

<div class="employee-detail-edit">

    <!-- <input id="SundayDate" type="hidden" name="SundayDate" value=<?php //echo $SundayDateFull; ?>>
    <input id="SaturdayDate" type="hidden" name="SaturdayDate" value=<?php //echo $SaturdayDateFull; ?>> -->
  
    <?php Pjax::begin(['id' => 'ShowTimeEntriesView', 'timeout' => false]) ?>
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
				// [
				// 	'label' => 'Date',
				// 	'attribute' => 'Date',
				// 	'headerOptions' => ['class' => 'text-center'],
				// 	'contentOptions' => ['class' => 'text-center'],
				// ],
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
                ]
                // ,
				// [
				// 	'label' => 'Total Time',
				// 	'attribute' => 'Total Time',
				// 	'headerOptions' => ['class' => 'text-center'],
				// 	'contentOptions' => ['class' => 'text-center'],
				// ]
			]
		]);
		?>
		
		<input type="hidden" value=<?php echo Yii::$app->getRequest()->getQueryParam('id') ?> name="userId" id="userId">
		<!-- <input type="hidden" value=<?php //echo $inOvertime ?> id="inOvertime"> -->
    <?php Pjax::end() ?>
    <?php
    Pjax::begin(['id' => 'showTime', 'timeout' => false]);
		Modal::begin([
			'header' => '<h4>ADD TASK</h4>',
			'id' => 'addTaskModal',
			'size' => 'modal-lg',
		]);
		// echo "<div id='modalAddTask'><span id='modalContentSpan'></span></div>";
		Modal::end();
    Pjax::end();
    ?>
</div>
