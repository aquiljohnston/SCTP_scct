<?php
/**
 * Created by PhpStorm.
 * User: tzhang
 * Date: 2018/1/25
 * Time: 16:42
 */

use yii\helpers\Html;
use kartik\form\ActiveForm;
use kartik\widgets\TimePicker;
use kartik\datetime\DateTimePicker;
use yii\widgets\Pjax;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="time-entry-form">
    <?php Pjax::begin(['id' => 'taskEntryModal', 'timeout' => false]) ?>
    <?php $form = ActiveForm::begin([
        'id' => 'TaskEntryForm',//$model->formName(),
        'type' => ActiveForm::TYPE_HORIZONTAL,
        'action' => Url::to('/task/add-task-entry'),
        'formConfig' => ['labelSpan' => 1, 'deviceSize' => ActiveForm::SIZE_SMALL],
    ]); ?>
    <div class="form-group kv-fieldset-inline" id="time_entry_form">
        <div class="row">
            <?= Html::activeLabel($model, 'Date', [
                'label' => 'Date',
                'class' => 'col-sm-2 control-label'
            ]) ?>
            <div class="col-sm-4">
                <?= $form->field($model, 'Date', [
                    'showLabels' => false
                ])->widget(\kartik\widgets\DatePicker::classname(), [
                    'pluginOptions' => [
                        'placeholder' => 'Enter Date...',
                        'startDate' => $SundayDate,
                        'endDate' => $SaturdayDate,
                        'autoclose'=>true],
                    'type' => \kartik\widgets\DatePicker::TYPE_COMPONENT_APPEND,
                    'pluginEvents' => [
                        "changeDate" => 'function(e) {  
                            var Date = $("#dynamicmodel-date").val();
                            var StartTime = $("#dynamicmodel-starttime").val();
                            var EndTime = $("#dynamicmodel-endtime").val();
                            var TaskName = $("#dynamicmodel-taskname").val();
                            var ChangeOfAccountType = $("#dynamicmodel-chargeofaccounttype").val();
                           
                            if (Date.length != "" && StartTime.length != "" && EndTime.length != "" && TaskName.length != 0 && ChangeOfAccountType.length != 0){
                                //$("#create_task_entry_submit_btn").prop("disabled", false);
                            }
                         }'
                    ]
                ]); ?>
            </div>

            <?= Html::activeLabel($model, 'TaskName', [
                'label' => 'Task Name',
                'class' => 'col-sm-2 control-label'
            ]) ?>
            <div class="col-sm-4">
                <?= $form->field($model, 
                    'TaskName', [
                    'showLabels' => false
                ])->dropDownList($allTask,
                      array('prompt'=>'--Select A Task--')
    ); ?>
            </div>
        </div>
        <div class="row">
            <?= Html::activeLabel($model, 'StartTime', [
                'label' => 'Start Time',
                'class' => 'col-sm-2 control-label'
            ]) ?>
            <div class="col-sm-4">
                <?= $form->field($model, 'StartTime', [
                    'showLabels' => false
                ])->widget(\kartik\widgets\TimePicker::classname(), [
                    'id' => 'StartTimePicker',
                    'pluginOptions' => ['placeholder' => 'Enter time...',]
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
                    'pluginOptions' => ['placeholder' => 'Enter time...',]
                ]); ?>
            </div>
        </div>

        <div class="row">
            <?= Html::activeLabel($model, 'ChargeOfAccountType', [
                'label' => 'Account Type',
                'class' => 'col-sm-2 control-label'
            ]) ?>
            <div class="col-sm-4">
                <?= $form->field($model, 'ChargeOfAccountType', [
                    'showLabels' => false
                ])->dropDownList($chartOfAccountType,
                     array('prompt'=>'--Select an Account Type--')); ?>
            </div>
        </div>
        <?= Html::activeHiddenInput($model, 'TimeCardID', ['value' => $timeCardID]); ?>
    </div>
    <input type="hidden" name="weekStart" value=<?=Yii::$app->getRequest()->getQueryParam('weekStart') ?> />
    <input type="hidden" name="weekEnd" value=<?=Yii::$app->getRequest()->getQueryParam('weekEnd') ?> />
    <br>
    <br>
    <div class="form-group">
        <?= Html::Button('Submit', ['class' => 'btn btn-success', 'id' => 'create_task_entry_submit_btn', 'disabled' => 'disabled']) ?>
    </div>
    <?php ActiveForm::end(); ?>
    <?php Pjax::end() ?>

    <script>

        $(document).off('mouseleave', '#TaskEntryForm :input').on('mouseleave', '#TaskEntryForm :input', function (){
            if (InputFieldValidator()){
                $('#create_task_entry_submit_btn').prop('disabled', false); 
               }
               else{
                $('#create_task_entry_submit_btn').prop('disabled', true); 
               }
               
        });

        $(document).off('click','#TaskEntryForm .glyphicon-remove').on('click', '#TaskEntryForm .glyphicon-remove', function (){
         
               $('#create_task_entry_submit_btn').prop('disabled', true); 
        });
 

        $('#create_task_entry_submit_btn').click(function (event) {
             if (InputFieldValidator()) {
                TaskEntryCreation();
                $(this).closest('.modal-dialog').parent().modal('hide');//.dialog("close");
                event.preventDefault();
                return false;
            } else {
                $('#create_task_entry_submit_btn').prop('disabled', true);
            }
        });

        function InputFieldValidator() {
			var date = $('#dynamicmodel-date').val();
			var TaskName = $('#dynamicmodel-taskname').val();
			var ChangeOfAccountType = $('#dynamicmodel-chargeofaccounttype').val();
			//convert times to 24
			var StartTime = $('#dynamicmodel-starttime').val();
			var EndTime = $('#dynamicmodel-endtime').val();

            if (date !="" && StartTime != "" &&
                EndTime != "" && TaskName != "" && 
                ChangeOfAccountType != "")
				//>= allows same start and end remove the = if this is not allowed.
				{
               //only convert when not empty
                StartTime = ConvertToTwentyFourHourTime(StartTime);
                EndTime = ConvertToTwentyFourHourTime(EndTime);
                //now compare 
                if(EndTime > StartTime)
                return true;
            } else {
                return false; 
            }
                
        }
        
		
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