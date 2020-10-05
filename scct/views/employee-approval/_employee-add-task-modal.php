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
?>
<style type="text/css">
    [data-key="0"] {
        display: none;
    }
</style>

<div class="employee-detail-edit">

    <!-- <input id="SundayDate" type="hidden" name="SundayDate" value=<?php //echo $SundayDateFull; ?>>
    <input id="SaturdayDate" type="hidden" name="SaturdayDate" value=<?php //echo $SaturdayDateFull; ?>> -->


    <!--update form-->
    <?php $form = ActiveForm::begin([
        'id'         => 'EmployeeDetailModalForm',
        'type'       => ActiveForm::TYPE_HORIZONTAL,
        'formConfig' => ['labelSpan' => 1, 'deviceSize' => ActiveForm::SIZE_SMALL],
    ]); ?>
    <div class="form-group kv-fieldset-inline" id="employee_detail_form">
        <div class="row">

            <?php if (count($breakDownData) > 0) {

                $endTime = date('H:i', strtotime($breakDownData[0]['Start Time']));
                $startTime = end($breakDownData);
                $startTime = date('H:i', strtotime($startTime['End Time']));

                $checkboxArr = [
                    $endTime   => 'Morning',
                    $startTime => 'Afternoon'
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
                            ]);
                        }
                    ]); ?>
                </div>
                <div class="clearfix"></div>
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
                    'pluginOptions' => [
                        'placeholder'  => 'Enter time...',
                        'defaultTime'  => false,
                        'showMeridian' => false
                    ],
                    'disabled'      => $model->Task == 'Employee Logout' ? true : false,
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
                    'pluginOptions' => [
                        'placeholder'  => 'Enter time...',
                        'defaultTime'  => false,
                        'showMeridian' => false
                    ],
                    'disabled'      => $model->Task == 'Employee Login' ? true : false,
                ]); ?>
            </div>

        </div>
    </div>
    <br>
    <div id="employeeDetailModalFormButtons" class="form-group" style="display:block">
        <?= Html::Button('Submit', ['class' => 'btn btn-success', 'id' => 'employee_detail_form_submit_btn']) ?>
    </div>
    <?php ActiveForm::end(); ?>
    <input type="hidden" value="<?php echo $userID ?>" id="userID">
    <input type="hidden" value="<?php echo $date ?>" id="date">
</div>

<script>
    //form on project change reload task dropDownList
    $(document).
        off('change', '#employeedetailtime-projectid').
        on('change', '#employeedetailtime-projectid', function() {
            reloadTaskDropdown();
        });

    $body = $('body');

    //
    $body.on('click', '.time-of-day-checkbox', function(e) {

        let timeOfDay = $(this).data('time-of-day');
        let time = $(this).data('time');
        let isChecked = $(this).is(':checked');

        if (timeOfDay == 'morning') {

            if (isChecked) {

                //
                $('#employeedetailtime-endtime').val(time);
                $('#employeedetailtime-endtime').attr('disabled', true);

                //
                $('#employeedetailtime-starttime').val('');
                $('#employeedetailtime-starttime').removeAttr('disabled');
            }

        } else if (timeOfDay == 'afternoon') {

            if (isChecked) {

                //
                $('#employeedetailtime-starttime').val(time);
                $('#employeedetailtime-starttime').attr('disabled', true);

                //
                $('#employeedetailtime-endtime').val('');
                $('#employeedetailtime-endtime').removeAttr('disabled');

            }
        }
    });

    //form pjax reload
    function reloadTaskDropdown() {
        //get current user for project dropdown
        userID = $('#userID').val();
        //fetch formatted form values
        //  data = getFormData();

        $('#loading').show();
        $.pjax.reload({
            type: 'POST',
            replace: false,
            url: '/employee-approval/add-task-modal?userID=' + userID + '&date=' + $('#date').val(),
            data: {projectID: $('#employeedetailtime-projectid').val()},
            container: '#addTaskDropDownPjax',
            timeout: 99999,
        });
        $('#addTaskDropDownPjax').off('pjax:success').on('pjax:success', function() {
            $('#loading').hide();
        });
    }
</script>
