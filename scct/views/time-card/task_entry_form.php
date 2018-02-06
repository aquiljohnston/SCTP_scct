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
        'action' => Url::to('/time-card/add-task-entry'),
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
                           
                            if (Date.length != 0 && StartTime.length != 0 && EndTime.length != 0 && TaskName.length != 0 && ChangeOfAccountType.length != 0){
                                $("#create_task_entry_submit_btn").prop("disabled", false);
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
                <?= $form->field($model, 'TaskName', [
                    'showLabels' => false
                ])->dropDownList($allTask); ?>
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
                ])->dropDownList($chartOfAccountType); ?>
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
        console.log("Date: " + $('#dynamicmodel-date').val() + " Start Time: " + $('#dynamicmodel-starttime').val() +" End Time: " + $('#dynamicmodel-endtime').val() +" Task Name: "+$('#dynamicmodel-taskname').val() +" Account Type: "+$('#dynamicmodel-chargeofaccounttype').val());
        var date = $('#dynamicmodel-date').val();
        var StartTime = $('#dynamicmodel-starttime').val();
        var EndTime = $('#dynamicmodel-endtime').val();
        var TaskName = $('#dynamicmodel-taskname').val();
        var ChangeOfAccountType = $('#dynamicmodel-chargeofaccounttype').val();

        $(document).off('click', '#dynamicmodel-taskname').on('click', '#dynamicmodel-taskname', function (){
            if (InputFieldValidator)
                $('#create_task_entry_submit_btn').prop('disabled', false);
        });
        $(document).off('click', '#dynamicmodel-chargeofaccounttype').on('click', '#dynamicmodel-chargeofaccounttype', function (){
            if (InputFieldValidator)
                $('#create_task_entry_submit_btn').prop('disabled', false);
        });

        $('#create_task_entry_submit_btn').click(function (event) {
            console.log("SUBMIT CLICKED !");
            console.log("Date: " + $('#dynamicmodel-date').val() + " Start Time: " + $('#dynamicmodel-starttime').val() +" End Time: " + $('#dynamicmodel-endtime').val() +" Task Name: "+$('#dynamicmodel-taskname').val() +" Account Type: "+$('#dynamicmodel-chargeofaccounttype').val());
            if (InputFieldValidator) {
                TaskEntryCreation();
                $(this).closest('.modal-dialog').parent().modal('hide');//.dialog("close");
                event.preventDefault();
                return false;
            } else {
                $('#create_task_entry_submit_btn').prop('disabled', true);
            }
        });

        function InputFieldValidator() {
            if (date != null && date.length != 0 && StartTime != null && EndTime != null && TaskName != null && ChangeOfAccountType != null)
                return true;
            else
                return false;
        }
    </script>
</div>