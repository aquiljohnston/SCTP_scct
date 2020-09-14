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
				<?= Html::activeLabel($model, 'ProjectName', [
					'label' => 'Project',
					'class' => 'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-4">
					<?= $form->field($model, 'ProjectName', [
						'showLabels' => false
					])->textInput(['placeholder' => '', 'type' => 'string']); ?>
				</div>
				<?= Html::activeLabel($model, 'Task', [
					'label' => 'Task',
					'class' => 'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-4">
					<?= $form->field($model, 'Task', [
						'showLabels' => false
					])->textInput(['placeholder' => '', 'type' => 'string',
						'readonly' => $model->Task == 'Employee Logout' || $model->Task == 'Employee Login' ? true : false,]); ?>
				</div>
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
				
				<?= Html::activeHiddenInput($model, 'ID', ['value' => $model->ID]); ?>
				<!-- may need to add date depending on data format-->
			</div>
		</div>
		<br>
		<div id="employeeDetailModalFormButtons" class="form-group" style="display:none">
			<?= Html::Button('Cancel', ['class' => 'btn btn-success', 'id' => 'employee_detail_form_cancel_btn']) ?>
			<?= Html::Button('Submit', ['class' => 'btn btn-success', 'id' => 'employee_detail_form_submit_btn']) ?>
		</div>
    <?php ActiveForm::end(); ?>
</div>