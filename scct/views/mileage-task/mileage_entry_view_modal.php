<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use kartik\grid\GridView;
use kartik\form\ActiveForm;

$columns = [
	[
		'label' => 'Type',
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
		'label' => 'Rate',
		'attribute' => 'MileageRate',
		'headerOptions' => ['class' => 'text-center'],
		'contentOptions' => ['class' => 'text-center'],
		'value' => function($model, $key, $index, $column) {
			return round($model['MileageRate'], 3);
		},
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
	],[
		'attribute' => 'StartingMileageEntryComment',
		'hidden' => true,
	],[
		'attribute' => 'EndingMileageEntryComment',
		'hidden' => true,
	],
];
$gridViewSettingsArray = [
	'id' =>'mileageEntryGV',
	'dataProvider' => $mileageEntryDataProvider,
	'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ''],
	'summary' => '',
	'export' => false,
	'pjax' =>true,
	'pjaxSettings' => [
		'options' => [
			'id' => 'mileageEntryGridView',
		],
	],
];
//add create, delete, and update options if not in readOnly state
if($readOnly == 0){
	$columns[] = [
		'class' => 'kartik\grid\ActionColumn',
		'template' => '{update} {delete}',
		'header' => '',
		'buttons' => [
			'update' => function ($url, $model, $key) {
				return Html::a('<span id="mileageModalUpdateAction" class="glyphicon glyphicon-pencil" title="Edit"></span>');
			},
			'delete' => function ($url, $model, $key) {
				return Html::a('<span id="mileageModalDeactivateAction" class="glyphicon glyphicon-trash" title="Deactivate"></span>');
			},
		]
	];
	$gridViewSettingsArray['emptyText'] = 'Click to Add Mileage.';
	$gridViewSettingsArray['emptyTextOptions'] = [
		'class' => 'createEntryRow',
		'style' => 'text-align:center'
	];
}
//add columns to gridview settings after check for read only status
$gridViewSettingsArray['columns'] = $columns;
?>

<div class="mileage-entry-view">
	<input type="hidden" value=<?php echo $readOnly ?> id="viewMileageReadOnly">	
	<!--mileage entry list-->
	<?php Pjax::begin([
        'id' => 'mileageEntryGridViewPJAX',
        'timeout' => 10000,
        'enablePushState' => false ]) ?>
		<?= GridView::widget($gridViewSettingsArray); ?>
    <?php Pjax::end() ?>
	
	<!--selected mileage entry images-->
	<div id="odometerImgs" style="display:none">
		<img id="mileageViewModalPhoto1" src="" alt="photo1" class="mileageViewModalImg">
		<img id="mileageViewModalPhoto2" src="" alt="photo2" class="mileageViewModalImg">
	</div>
	
	<!--selected mileage entry update form-->
	<?php $form = ActiveForm::begin([
        'id' => 'MileageEntryModalForm',
        'type' => ActiveForm::TYPE_HORIZONTAL,
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
						'disabled' => true
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
						'disabled' => true
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
				</div>
				<?= Html::activeLabel($model, 'StartingMileageEntryComment', [
					'label' => 'Starting Comment',
					'class' => 'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-4">
					<?= $form->field($model, 'StartingMileageEntryComment', [
						'showLabels' => false
					])->textInput(['placeholder' => 'Starting Comment', 'type' => 'string', 'readonly' => true]); ?>
				</div>
				<?= Html::activeLabel($model, 'EndingMileageEntryComment', [
					'label' => 'Ending Comment',
					'class' => 'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-4">
					<?= $form->field($model, 'EndingMileageEntryComment', [
						'showLabels' => false
					])->textInput(['placeholder' => 'Ending Comment', 'type' => 'string', 'readonly' => true]); ?>
				</div>
				<?= Html::activeLabel($model, 'PersonalMiles', [
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
				<?= Html::activeLabel($model, 'MileageRate', [
					'label'=>'Rate', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-4">
					<?= $form->field($model, 'MileageRate',[
						'showLabels'=>false
					])->dropDownList($rates); ?>
				</div>
				<?= Html::activeHiddenInput($model, 'EntryID', ['value' => $model->EntryID]); ?>
				<?= Html::activeHiddenInput($model, 'CardID', ['value' => $model->CardID]); ?>
				<?= Html::activeHiddenInput($model, 'Date', ['value' => $model->Date]); ?>
			</div>
		</div>
		<br>
		<div id="mileageEntryModalFormButtons" class="form-group" style="display:none">
			<?= Html::Button('Cancel', ['class' => 'btn btn-success', 'id' => 'update_mileage_entry_cancel_btn']) ?>
			<?= Html::Button('Submit', ['class' => 'btn btn-success', 'id' => 'update_mileage_entry_submit_btn']) ?>
		</div>
    <?php ActiveForm::end(); ?>
	
	<script>
		formPopulated = false;
		isEdit = false;
		isCreate = false;
	
		//apply edit tooltip on table
		$.each($('#mileageEntryGridView tbody tr'),function(){
			//add click to view title to all non 'createEntryRow' rows
			if(!$(this).find('div').hasClass('createEntryRow')){
				$(this).attr("title","Click to view.")
			}
		});
	
		//add 'createEntryRow' row for adding mileage functionality if it does not exist
		$(document).ready(function () {
			//ensure 'createEntryRow' row is present
			checkCreateEntryRow();
		});		
			
		//apply listeners on rows to select entry for view/create
		$(document).off('click', '#mileageEntryGridView tbody tr').on('click', '#mileageEntryGridView tbody tr',function (){
			//apply selected highlight_file
			$(this).addClass('mileageEntrySelectedRow').siblings().removeClass("mileageEntrySelectedRow");
			//set forPopulated to false to avoid validation errors
			formPopulated = false;
			
			//check if the clicked row is a 'createEntryRow'
			if($(this).find('td .createEntryRow').length>0){
				//check submission status
				if($('#isSubmitted').val()){
					//prompt user for submission reset and then open create form on acceptance
					resetSubmissionStatusDialog('showCreateEntryForm');
				}else{
					showCreateEntryForm();
				}
			} else {
				//set isCreate false
				isCreate = false;
				//get type of entry
				type = $(this).find("td[data-col-seq='0']").text();
				
				//if odometer display images
				if(type == 'Odometer'){
					$("#odometerImgs").css("display", "block");
					//set img src values
					photo1 = $(this).find("td[data-col-seq='10']").text();
					photo2 = $(this).find("td[data-col-seq='11']").text();
					if(photo1 != '') $("#mileageViewModalPhoto1").attr('src', "../images/" + photo1);
					if(photo2 != '') $("#mileageViewModalPhoto2").attr('src', "../images/" + photo2);
				} else {
					$('#odometerImgs').css("display", "none");
				}
				
				//get values
				entryID = $(this).find("td[data-col-seq='7']").text();
				startTime = $(this).find("td[data-col-seq='8']").text();
				endTime = $(this).find("td[data-col-seq='9']").text();
				startMileage = $(this).find("td[data-col-seq='2']").text();
				endMileage = $(this).find("td[data-col-seq='3']").text();
				startComment = $(this).find("td[data-col-seq='12']").text();
				endComment = $(this).find("td[data-col-seq='13']").text();
				personalMiles = $(this).find("td[data-col-seq='4']").text();
				adminMiles = $(this).find("td[data-col-seq='5']").text();
				//set values
				$('#mileageentrytask-entryid').val(entryID);
				$('#mileageentrytask-starttime').data('timepicker').setTime(startTime);
				$('#mileageentrytask-endtime').data('timepicker').setTime(endTime);
				$('#mileageentrytask-startingmileage').val(startMileage);
				$('#mileageentrytask-endingmileage').val(endMileage);
				$('#mileageentrytask-startingmileageentrycomment').val(startComment);
				$('#mileageentrytask-endingmileageentrycomment').val(endComment);
				$('#mileageentrytask-personalmiles').val(personalMiles);
				$('#mileageentrytask-adminmiles').val(adminMiles);
				$('#mileageentrytask-mileagerate').val('');
				
				if(!isEdit){
					//disable fields
					$('#mileageentrytask-starttime').attr("disabled", true);
					$('#mileageentrytask-endtime').attr("disabled", true);
					$('#mileageentrytask-startingmileage').attr("readonly", true);
					$('#mileageentrytask-endingmileage').attr("readonly", true);	
					$('#mileageentrytask-startingmileageentrycomment').attr("readonly", true);
					$('#mileageentrytask-endingmileageentrycomment').attr("readonly", true);
					$('#mileageentrytask-personalmiles').attr("readonly", true);
					$('#mileageentrytask-adminmiles').attr("readonly", true);
					$('#mileageentrytask-mileagerate').attr("disabled", true);
					//hide buttons
					$("#mileageEntryModalFormButtons").css("display", "none");
				} else {
					isEdit = false;
				}
				//display form
				$("#MileageEntryModalForm").css("display", "block");
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
		
		//enable form for update entry
		$(document).off('click', '#mileageModalUpdateAction').on('click', '#mileageModalUpdateAction',function (event){
			type = $(this).closest('tr').find("td[data-col-seq='0']").text();
			if($('#isSubmitted').val()){
				//prompt user for submission reset and then open update form on acceptance
				resetSubmissionStatusDialog('showUpdateEntryForm', type);
			}else{
				showUpdateEntryForm(type);
			}
		});
		
		//check for valid form to determine when submit should be available
		$('#MileageEntryModalForm :input').keyup(function (){
			if(formPopulated){
				mileageUpdateSubmitButtonSetState();   
			}
        });
		$(document).off('change', '#MileageEntryModalForm :input').on('change', '#MileageEntryModalForm :input', function (){
			if(formPopulated){
				mileageUpdateSubmitButtonSetState();   
			}
        });
		
		//reset and disable form on cancel
		$(document).off('click', '#update_mileage_entry_cancel_btn').on('click', '#update_mileage_entry_cancel_btn',function (){
			//reset flags
			formPopulated = false;
			//get selected row
			row = $("#mileageEntryGridView tbody tr.mileageEntrySelectedRow");
			//get values
			entryID = row.find("td[data-col-seq='7']").text();
			startTime = row.find("td[data-col-seq='8']").text();
			endTime = row.find("td[data-col-seq='9']").text();
			startMileage = row.find("td[data-col-seq='2']").text();
			endMileage = row.find("td[data-col-seq='3']").text();
			startComment = row.find("td[data-col-seq='12']").text();
			endComment = row.find("td[data-col-seq='13']").text();
			personalMiles = row.find("td[data-col-seq='4']").text();
			adminMiles = row.find("td[data-col-seq='5']").text();
			//set values
			$('#mileageentrytask-entryid').val(entryID);
			$('#mileageentrytask-starttime').data('timepicker').setTime(startTime);
			$('#mileageentrytask-endtime').data('timepicker').setTime(endTime);
			$('#mileageentrytask-startingmileage').val(startMileage);
			$('#mileageentrytask-endingmileage').val(endMileage);
			$('#mileageentrytask-startingmileageentrycomment').val(startComment);
			$('#mileageentrytask-endingmileageentrycomment').val(endComment);
			$('#mileageentrytask-personalmiles').val(personalMiles);
			$('#mileageentrytask-adminmiles').val(adminMiles);
			$('#mileageentrytask-mileagerate').val('');
			//disable fields
			$('#mileageentrytask-starttime').attr("disabled", true);
			$('#mileageentrytask-endtime').attr("disabled", true);
			$('#mileageentrytask-startingmileage').attr("readonly", true);
			$('#mileageentrytask-endingmileage').attr("readonly", true);
			$('#mileageentrytask-startingmileageentrycomment').attr("readonly", true);
			$('#mileageentrytask-endingmileageentrycomment').attr("readonly", true);
			$('#mileageentrytask-personalmiles').attr("readonly", true);
			$('#mileageentrytask-adminmiles').attr("readonly", true);
			$('#mileageentrytask-mileagerate').attr("disabled", true);
			//hide buttons
			$("#mileageEntryModalFormButtons").css("display", "none");
			//hide form if was create//display form
			if(isCreate){
				$("#MileageEntryModalForm").css("display", "none");
				//reset isCreate
				isCreate = false;
			}
		});
		
		//submit form
		$(document).off('click', '#update_mileage_entry_submit_btn').on('click', '#update_mileage_entry_submit_btn',function (){
			if(mileageUpdateFormValidator()){
				//enable potentially disabled fields
				$('#mileageentrytask-starttime').attr("disabled", false);
				$('#mileageentrytask-endtime').attr("disabled", false);
				$('#mileageentrytask-mileagerate').attr("disabled", false);
				//get form data
				var form = $('#MileageEntryModalForm');
				$('#loading').show();
				var urlString = '/mileage-task/update';
				//check isCreate to determine route
				if(isCreate) 
					urlString = '/mileage-task/add-mileage-entry-task';
				//post form data
				$.ajax({
					type: 'POST',
					url: urlString,
					data: form.serialize(),
					success: function (response) {
						reloadMileageGridViews();
					}
				});	
			}else{
				$('#update_mileage_entry_submit_btn').prop('disabled', true);
			}			
		});
		
		//deactivate entry
		$(document).off('click', '#mileageModalDeactivateAction').on('click', '#mileageModalDeactivateAction',function (){
			//get entry id
			var entryID = $(this).closest('tr').attr('data-key');
			if($('#isSubmitted').val()){
				//prompt user for submission reset and then open deactivate dialog
				resetSubmissionStatusDialog('deactivateEntry', entryID);
			}else{
				deactivateEntry(entryID);
			}
			
		});
		
		function reloadMileageGridViews(){
			//reload show entries gridview
			$.pjax.reload({
				container:"#ShowMileageEntriesView",
				timeout: 99999
			}).done(function(){
				validateMileageToolTip();
				//get params
				id = $('#mileageentrytask-cardid').val();
				date = $('#mileageentrytask-date').val();
				readOnly = $('#viewMileageReadOnly').val();
				//reload update modal gridview
				$.pjax.reload({
					type: 'GET',
					url: '/mileage-task/view-mileage-entry-task-by-day?mileageCardID='+id+'&date='+date+'&readOnly='+readOnly,
					container:"#mileageEntryGridViewPJAX",
					timeout: 99999,
					push: false,
					replace: false,
				}).done(function(){
					//ensure 'createEntryRow' row is present
					checkCreateEntryRow();
					//hide form
					$("#MileageEntryModalForm").css("display", "none");
					$('#odometerImgs').css("display", "none");
					$('#loading').hide();
				});
			});
		}
		
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
			mileageRate = $('#mileageentrytask-mileagerate').val();
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
			//mileage rate required
			if(mileageRate == ''){
				return false;
			}
			//return true if no rules are broken
			return true;
		}
		
		function checkCreateEntryRow(){
			if(!$('#mileageEntryGridView tr td .createEntryRow').length > 0 && $('#viewMileageReadOnly').val() == 0){
				//append createEntryRow row
				$('#mileageEntryGridView tbody tr:last').after('<tr><td colspan="12"><div class="createEntryRow" style="text-align: center">Click to Add Mileage.</div></td></tr>');
			}
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
		
		function showCreateEntryForm(){
			//set isCreate true
			isCreate = true;
			//hide pictures
			$('#odometerImgs').css("display", "none");
			//display form
			$("#MileageEntryModalForm").css("display", "block");
			//set default values
			$('#mileageentrytask-entryid').val(0);
			$('#mileageentrytask-starttime').data('timepicker').setTime('12:00 AM');
			$('#mileageentrytask-endtime').data('timepicker').setTime('12:00 AM');
			$('#mileageentrytask-startingmileage').val('0.0');
			$('#mileageentrytask-endingmileage').val('0.0');
			$('#mileageentrytask-startingmileageentrycomment').val('');
			$('#mileageentrytask-endingmileageentrycomment').val('');
			$('#mileageentrytask-personalmiles').val('0.0');
			$('#mileageentrytask-adminmiles').val('');
			$('#mileageentrytask-mileagerate').val('');
			//enable/disable fields
			$('#mileageentrytask-starttime').attr("disabled", true);
			$('#mileageentrytask-endtime').attr("disabled", true);
			$('#mileageentrytask-startingmileage').attr("readonly", true);
			$('#mileageentrytask-endingmileage').attr("readonly", true);
			$('#mileageentrytask-startingmileageentrycomment').attr("readonly", true);
			$('#mileageentrytask-endingmileageentrycomment').attr("readonly", true);
			$('#mileageentrytask-personalmiles').attr("readonly", true);
			$('#mileageentrytask-adminmiles').attr("readonly", false);
			$('#mileageentrytask-mileagerate').attr("disabled", false);
			//display form buttons
			$("#mileageEntryModalFormButtons").css("display", "block");
		}
		
		function showUpdateEntryForm(type){
			//enable fields
			if(type == 'Odometer'){
				$('#mileageentrytask-starttime').attr("disabled", false);
				$('#mileageentrytask-endtime').attr("disabled", false);
				//remove disabled class from time picker buttons that is not removed by setting disabled to false
				$('.disabled-addon').removeClass('disabled-addon');
				$('#mileageentrytask-startingmileage').attr("readonly", false);
				$('#mileageentrytask-endingmileage').attr("readonly", false);		
				$('#mileageentrytask-startingmileageentrycomment').attr("readonly", true);
				$('#mileageentrytask-endingmileageentrycomment').attr("readonly", true);
				$('#mileageentrytask-personalmiles').attr("readonly", false);
				$('#mileageentrytask-adminmiles').attr("readonly", true);
				$('#mileageentrytask-mileagerate').attr("disabled", false);
			}else{
				$('#mileageentrytask-starttime').attr("disabled", true);
				$('#mileageentrytask-endtime').attr("disabled", true);
				$('#mileageentrytask-startingmileage').attr("readonly", true);
				$('#mileageentrytask-endingmileage').attr("readonly", true);
				$('#mileageentrytask-startingmileageentrycomment').attr("readonly", true);
				$('#mileageentrytask-endingmileageentrycomment').attr("readonly", true);
				$('#mileageentrytask-personalmiles').attr("readonly", true);
				$('#mileageentrytask-adminmiles').attr("readonly", false);
				$('#mileageentrytask-mileagerate').attr("disabled", false);
			}
			$("#mileageEntryModalFormButtons").css("display", "block");
			
			//set is edit to allow row click event to populate fields without disabling form
			isEdit = true;
		}
		
		function deactivateEntry(entryID){
			krajeeDialog.defaults.confirm.title = 'Deactivate';
			krajeeDialog.confirm('Are you sure you want to deactivate this entry?', function (resp) {
				if(resp){
					$('#loading').show();
					//post form data
					$.ajax({
						type: 'POST',
						url: '/mileage-task/deactivate?entryID='+entryID,
						success: function (response) {
							reloadMileageGridViews();
						}
					});		
				}
			})
		}
		
		function resetSubmissionStatusDialog(action, variable){
			krajeeDialog.defaults.confirm.title = 'RESET SUBMISSION STATUS!';
			krajeeDialog.confirm("The card you're attempting to edit has already been submitted. " +
				"Continuing with this action will reset all cards for the week and require you to resubmit. " + 
				"Do you wish to proceed?", function (resp) {
				if (resp) {
					startDate = $('#SundayDate').val();
					endDate = $('#SaturdayDate').val();
					data = { dates: {startDate: startDate, endDate: endDate}};
					$('#loading').show();
					//ajax call to reset submission status
					$.ajax({
						type: 'POST',
						url: '/mileage-card/accountant-reset/',
						data: data,
						success: function(resp) {
							if(resp){
								$('#isSubmitted').val('');
								$('#loading').hide();
								// execute next action based on trigger
								// handle potentially passing selected cell from grid view click
								if(variable){
									window[action](variable);
								}else{
									window[action]();
								}
							}
						}
					});
					
				}
			});
		}
	</script>
</div>