<?php

use kartik\form\ActiveForm;
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

    <!--update form-->
    <?php $form = ActiveForm::begin([
        'id' => 'EmployeeDetailModalForm',
        'type' => ActiveForm::TYPE_HORIZONTAL,
        'formConfig' => ['labelSpan' => 1, 'deviceSize' => ActiveForm::SIZE_SMALL],
    ]); ?>
    <div class="form-group kv-fieldset-inline" id="employee_detail_form">
        <div class="row">
            <!--Prev Row Data-->

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

            <?php Pjax::begin(['id' => 'addTaskDropDownPjax', 'timeout' => false]) ?>
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

            <?php  Pjax::end() ?>
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

        </div>
    </div>
    <br>
    <div id="employeeDetailModalFormButtons" class="form-group" style="display:block">
        <?= Html::Button('Submit', ['class' => 'btn btn-success', 'id' => 'employee_detail_form_submit_btn']) ?>
    </div>
    <?php ActiveForm::end(); ?>
    <input type="hidden" value="<?php echo $userID?>" id="userID">
</div>

<script>
    //form on project change reload task dropDownList
    $(document).off('change', '#employeedetailtime-projectid').on('change', '#employeedetailtime-projectid', function (){
       reloadTaskDropdown();
    });

    //form pjax reload
    function reloadTaskDropdown(){
        //get current user for project dropdown
        userID = $('#userID').val();
        //fetch formatted form values
      //  data = getFormData();

        $('#loading').show();
        $.pjax.reload({
            type: 'POST',
            replace: false,
            url: '/employee-approval/add-task-modal?userID=' + userID,
            data: {projectID: $('#employeedetailtime-projectid').val()},
            container: '#addTaskDropDownPjax',
            timeout: 99999
        });
        $('#addTaskDropDownPjax').off('pjax:success').on('pjax:success', function () {
            $('#loading').hide();
        });
    }
</script>
