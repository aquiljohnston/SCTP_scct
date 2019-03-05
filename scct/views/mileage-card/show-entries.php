<?php

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\grid\GridView;
use yii\bootstrap\Modal;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\mileage-card */
?>
<style type="text/css">
[data-key="0"] 
{
    display:none;
}
</style>

<div class="mileage-card-entries">

    <input id="SundayDate" type="hidden" name="SundayDate" value=<?php echo $SundayDateFull; ?>>
    <input id="SaturdayDate" type="hidden" name="SaturdayDate" value=<?php echo $SaturdayDateFull; ?>>
    <input id="MileageCardProjectID" type="hidden" name="MileageCardProjectID" value=<?php echo $mileageCardProjectID; ?>>
  
    <div class="lightBlueBar">
		<h3> <?= $projectName.' Week '.$from.' - '.$to.': '.$lName.', '.$fName; ?></h3>

		<?php
		$isAccountant = Yii::$app->session['UserAppRoleType'] == 'Accountant';
		if ($model['MileageCardApprovedFlag'] == 1) {
			$approve_status = true;
		} else {
			$approve_status = false;
		}
		?>
		<p>
			<?= Html::a('Back', ['index'], ['class' => 'btn btn-primary']) ?>
			<?= Html::button('Approve', [
				'class' => 'btn btn-primary',
				'disabled' => $approve_status || $isAccountant,
				'id' => 'approve_mileageCard_btn_id',
			]) ?>
			<?= Html::button('Deactivate', [
				'class' => 'btn btn-primary',
				'disabled' => true,
				'id' => 'deactive_mileageEntry_btn_id',
			]) ?>
			<?= Html::button('Add Mileage', [
				'class' => 'btn btn-primary add_mileageEntry_btn',
				'disabled' => $approve_status && ($isSubmitted || !$isAccountant),
				'id' => 'add_mileage_btn_id',
			]) ?>
		</p>
		<br>
    </div>
    <?php Pjax::begin(['id' => 'ShowMileageEntriesView', 'timeout' => false]) ?>
		<?= \kartik\grid\GridView::widget([
			'id' => 'allMileageEntries',
			'dataProvider' => $task,
			'export' => false,
			'pjax' => true,
			'summary' => '',
			'caption' => "",
			'columns' => [
				[
					'label' => 'Task',
					'attribute' => 'Task',
				],
				[
					'label' => 'Sunday ' . $SundayDate,
					'attribute' => 'Date1',
					'headerOptions' => ['class'=>$SundayDateFull]
				],
				[
					'label' => 'Monday '. $MondayDate,
					'attribute' => 'Date2',
					'headerOptions' => ['class'=>$MondayDateFull],
				],
				[
					'label' => 'Tuesday '. $TuesdayDate,
					'attribute' => 'Date3',
					'headerOptions' => ['class'=>$TuesdayDateFull],
				],
				[
					'label' => 'Wednesday '. $WednesdayDate,
					'attribute' => 'Date4',
					'headerOptions' => ['class'=>$WednesdayDateFull],
				],
				[
					'label' => 'Thursday '. $ThursdayDate,
					'attribute' => 'Date5',
					'headerOptions' => ['class'=>$ThursdayDateFull],
				],
				[
					'label' => 'Friday '. $FridayDate,
					'attribute' => 'Date6',
					'headerOptions' => ['class'=>$FridayDateFull],
				],
				[
					'label' => 'Saturday '. $SaturdayDate,
					'attribute' => 'Date7',
					'headerOptions' => ['class'=>$SaturdayDateFull],
				],
				[
					'header'            => 'Deactivate Mileage',
					'class'             => 'kartik\grid\CheckboxColumn',
					'contentOptions'    => [],
					'checkboxOptions'   => function ($model) {
						return ['mileageCardId' => Yii::$app->getRequest()->getQueryParam('id'),
							'taskName' => $model['Task'],'entry' => '','class'=>'entryData'];
					}
				]
			]
		]);
		?>
		<?= Html::label('Total Miles: '. $model['SumMiles'],
			null, ['id' => 'entries_sum_miles']) ?>
		<input type="hidden" value=<?php echo $model["MileageCardID"]?> name="mileageCardId" id="mileageCardId">
		<input type="hidden" value=<?php echo $isAccountant ?> id="isAccountant">
		<input type="hidden" value=<?php echo $isSubmitted ?> id="isSubmitted">
    <?php Pjax::end() ?>
    <?php
    Pjax::begin(['id' => 'showMiles', 'timeout' => false]);
		Modal::begin([
			'header' => '<h4>ADD MILEAGE</h4>',
			'id' => 'addMileageModal',
			'size' => 'modal-lg',
		]);
		echo "<div id='modalAddMiles'><span id='modalContentSpan'></span></div>";
		Modal::end();
    Pjax::end();
    ?>
	
	<?php
    Pjax::begin(['id' => 'showMileageEntries', 'timeout' => false]);
		Modal::begin([
			'header' => '<h4 id="viewMileageModalTitle" style="float:left;"></h4>
				<h4 id="viewMileageModalDate" style="float:right;"></h4>',
			'id' => 'viewMileageModal',
			'size' => 'modal-lg',
		]);
		echo "<div id='modalViewMiles'><span id='viewEntriesModalContentSpan'></span></div>";
		Modal::end();
    Pjax::end();
    ?>

</div>
