<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\equipment */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="equipment-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'EquipmentName')->textInput() ?>

    <?= $form->field($model, 'EquipmentSerialNumber')->textInput() ?>

    <?= $form->field($model, 'EquipmentDetails')->textInput() ?>

    <?= $form->field($model, 'EquipmentType')->textInput() ?>

    <?= $form->field($model, 'EquipmentManufacturer')->textInput() ?>

    <?= $form->field($model, 'EquipmentManufactureYear')->textInput() ?>

    <?= $form->field($model, 'EquipmentCondition')->textInput() ?>

    <?= $form->field($model, 'EquipmentMACID')->textInput() ?>

    <?= $form->field($model, 'EquipmentModel')->textInput() ?>

    <?= $form->field($model, 'EquipmentColor')->textInput() ?>

    <?= $form->field($model, 'EquipmentWarrantyDetail')->textInput() ?>

    <?= $form->field($model, 'EquipmentComment')->textInput() ?>

    <?= $form->field($model, 'EquipmentClientID')->textInput() ?>

    <?= $form->field($model, 'EquipmentProjectID')->textInput() ?>

    <?= $form->field($model, 'EquipmentAnnualCalibrationDate')->textInput() ?>

    <?= $form->field($model, 'EquipmentAnnualCalibrationStatus')->textInput() ?>

    <?= $form->field($model, 'EquipmentAssignedUserID')->textInput() ?>

    <?= $form->field($model, 'EquipmentCreatedByUser')->textInput() ?>

    <?= $form->field($model, 'EquipmentCreateDate')->textInput() ?>

    <?= $form->field($model, 'EquipmentModifiedBy')->textInput() ?>

    <?= $form->field($model, 'EquipmentModifiedDate')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
