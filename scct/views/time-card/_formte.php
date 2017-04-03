<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;
use kartik\time\TimePicker;
use kartik\datetime\DateTimePicker;
use yii\widgets\Pjax;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\TimeCard */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="time-entry-form">
    <?php Pjax::begin(['id' => 'timeEntryModal', 'timeout' => false]) ?>
    <?php $form = ActiveForm::begin([
        'id' => 'TimeEntryForm',//$model->formName(),
        'type' => ActiveForm::TYPE_HORIZONTAL,
        //'action' => Url::to('/time-card/create-time-entry'),
        'formConfig' => ['labelSpan' => 1, 'deviceSize' => ActiveForm::SIZE_SMALL],
    ]); ?>
    <div class="form-group kv-fieldset-inline" id="time_entry_form">
        <?= Html::activeLabel($model, 'TimeEntryStartTime', [
            'label' => 'Start Time',
            'class' => 'col-sm-2 control-label'
        ]) ?>
        <div class="col-sm-4">
            <?= $form->field($model, 'TimeEntryStartTime', [
                'showLabels' => false
            ])->widget(TimePicker::classname(), [
                'options' => ['placeholder' => 'Enter time...'],
            ]); ?>
        </div>

        <?= Html::activeLabel($model, 'TimeEntryEndTime', [
            'label' => 'End Time',
            'class' => 'col-sm-2 control-label'
        ]) ?>
        <div class="col-sm-4">
            <?= $form->field($model, 'TimeEntryEndTime', [
                'showLabels' => false
            ])->widget(TimePicker::classname(), [
                'options' => ['placeholder' => 'Enter time...'],
            ]); ?>
        </div>

        <?= Html::activeLabel($activityModel, 'ActivityCode', [
            'label' => 'Activity Type',
            'class' => 'col-sm-2 control-label'
        ]) ?>
        <div class="col-sm-4">
            <?= $form->field($activityModel, 'ActivityCode', [
                'showLabels' => false
            ])->dropDownList($activityCode); ?>
        </div>

        <?= Html::activeLabel($model, 'TimeEntryComment', [
            'label' => 'Comment',
            'class' => 'col-sm-2 control-label'
        ]) ?>
        <div class="col-sm-4">
            <?= $form->field($model, 'TimeEntryComment', [
                'showLabels' => false
            ])->textInput(['placeholder' => 'TimeEntryComment']); ?>
        </div>

        <?= Html::activeLabel($activityModel, 'ActivityPayCode', [
            'label' => 'Activity Pay Code',
            'class' => 'col-sm-2 control-label'
        ]) ?>
        <div class="col-sm-4">
            <?= $form->field($activityModel, 'ActivityPayCode', [
                'showLabels' => false
            ])->dropDownList($activityPayCode); ?>
        </div>

        <!--div class="col-sm-4">
					<? /*= $form->field($model, 'TimeEntryDate')
					  ->hiddenInput(['value' =>$model["TimeEntryStartTime"]])
					  ->label(false);

					*/ ?>
				</div-->

        <?= Html::activeHiddenInput($model, 'TimeEntryCreatedBy', ['value' => Yii::$app->user->identity->id]); ?>
    </div>
    <br>
    <br>
    <div class="form-group">
        <?= Html::Button('Submit', ['class' => 'btn btn-success', 'id' => 'time_card_submit_btn']) ?>
    </div>
    <?php ActiveForm::end(); ?>
    <?php Pjax::end() ?>

    <script>
        $('#time_card_submit_btn').click(function (event) {
            TimeEntryCreation();
            $(this).closest('.modal-dialog').parent().modal('hide');//.dialog("close");
            event.preventDefault();
            return false;
        });
    </script>
</div>

