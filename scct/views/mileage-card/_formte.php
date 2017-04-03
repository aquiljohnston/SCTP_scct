<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;
use kartik\time\TimePicker;
use kartik\datetime\DateTimePicker;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\MileageCard */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="mileage-entry-form">

    <?php Pjax::begin(['id' => 'mileageEntryModal', 'timeout' => false]) ?>
    <?php $form = ActiveForm::begin([
        'id' => 'MileageEntryForm',//$model->formName(),
        'type' => ActiveForm::TYPE_HORIZONTAL,
        'formConfig' => ['labelSpan' => 1, 'deviceSize' => ActiveForm::SIZE_SMALL],
    ]); ?>
    <div class="form-group kv-fieldset-inline" id="mileage_entry_form">
        <!-- Starting Mileage Field -->
        <?= Html::activeLabel($model, 'MileageEntryStartingMileage', [
            'label' => 'Starting Mileage',
            'class' => 'col-sm-2 control-label'
        ]) ?>
        <div class="col-sm-4">
            <?= $form->field($model, 'MileageEntryStartingMileage', [
                'showLabels' => false
            ])->textInput(['placeholder' => 'Starting Mileage']); ?>
        </div>

        <!-- Ending Mileage Field -->
        <?= Html::activeLabel($model, 'MileageEntryEndingMileage', [
            'label' => 'Ending Mileage',
            'class' => 'col-sm-2 control-label'
        ]) ?>
        <div class="col-sm-4">
            <?= $form->field($model, 'MileageEntryEndingMileage', [
                'showLabels' => false
            ])->textInput(['placeholder' => 'Ending Mileage']); ?>
        </div>

        <!-- Time Picker for Mileage Entry Start Time -->
        <?= Html::activeLabel($model, 'MileageEntryStartDate', [
            'label' => 'Start Time',
            'class' => 'col-sm-2 control-label'
        ]) ?>
        <div class="col-sm-4">
            <?= $form->field($model, 'MileageEntryStartDate', [
                'showLabels' => false
            ])->widget(TimePicker::classname(), [
                'options' => ['placeholder' => 'Enter time...'],
            ]); ?>
        </div>

        <!-- Time Picker for Mileage Entry End Time -->
        <?= Html::activeLabel($model, 'MileageEntryEndDate', [
            'label' => 'End Time',
            'class' => 'col-sm-2 control-label'
        ]) ?>
        <div class="col-sm-4">
            <?= $form->field($model, 'MileageEntryEndDate', [
                'showLabels' => false
            ])->widget(TimePicker::classname(), [
                'options' => ['placeholder' => 'Enter time...'],
            ]); ?>
        </div>

        <!-- Retrieve the User ID that is creating the mileage entry -->
        <?= Html::activeHiddenInput($model, 'MileageEntryCreatedBy', ['value' => Yii::$app->user->identity->id]); ?>
    </div>
    <div class="form-group">
        <?= Html::Button('Submit', ['class' => 'btn btn-success', 'id' => 'mileage_card_submit_btn']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    <?php Pjax::end() ?>

    <script>
        /*$('#mileage_card_submit_btn').click(function () {
            $('#mileage_card_submit_btn').attr('disabled', true)
                .parents('form').submit();
        });*/
        $('#mileage_card_submit_btn').click(function (event) {
            MileageEntryCreation();
            $(this).closest('.modal-dialog').parent().modal('hide');//.dialog("close");
            event.preventDefault();
            return false;
        });
    </script>
</div>

