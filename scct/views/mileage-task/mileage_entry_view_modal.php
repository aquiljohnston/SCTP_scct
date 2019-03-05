<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use kartik\grid\GridView;
use kartik\form\ActiveForm;

$columns = [
	[
		'label' => 'Mileage Type',
		'attribute' => 'MileageType',
		'headerOptions' => ['class' => 'text-center'],
		'contentOptions' => ['class' => 'text-center'],
	],[
		'label' => 'Start Time - End Time',
		'headerOptions' => ['class' => 'text-center'],
		'contentOptions' => ['class' => 'text-center'],
		'value' => function($model, $key, $index, $column) {
			return $model['StartTime'] . ' - ' . $model['EndTime'];
		},
	],[
		'label' => 'Starting Mileage',
		'attribute' => 'StartingMileage',
		'headerOptions' => ['class' => 'text-center'],
		'contentOptions' => ['class' => 'text-center'],
	],[
		'label' => 'Ending Mileage',
		'attribute' => 'EndingMileage',
		'headerOptions' => ['class' => 'text-center'],
		'contentOptions' => ['class' => 'text-center'],
	],[
		'label' => 'Personal Miles',
		'attribute' => 'PersonalMiles',
		'headerOptions' => ['class' => 'text-center'],
		'contentOptions' => ['class' => 'text-center'],
	],[
		'label' => 'Admin Miles',
		'attribute' => 'AdminMiles',
		'headerOptions' => ['class' => 'text-center'],
		'contentOptions' => ['class' => 'text-center'],
	],[
		'attribute' => 'EntryID',
		'hidden' => true,
	],[
		'attribute' => 'StartTime',
		'hidden' => true,
	],[
		'attribute' => 'EndTime',
		'hidden' => true,
	],[
		'attribute' => 'Photo1',
		'hidden' => true,
	],[
		'attribute' => 'Photo2',
		'hidden' => true,
	]
];

?>

<div class="mileage-entry-view">
	<!--mileage entry list-->
	<?php Pjax::begin([
        'id' => 'mileageEntryGridViewPJAX',
        'timeout' => 10000,
        'enablePushState' => false  ]) ?>

		<?= GridView::widget([
			'id' =>'mileageEntryGV',
			'dataProvider' => $mileageEntryDataProvider,
			'summary' => '',
			'export' => false,
			'pjax' =>true,
			'pjaxSettings' => [
				'options' => [
					'id' => 'mileageEntryGridView',
				],
			],
			'columns' => $columns,
		]); ?>
		
    <?php Pjax::end() ?>
	
	<!--selected mileage entry images-->
	<div id="odometerImgs" style="display:none">
		<img id="mileageViewModalPhoto1" src="" alt="photo1" class="mileageViewModalImg">
		<img id="mileageViewModalPhoto2" src="" alt="photo2" class="mileageViewModalImg">
	</div>
	
	<!--selected mileage entry update form-->
	<?php $form = ActiveForm::begin([
        'id' => 'MileageEntryUpdateForm',
        'type' => ActiveForm::TYPE_HORIZONTAL,
        'action' => Url::to('/mileage-task/update'),
        'formConfig' => ['labelSpan' => 1, 'deviceSize' => ActiveForm::SIZE_SMALL],
    ]); ?>
		<div class="form-group kv-fieldset-inline" id="mileage_entry_form">
			<div class="row">
				<?= Html::activeLabel($model, 'StartTime', [
					'label' => 'Start Time',
					'class' => 'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-4">
					<?= $form->field($model, 'StartTime', [
						'showLabels' => false
					])->widget(\kartik\widgets\TimePicker::classname(), [
						'pluginOptions' => ['placeholder' => 'Enter time...','defaultTime' => FALSE],
						'readonly' => true
					]); ?>
				</div>
				<?= Html::activeLabel($model, 'EndTime', [
					'label' => 'End Time',
					'class' => 'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-4">
					<?= $form->field($model, 'EndTime', [
						'showLabels' => false
					])->widget(\kartik\widgets\TimePicker::classname(), [
						'pluginOptions' => ['placeholder' => 'Enter time...','defaultTime' => FALSE],
						'readonly' => true
					]); ?>
				</div>
				<?= Html::activeLabel($model, 'StartingMileage', [
					'label' => 'Starting Mileage',
					'class' => 'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-4">
					<?= $form->field($model, 'StartingMileage', [
						'showLabels' => false
					])->textInput(['placeholder' => 'Starting Mileage', 'id'=> 'mileageEntryFormStartingMileage', 'type' => 'number', 'readonly' => true]); ?>
				</div>
				<?= Html::activeLabel($model, 'EndingMileage', [
					'label' => 'Ending Mileage',
					'class' => 'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-4">
					<?= $form->field($model, 'EndingMileage', [
						'showLabels' => false
					])->textInput(['placeholder' => 'Ending Mileage', 'id'=> 'mileageEntryFormEndingMileage', 'type' => 'number', 'readonly' => true]); ?>
				</div><?= Html::activeLabel($model, 'PersonalMiles', [
					'label' => 'Personal Miles',
					'class' => 'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-4">
					<?= $form->field($model, 'PersonalMiles', [
						'showLabels' => false
					])->textInput(['placeholder' => 'Personal Miles', 'id'=> 'mileageEntryFormPersonalMiles', 'type' => 'number', 'readonly' => true]); ?>
				</div>
				<?= Html::activeLabel($model, 'AdminMiles', [
					'label' => 'Admin Miles',
					'class' => 'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-4">
					<?= $form->field($model, 'AdminMiles', [
						'showLabels' => false
					])->textInput(['placeholder' => 'Admin Miles', 'id'=> 'mileageEntryFormAdminMiles', 'type' => 'number', 'readonly' => true]); ?>
				</div>
				<?= Html::activeHiddenInput($model, 'EntryID', ['id'=> 'mileageEntryFormEntryID', 'value' => $model->EntryID]); ?>
			</div>
		</div>
		<br>
		<br>
		<div id="updateMileageEntryButtons" class="form-group">
			<?= Html::Button('Cancel', ['class' => 'btn btn-success', 'id' => 'update_mileage_entry_cancel_btn']) ?>
			<?= Html::Button('Submit', ['class' => 'btn btn-success', 'id' => 'update_mileage_entry_submit_btn', 'disabled' => 'disabled']) ?>
		</div>
    <?php ActiveForm::end(); ?>
	
	<script>
	
		//apply edit tooltip on table
		$.each($('#mileageEntryGridView tbody tr'),function(){
			$(this).attr("title","Click to edit.")
		});
		
		//close form on cancel
		$(document).off('click', '#update_mileage_entry_cancel_btn').on('click', '#update_mileage_entry_cancel_btn',function (){
			$("#MileageEntryUpdateForm").css("display", "none");
			$('#odometerImgs').css("display", "none");
		});
		
		//apply listeners on rows to select entry for edit
		$(document).off('click', '#mileageEntryGridView tbody tr').on('click', '#mileageEntryGridView tbody tr',function (){
			//get type of entry
			type = $(this).find("td[data-col-seq='0']").text();
			console.log('type ' + type);
			
			//if odometer display images
			if(type == 'Odometer'){
				$("#odometerImgs").css("display", "block");
				//set img src values
				photo1 = $(this).find("td[data-col-seq='9']").text();
				photo2 = $(this).find("td[data-col-seq='10']").text();
				if(photo1 != '') $("#mileageViewModalPhoto1").attr('src', "../images/" + photo1);
				if(photo2 != '') $("#mileageViewModalPhoto2").attr('src', "../images/" + photo2);
			} else {
				$('#odometerImgs').css("display", "none");
			}

			//display form
			$("#MileageEntryUpdateForm").css("display", "block");
			//get values
			entryID = $(this).find("td[data-col-seq='6']").text();
			startTime = $(this).find("td[data-col-seq='7']").text();
			endTime = $(this).find("td[data-col-seq='8']").text();
			startMileage = $(this).find("td[data-col-seq='2']").text();
			endMileage = $(this).find("td[data-col-seq='3']").text();
			personalMiles = $(this).find("td[data-col-seq='4']").text();
			adminMiles = $(this).find("td[data-col-seq='5']").text();
			//set values
			$('#mileageEntryFormEntryID').val(entryID);
			$('#dynamicmodel-starttime').data('timepicker').setTime(startTime);
			$('#dynamicmodel-endtime').data('timepicker').setTime(endTime);
			$('#mileageEntryFormStartingMileage').val(startMileage);
			$('#mileageEntryFormEndingMileage').val(endMileage);
			$('#mileageEntryFormPersonalMiles').val(personalMiles);
			$('#mileageEntryFormAdminMiles').val(adminMiles);
			//enable fields
			if(type == 'Odometer'){
				$('#dynamicmodel-starttime').attr("readonly", false);
				$('#dynamicmodel-endtime').attr("readonly", false);
				$('#mileageEntryFormStartingMileage').attr("readonly", false);
				$('#mileageEntryFormEndingMileage').attr("readonly", false);
				$('#mileageEntryFormPersonalMiles').attr("readonly", false);
				$('#mileageEntryFormAdminMiles').attr("readonly", false);
			}else{
				$('#dynamicmodel-starttime').attr("readonly", true);
				$('#dynamicmodel-endtime').attr("readonly", true);
				$('#mileageEntryFormStartingMileage').attr("readonly", true);
				$('#mileageEntryFormEndingMileage').attr("readonly", true);
				$('#mileageEntryFormPersonalMiles').attr("readonly", true);
				$('#mileageEntryFormAdminMiles').attr("readonly", false);
			}	
		});
		
	</script>
</div>