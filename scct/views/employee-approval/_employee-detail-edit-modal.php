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
        'formConfig' => ['labelSpan' => 1, 'deviceSize' => ActiveForm::SIZE_SMALL],
    ]); ?>
		<div class="form-group kv-fieldset-inline" id="employee_detail_form">
			<div class="row">
				<!--Prev Row Data-->
				<?= Html::activeHiddenInput($prevModel, 'PrevID', ['value' => $prevModel->ID]); ?>
				<?= Html::activeHiddenInput($prevModel, 'PrevProjectID', ['value' => $prevModel->ProjectID]); ?>
				<?= Html::activeHiddenInput($prevModel, 'PrevProjectName', ['value' => $prevModel->ProjectName]); ?>
				<?= Html::activeHiddenInput($prevModel, 'PrevTask', ['value' => $prevModel->Task]); ?>
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
				<?= Html::activeHiddenInput($model, 'ProjectName', ['value' => $model->ProjectName]); ?>
				<?php Pjax::begin(['id' => 'taskDropDownPjax', 'timeout' => false]) ?>
					<?= Html::activeLabel($model, 'Task', [
						'label' => 'Task',
						'class' => 'col-sm-2 control-label'
						]) ?>
					<div class="col-sm-4">
						<?= $form->field($model, 'Task', [
							'showLabels' => false
						])->dropDownList($taskDropDown,
							['readonly' => $model->Task == 'Employee Logout' || $model->Task == 'Employee Login' ? true : false,]); ?>
					</div>
				<?php Pjax::end() ?>
				<?= Html::activeLabel($model, 'StartTime', [
					'label' => 'Start Time',
					'class' => 'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-4">
					<?= $form->field($model, 'StartTime', [
						'showLabels' => false
					])->widget(\kartik\widgets\TimePicker::classname(), [
						'pluginOptions' => ['placeholder' => 'Enter time...','defaultTime' => FALSE, 'showMeridian' => FALSE],
						'disabled' => $model->Task == 'Employee Logout' ? true : false,
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
						'pluginOptions' => ['placeholder' => 'Enter time...','defaultTime' => FALSE, 'showMeridian' => FALSE],
						'disabled' => $model->Task == 'Employee Login' ? true : false,
					]); ?>
				</div>
				<!--Next Row Data-->
				<?= Html::activeHiddenInput($nextModel, 'NextID', ['value' => $nextModel->ID]); ?>
				<?= Html::activeHiddenInput($nextModel, 'NextProjectID', ['value' => $nextModel->ProjectID]); ?>
				<?= Html::activeHiddenInput($nextModel, 'NextProjectName', ['value' => $nextModel->ProjectName]); ?>
				<?= Html::activeHiddenInput($nextModel, 'NextTask', ['value' => $nextModel->Task]); ?>
				<?= Html::activeHiddenInput($nextModel, 'NextStartTime', ['value' => $nextModel->StartTime]); ?>
				<?= Html::activeHiddenInput($nextModel, 'NextEndTime', ['value' => $nextModel->EndTime]); ?>
			</div>
		</div>
		<br>
		<div id="employeeDetailModalFormButtons" class="form-group" style="display:block">
			<?= Html::Button('Submit', ['class' => 'btn btn-success', 'id' => 'employee_detail_form_submit_btn']) ?>
		</div>
    <?php ActiveForm::end(); ?>
	<input type="hidden" value="<?php echo $userID?>" id="userID">
	<script>
	//form on time change update adjeacent rows
	//on start time change update end time of prev if it exist
	$(document).off('change', '#employeedetailtime-starttime').on('change', '#employeedetailtime-starttime', function (){
		if(!$('#employeedetailtime-prevendtime').val() == ''){
			$('#employeedetailtime-prevendtime').val($('#employeedetailtime-starttime').val());
		}
	});
	//on end time change update start time of next if it exist
	$(document).off('change', '#employeedetailtime-endtime').on('change', '#employeedetailtime-endtime', function (){
		if(!$('#employeedetailtime-nextstarttime').val() == ''){
			$('#employeedetailtime-nextstarttime').val($('#employeedetailtime-endtime').val());
		}
	});
	//form on project change reload task dropDownList
	$(document).off('change', '#employeedetailtime-projectid').on('change', '#employeedetailtime-projectid', function (){
		reloadTaskDropdown();
	});
	//form validation check
	function validateForm(){
		
	}
	//form pjax reload
	function reloadTaskDropdown(){
		//get current user for project dropdown
		userID = $('#userID').val();
		//fetch formatted form values
		data = getFormData();
		
		$('#loading').show();
		$.pjax.reload({
			type: 'POST',
			replace: false,
			url: '/employee-approval/employee-detail-modal?userID=' + userID,
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
		
	}
	
	//fetch formatted form values
	function getFormData(){
		//get current row of data
		id = $('#employeedetailtime-id').val();
		projectID = $('#employeedetailtime-projectid').val();
		projectName = $('#employeedetailtime-projectname').val();
		task = $('#employeedetailtime-task').val();
		startTime = $('#employeedetailtime-starttime').val();
		endTime = $('#employeedetailtime-endtime').val();
		//grab previous row of data
		prevId = $('#employeedetailtime-previd').val();
		prevProjectID = $('#employeedetailtime-prevprojectid').val();
		prevProjectName = $('#employeedetailtime-prevprojectname').val();
		prevTask = $('#employeedetailtime-prevtask').val();
		prevStartTime = $('#employeedetailtime-prevstarttime').val();
		prevEndTime = $('#employeedetailtime-prevendtime').val();
		//grab next row of data
		nextId = $('#employeedetailtime-nextid').val();
		nextProjectID = $('#employeedetailtime-nextprojectid').val();
		nextProjectName = $('#employeedetailtime-nextprojectname').val();
		nextTask = $('#employeedetailtime-nexttask').val();
		nextStartTime = $('#employeedetailtime-nextstarttime').val();
		nextEndTime = $('#employeedetailtime-nextendtime').val();
		
		data = {
			Current: {ID: id, ProjectID: projectID, ProjectName: projectName, Task: task, StartTime: startTime, EndTime: endTime},
			Prev: {ID: prevId, ProjectID: prevProjectID,ProjectName: prevProjectName, Task: prevTask, StartTime: prevStartTime, EndTime: prevEndTime},
			Next: {ID: nextId, ProjectID: nextProjectID,ProjectName: nextProjectName, Task: nextTask, StartTime: nextStartTime, EndTime: nextEndTime}
		};
		
		return data;
	}
	</script>
</div>