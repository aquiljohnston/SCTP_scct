<?php

use app\models\EmployeeDetailTime;
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
/* @var $breakDownData array */
/* @var $model EmployeeDetailTime */
/* @var  $projectDropDown array */
/* @var $taskDropDown array */
/* @var $userID int */
/* @var $date string */

?>
<style type="text/css">
    [data-key="0"] {
        display: none;
    }
</style>

<div class="employee-detail-edit">


    <!--update form-->
    <?php $form = ActiveForm::begin([
        'id'         => 'EmployeeDetailAddTaskModalForm',
        'type'       => ActiveForm::TYPE_HORIZONTAL,
        'formConfig' => ['labelSpan' => 1, 'deviceSize' => ActiveForm::SIZE_SMALL],
    ]); ?>
    <div class="form-group kv-fieldset-inline" id="employee_detail_form">
        <div class="row">

            <?php if (count($breakDownData) > 0) {

                $startTimeArr = [];
                $endTimeArr = [];
                foreach ($breakDownData as $breakDown) {
                    $startTimeArr[strtotime($breakDown['Start Time'])] = $breakDown['Start Time'];
                    $endTimeArr[strtotime($breakDown['End Time'])] = $breakDown['End Time'];
                }

                krsort($startTimeArr);
                krsort($endTimeArr);

                $startTime = $startTimeArr[array_key_first($startTimeArr)];
                $endTime = end($endTimeArr);


                $checkboxArr = [
                    $endTime   => ucfirst(EmployeeDetailTime::TIME_OF_DAY_MORNING),
                    $startTime => ucfirst(EmployeeDetailTime::TIME_OF_DAY_AFTERNOON)
                ];
                ?>
                <div class="col-sm-12 text-center">
                    <?= Html::activeRadioList($model, 'TimeOfDay', $checkboxArr, [
                        'item' => function ($index, $label, $name, $checked, $value) use ($model) {
                            return Html::radio($name, false, [
                                    'value'            => $value,
                                    'label'            => Html::encode($label) . ' (' . $value . ')',
                                    'class'            => 'time-of-day-checkbox',
                                    'data-time'        => $value,
                                    'data-time-of-day' => strtolower($label)
                                ]) . '<span style="margin-right:10px;"></span>';
                        }
                    ]); ?>
                </div>
                <div class="clearfix"></div>
                <p style="margin-top:35px;"></p>
            <?php } ?>

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
            <?= Html::activeLabel($model, 'TaskName', [
                'label' => 'Task',
                'class' => 'col-sm-2 control-label'
            ]) ?>
            <div class="col-sm-4">
                <?= $form->field($model, 'TaskID', [
                    'showLabels' => false
                ])->dropDownList($taskDropDown,
                    [
                        'readonly' => $model->TaskName == 'Employee Logout' || $model->TaskName == 'Employee Login' ? true : false,
                        'prompt'   => 'Select a Task'
                    ]); ?>
            </div>

            <?php Pjax::end() ?>
            <?= Html::activeLabel($model, 'StartTime', [
                'label' => 'Start Time',
                'class' => 'col-sm-2 control-label'
            ]) ?>
            <div class="col-sm-4">

                <?php
                $disableStartTime = false;
                if ($model->TaskName == 'Employee Logout') {
                    $disableStartTime = true;
                } else {
                    $disableStartTime = true;
                }
                ?>
                <?= $form->field($model, 'StartTime', [
                    'showLabels' => false,

                ])->widget(\kartik\widgets\TimePicker::classname(), [
                    'pluginOptions' => [
                        'placeholder'  => 'Enter time...',
                        'defaultTime'  => false,
                        'showMeridian' => false
                    ],
                    'disabled'      => $disableStartTime,
                ]); ?>
            </div>
            <?= Html::activeLabel($model, 'EndTime', [
                'label' => 'End Time',
                'class' => 'col-sm-2 control-label'
            ]) ?>
            <div class="col-sm-4">

                <?php
                $disableEndTime = false;
                if ($model->TaskName == 'Employee Login') {
                    $disableEndTime = true;
                } else {
                    $disableEndTime = true;
                }
                ?>
                <?= $form->field($model, 'EndTime', [
                    'showLabels' => false,
                    //'disabled'   => true
                ])->widget(\kartik\widgets\TimePicker::classname(), [
                    'pluginOptions' => [
                        'placeholder'  => 'Enter time...',
                        'defaultTime'  => false,
                        'showMeridian' => false
                    ],
                    'disabled'      => $disableEndTime
                ]); ?>
            </div>

        </div>
    </div>

    <br>
    <div class="row">
        <div class="col-sm-12 text-center">
            <div id="employeeDetailModalFormButtons" class="form-group" style="display:block">
                <?= Html::Button('Submit',
                    [
                        'class'    => 'btn btn-success',
                        'id'       => 'employee_detail_add_task_submit_btn',
                        'disabled' => true
                    ]) ?>
            </div>
        </div>
    </div>


    <?= $form->field($model, 'TaskName')->hiddenInput()->label(false); ?>
    <?= $form->field($model, 'TimeOfDayName')->hiddenInput()->label(false); ?>
    <input type="hidden" value="<?php echo $userID ?>" id="userID">
    <input type="hidden" value="<?php echo $date ?>" id="date">
    <?php ActiveForm::end(); ?>

</div>


