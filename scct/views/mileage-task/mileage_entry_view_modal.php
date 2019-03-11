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
        'enablePushState' => false ]) ?>

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
					])->textInput(['placeholder' => 'Starting Mileage', 'type' => 'number', 'readonly' => true]); ?>
				</div>
				<?= Html::activeLabel($model, 'EndingMileage', [
					'label' => 'Ending Mileage',
					'class' => 'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-4">
					<?= $form->field($model, 'EndingMileage', [
						'showLabels' => false
					])->textInput(['placeholder' => 'Ending Mileage', 'type' => 'number', 'readonly' => true]); ?>
				</div><?= Html::activeLabel($model, 'PersonalMiles', [
					'label' => 'Personal Miles',
					'class' => 'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-4">
					<?= $form->field($model, 'PersonalMiles', [
						'showLabels' => false
					])->textInput(['placeholder' => 'Personal Miles', 'type' => 'number', 'readonly' => true]); ?>
				</div>
				<?= Html::activeLabel($model, 'AdminMiles', [
					'label' => 'Admin Miles',
					'class' => 'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-4">
					<?= $form->field($model, 'AdminMiles', [
						'showLabels' => false
					])->textInput(['placeholder' => 'Admin Miles', 'type' => 'number', 'readonly' => true]); ?>
				</div>
				<?= Html::activeHiddenInput($model, 'EntryID', ['value' => $model->EntryID]); ?>
				<?= Html::activeHiddenInput($model, 'CardID', ['value' => $model->CardID]); ?>
				<?= Html::activeHiddenInput($model, 'Date', ['value' => $model->Date]); ?>
			</div>
		</div>
		<br>
		<div id="updateMileageEntryButtons" class="form-group">
			<?= Html::Button('Cancel', ['class' => 'btn btn-success', 'id' => 'update_mileage_entry_cancel_btn']) ?>
			<?= Html::Button('Submit', ['class' => 'btn btn-success', 'id' => 'update_mileage_entry_submit_btn']) ?>
		</div>
    <?php ActiveForm::end(); ?>
	
	<script>
		formPopulated = false;
	
		//apply edit tooltip on table
		$.each($('#mileageEntryGridView tbody tr'),function(){
			$(this).attr("title","Click to edit.")
		});
			
		//apply listeners on rows to select entry for edit
		$(document).off('click', '#mileageEntryGridView tbody tr').on('click', '#mileageEntryGridView tbody tr',function (){
			//apply selected highlight_file
			$(this).addClass('mileageEntrySelectedRow').siblings().removeClass("mileageEntrySelectedRow");;
			//set forPopulated to false to avoid validation errors
			formPopulated = false;
			//get type of entry
			type = $(this).find("td[data-col-seq='0']").text();
			
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
			$('#mileageentrytask-entryid').val(entryID);
			$('#mileageentrytask-starttime').data('timepicker').setTime(startTime);
			$('#mileageentrytask-endtime').data('timepicker').setTime(endTime);
			$('#mileageentrytask-startingmileage').val(startMileage);
			$('#mileageentrytask-endingmileage').val(endMileage);
			$('#mileageentrytask-personalmiles').val(personalMiles);
			$('#mileageentrytask-adminmiles').val(adminMiles);
			//enable fields
			if(type == 'Odometer'){
				$('#mileageentrytask-starttime').attr("readonly", false);
				$('#mileageentrytask-endtime').attr("readonly", false);
				$('#mileageentrytask-startingmileage').attr("readonly", false);
				$('#mileageentrytask-endingmileage').attr("readonly", false);
				$('#mileageentrytask-personalmiles').attr("readonly", false);
				$('#mileageentrytask-adminmiles').attr("readonly", true);
			}else{
				$('#mileageentrytask-starttime').attr("readonly", true);
				$('#mileageentrytask-endtime').attr("readonly", true);
				$('#mileageentrytask-startingmileage').attr("readonly", true);
				$('#mileageentrytask-endingmileage').attr("readonly", true);
				$('#mileageentrytask-personalmiles').attr("readonly", true);
				$('#mileageentrytask-adminmiles').attr("readonly", false);
			}
			//set formPopulated to true to enable validation
			formPopulated = true;
			mileageUpdateSubmitButtonSetState();
		});
		
		//link to image onclick
		$(document).off('click', '.mileageViewModalImg').on('click', '.mileageViewModalImg',function (){
			imgSrc = $(this).attr('src').substr(10);
			url = window.location.protocol + "//" + window.location.host + "/" + "/mileage-card/view-image?photoPath=" + imgSrc;
			win = window.open(url ,'_blank');
		});
		
		//close form on cancel
		$(document).off('click', '#update_mileage_entry_cancel_btn').on('click', '#update_mileage_entry_cancel_btn',function (){
			$("#MileageEntryUpdateForm").css("display", "none");
			$('#odometerImgs').css("display", "none");
		});
		
		//check for valid form to determine when submit should be available
		$(document).off('change', '#MileageEntryUpdateForm :input').on('change', '#MileageEntryUpdateForm :input', function (){
			if(formPopulated){
				mileageUpdateSubmitButtonSetState();   
			}
        });
		
		//submit form
		$(document).off('click', '#update_mileage_entry_submit_btn').on('click', '#update_mileage_entry_submit_btn',function (){
			if(mileageUpdateFormValidator()){
				//get form data
				var form = $('#MileageEntryUpdateForm');
				$('#loading').show();
				//post form data
				$.ajax({
					type: 'POST',
					url: form.attr("action"),
					data: form.serialize(),
					success: function (response) {
						//reload show entries gridview
						$.pjax.reload({
							container:"#ShowMileageEntriesView",
							timeout: 99999
						}).done(function(){
							validateMileageToolTip();
							//get params
							id = $('#mileageentrytask-cardid').val();
							date = $('#mileageentrytask-date').val();
							//reload update modal gridview
							$.pjax.reload({
								type: 'GET',
								url: '/mileage-task/view-mileage-entry-task-by-day?mileageCardID='+id+'&date='+date,
								container:"#mileageEntryGridViewPJAX",
								timeout: 99999,
								push: false,
								replace: false,
							}).done(function(){
								//hide form
								$("#MileageEntryUpdateForm").css("display", "none");
								$('#odometerImgs').css("display", "none");
								$('#loading').hide();
							});
						});
					}
				});	
			}else{
				$('#update_mileage_entry_submit_btn').prop('disabled', true);
			}			
		});
		
		//sets submit button state
		function mileageUpdateSubmitButtonSetState(){
			if (mileageUpdateFormValidator()){
				$('#update_mileage_entry_submit_btn').prop('disabled', false); 
            }else{
				$('#update_mileage_entry_submit_btn').prop('disabled', true); 
            }   
		}
		
		//function returns boolean if form fields are valid input
		function mileageUpdateFormValidator(){
			//get form values
			startTime = $('#mileageentrytask-starttime').val();
			endTime = $('#mileageentrytask-endtime').val();
			startMiles = parseFloat($('#mileageentrytask-startingmileage').val());
			endMiles = parseFloat($('#mileageentrytask-endingmileage').val());
			personalMiles = parseFloat($('#mileageentrytask-personalmiles').val());
			adminMiles = parseFloat($('#mileageentrytask-adminmiles').val());
			//not null
			if(startTime == "" ||
			endTime == "" ||
			isNaN(startMiles) ||
			isNaN(endMiles) ||
			isNaN(personalMiles) ||
			isNaN(adminMiles)){
				return false;
			}
			//end time >= start time
			//get 24 hour format
			startTime24 = ConvertToTwentyFourHourTime(startTime);
			endTime24 = ConvertToTwentyFourHourTime(endTime);
			if(startTime24 > endTime24){
				return false;
			}
			//mileage >= 0
			if(startMiles < 0 ||
			endMiles < 0 ||
			personalMiles < 0 ||
			adminMiles < 0){
				return false;
			}
			//end miles >= start miles
			if(startMiles > endMiles){
				return false;
			}
			
			//return true if no rules are broken
			return true;
		}
		
		//also in task_entry_form should look to extract out
		//expected format of "hh:mm AM/PM"
		//returns string in 24 hour format "hh:mm"
		function ConvertToTwentyFourHourTime(twelveHourTime) {
			var hours = Number(twelveHourTime.match(/^(\d+)/)[1]);
			var minutes = Number(twelveHourTime.match(/:(\d+)/)[1]);
			var AMPM = twelveHourTime.match(/\s(.*)$/)[1];
			if(AMPM == "PM" && hours<12) hours = hours+12;
			if(AMPM == "AM" && hours==12) hours = hours-12;
			var sHours = hours.toString();
			var sMinutes = minutes.toString();
			if(hours<10) sHours = "0" + sHours;
			if(minutes<10) sMinutes = "0" + sMinutes;
			return sHours + ":" + sMinutes;
		}
	</script>
</div>