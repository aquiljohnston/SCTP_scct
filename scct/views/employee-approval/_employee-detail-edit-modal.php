<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use kartik\form\ActiveForm;

?>

<div class="employee-detail-edit">
	<!--display boundry values-->
	<div id="boundry_values">
		<span id="start_boundry" class="col-sm-6">
			<?= 'Minimum Start Time ' . ($prevModel->StartTime != ""  ? $prevModel->StartTime : "") ?>
		</span>
		<span id="end_boundry" class="col-sm-6">
			<?= 'Maximum End Time ' . ($nextModel->EndTime != ""  ? $nextModel->EndTime : "") ?>
		</span>
	</div>
	<!--update form-->
	<?php $form = ActiveForm::begin([
        'id' => 'EmployeeDetailModalForm',
        'type' => ActiveForm::TYPE_HORIZONTAL,
        'action' => Url::to('/employee-approval/employee-detail-update'),
        'formConfig' => ['labelSpan' => 1, 'deviceSize' => ActiveForm::SIZE_SMALL],
    ]); ?>
		<div class="form-group kv-fieldset-inline" id="employee_detail_form">
			<div class="row">
				<!--Prev Row Data-->
				<?= Html::activeHiddenInput($prevModel, 'PrevID', ['value' => $prevModel->ID]); ?>
				<?= Html::activeHiddenInput($prevModel, 'PrevProjectID', ['value' => $prevModel->ProjectID]); ?>
				<?= Html::activeHiddenInput($prevModel, 'PrevTaskID', ['value' => $prevModel->TaskID]); ?>
				<?= Html::activeHiddenInput($prevModel, 'PrevTaskName', ['value' => $prevModel->TaskName]); ?>
				<?= Html::activeHiddenInput($prevModel, 'PrevStartTime', ['value' => $prevModel->StartTime]); ?>
				<?= Html::activeHiddenInput($prevModel, 'PrevEndTime', ['value' => $prevModel->EndTime]); ?>
				<!--Current Row Data-->
				<?= Html::activeHiddenInput($model, 'ID', ['value' => $model->ID]); ?>
				<!-- may need to add date depending on data format-->
				<?= Html::activeLabel($model, 'ProjectID', [
					'label' => 'Project',
					'class' => 'col-sm-2 control-label'
					]) ?>
				<div class="col-sm-4">
					<?= $form->field($model, 'ProjectID', [
						'showLabels' => false
					])->dropDownList($projectDropDown); ?>
				</div>
				<?php Pjax::begin(['id' => 'taskDropDownPjax', 'timeout' => false]) ?>
					<?= Html::activeLabel($model, 'TaskID', [
						'label' => 'Task',
						'class' => 'col-sm-2 control-label'
						]) ?>
					<div class="col-sm-4">
						<?= $form->field($model, 'TaskID', [
							'showLabels' => false
						])->dropDownList($taskDropDown,
							['disabled' => $model->TaskID == 0 ? true : false,]
						); ?>
					</div>
				<?php Pjax::end() ?>
				<?= Html::activeHiddenInput($nextModel, 'TaskName', ['value' => $model->TaskName]); ?>
				<?= Html::activeLabel($model, 'StartTime', [
					'label' => 'Start Time',
					'class' => 'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-4">
					<?= $form->field($model, 'StartTime', [
						'showLabels' => false
					])->widget(\kartik\widgets\TimePicker::classname(), [
						'pluginOptions' => ['placeholder' => 'Enter time...','defaultTime' => FALSE, 'showMeridian' => FALSE, 'showSeconds' => true],
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
						'pluginOptions' => ['placeholder' => 'Enter time...','defaultTime' => FALSE, 'showMeridian' => FALSE, 'showSeconds' => true],
					]); ?>
				</div>
				<!--Next Row Data-->
				<?= Html::activeHiddenInput($nextModel, 'NextID', ['value' => $nextModel->ID]); ?>
				<?= Html::activeHiddenInput($nextModel, 'NextProjectID', ['value' => $nextModel->ProjectID]); ?>
				<?= Html::activeHiddenInput($nextModel, 'NextTaskID', ['value' => $nextModel->TaskID]); ?>
				<?= Html::activeHiddenInput($nextModel, 'NextTaskName', ['value' => $nextModel->TaskName]); ?>
				<?= Html::activeHiddenInput($nextModel, 'NextStartTime', ['value' => $nextModel->StartTime]); ?>
				<?= Html::activeHiddenInput($nextModel, 'NextEndTime', ['value' => $nextModel->EndTime]); ?>
			</div>
		</div>
		<br>
		<div id="employeeDetailModalFormButtons" class="form-group" style="display:block">
			<span id = "error_message"></span>
			<?= Html::Button('Submit', ['class' => 'btn btn-success', 'id' => 'employee_detail_form_submit_btn', 'disabled' => true]) ?>
		</div>
    <?php ActiveForm::end(); ?>
	<script>
	//form on task change update task name value
	$(document).off('change', '#employeedetailtime-taskid').on('change', '#employeedetailtime-taskid', function (){
		if($('#employeedetailtime-taskid').val() != 0){
			$('#employeedetailtime-taskname').val('Task ' + $('#employeedetailtime-taskid option:selected').text());
		}
	});
	//form on time change update adjeacent rows
	//on start time change update end time of prev if it exist
	$(document).off('change', '#employeedetailtime-starttime').on('change', '#employeedetailtime-starttime', function (){
		if(!$('#employeedetailtime-prevendtime').val() == ''){
			$('#employeedetailtime-prevendtime').val($('#employeedetailtime-starttime').val());
		}
		validateForm();
	});
	//on end time change update start time of next if it exist
	$(document).off('change', '#employeedetailtime-endtime').on('change', '#employeedetailtime-endtime', function (){
		if(!$('#employeedetailtime-nextstarttime').val() == ''){
			$('#employeedetailtime-nextstarttime').val($('#employeedetailtime-endtime').val());
		}
		validateForm();
	});
	//form on project change reload task dropDownList
	$(document).off('change', '#employeedetailtime-projectid').on('change', '#employeedetailtime-projectid', function (){
		reloadTaskDropdown();
	});
	//form submit on click employee_detail_form_submit_btn
	$(document).off('click', '#employee_detail_form_submit_btn').on('click', '#employee_detail_form_submit_btn', function (){
		submitEdit();
	});
	//form validation check
	function validateForm(){
		//get form data
		data = getFormData();
		//pass form to controller to perform rule check
		$.ajax({
			type: 'POST',
			url: '/employee-approval/employee-detail-validate',
			data: data,
			success: function (response) {
				if(response == ''){
					//if response is true enable submit and clear validation message
					$('#error_message').text('');
					$('#employee_detail_form_submit_btn').prop('disabled', false);//enable button
				}else{
					//if response if false disable submit and display validation message
					$('#error_message').text(response);
					$('#employee_detail_form_submit_btn').prop('disabled', true); //disable button
				}
			},
			error : function (){
				//if error in validation disable submit and display error message
				console.log('Internal Server Error');
			}
		});
	}
	//form pjax reload
	function reloadTaskDropdown(){
		//get date for url
		currentDate = $('#currentDate').val();
		//get current user for project dropdown
		userID = $('#userID').val();
		//fetch formatted form values
		data = getFormData();
		
		$('#loading').show();
		$.pjax.reload({
			type: 'POST',
			replace: false,
			url: '/employee-approval/employee-detail-modal?userID=' + userID + '&date=' + currentDate,
			data: data,
			container: '#taskDropDownPjax',
			timeout: 99999
		});
		$('#taskDropDownPjax').off('pjax:success').on('pjax:success', function () {
			$('#loading').hide();
		});
	}
	//form submit edit
	function submitEdit(){
		//get date to appened to time for edit
		currentDate = $('#currentDate').val();
		//fetch formatted form values with date param
		data = getFormData(currentDate);
		//get form object
		var form = $('#EmployeeDetailModalForm');
		
		$('#loading').show();
		$.ajax({
			type: 'POST',
			url: form.attr("action"),
			data: data,
			success: function (response) {
				if(response){
					$.pjax.reload({container:"#EmployeeDetailView", timeout: 99999}).done(function(){
						$('#employee_detail_form_submit_btn').closest('.modal-dialog').parent().modal('hide');
						$('#loading').hide();
					});
				}else
					console.log('Internal Server Error');
			},
			error : function (){
				console.log('Internal Server Error');
			}
		});
	}
	
	//fetch formatted form values
	function getFormData(currentDate = null){
		//get current row of data
		id = $('#employeedetailtime-id').val();
		projectID = $('#employeedetailtime-projectid').val();
		taskID = $('#employeedetailtime-taskid').val();
		taskName = $('#employeedetailtime-taskname').val();
		//append date if available for edit
		startTime = currentDate != null ? currentDate + ' ' + $('#employeedetailtime-starttime').val() : $('#employeedetailtime-starttime').val();
		endTime = currentDate != null ? currentDate + ' ' + $('#employeedetailtime-endtime').val() : $('#employeedetailtime-endtime').val();
		//grab previous row of data
		prevId = $('#employeedetailtime-previd').val();
		prevProjectID = $('#employeedetailtime-prevprojectid').val();
		prevTaskID = $('#employeedetailtime-prevtaskid').val();
		prevTaskName = $('#employeedetailtime-prevtaskname').val();
		//append date if available for edit and time is not empty
		prevStartTime = $('#employeedetailtime-prevstarttime').val() != '' && currentDate != null ?
			currentDate + ' ' + $('#employeedetailtime-prevstarttime').val() : $('#employeedetailtime-prevstarttime').val();
		prevEndTime = $('#employeedetailtime-prevendtime').val() != '' && currentDate != null ?
			currentDate + ' ' + $('#employeedetailtime-prevendtime').val() : $('#employeedetailtime-prevendtime').val();
		//grab next row of data
		nextId = $('#employeedetailtime-nextid').val();
		nextProjectID = $('#employeedetailtime-nextprojectid').val();
		nextTaskID = $('#employeedetailtime-nexttaskid').val();
		nextTaskName = $('#employeedetailtime-nexttaskname').val();
		//append date if available for edit and time is not empty
		nextStartTime = $('#employeedetailtime-nextstarttime').val() != '' && currentDate != null ?
			currentDate + ' ' + $('#employeedetailtime-nextstarttime').val() : $('#employeedetailtime-nextstarttime').val();
		nextEndTime = $('#employeedetailtime-nextendtime').val() != '' && currentDate != null ?
			currentDate + ' ' + $('#employeedetailtime-nextendtime').val() : $('#employeedetailtime-nextendtime').val();
		
		data = {
			Current: {ID: id, ProjectID: projectID, TaskID: taskID, TaskName: taskName, StartTime: startTime, EndTime: endTime},
			Prev: {ID: prevId, ProjectID: prevProjectID, TaskID: prevTaskID, TaskName: prevTaskName, StartTime: prevStartTime, EndTime: prevEndTime},
			Next: {ID: nextId, ProjectID: nextProjectID, TaskID: nextTaskID, TaskName: nextTaskName, StartTime: nextStartTime, EndTime: nextEndTime}
		};
		
		return data;
	}
	</script>
</div>