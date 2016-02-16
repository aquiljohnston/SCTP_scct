<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\EquipmentSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="equipment-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?//= $form->field($model, 'EquipmentID') ?>

    <?= $form->field($model, 'Name') ?>

    <?= $form->field($model, 'Serial Number') ?>

    <?= $form->field($model, 'Details') ?>

    <?= $form->field($model, 'Type') ?>
	
	<?= $form->field($model, 'Client Name') ?>
	
	<?= $form->field($model, 'Project Name') ?>
	
	<?= $form->field($model, 'Accepted Flag') ?>

    <?php // echo $form->field($model, 'EquipmentManufacturer') ?>

    <?php // echo $form->field($model, 'EquipmentManufactureYear') ?>

    <?php // echo $form->field($model, 'EquipmentCondition') ?>

    <?php // echo $form->field($model, 'EquipmentMACID') ?>

    <?php // echo $form->field($model, 'EquipmentModel') ?>

    <?php // echo $form->field($model, 'EquipmentColor') ?>

    <?php // echo $form->field($model, 'EquipmentWarrantyDetail') ?>

    <?php // echo $form->field($model, 'EquipmentComment') ?>

    <?php // echo $form->field($model, 'EquipmentClientID') ?>

    <?php // echo $form->field($model, 'EquipmentProjectID') ?>

    <?php // echo $form->field($model, 'EquipmentAnnualCalibrationDate') ?>

    <?php // echo $form->field($model, 'EquipmentAnnualCalibrationStatus') ?>

    <?php // echo $form->field($model, 'EquipmentAssignedUserID') ?>

    <?php // echo $form->field($model, 'EquipmentCreatedByUser') ?>

    <?php // echo $form->field($model, 'EquipmentCreateDate') ?>

    <?php // echo $form->field($model, 'EquipmentModifiedBy') ?>

    <?php // echo $form->field($model, 'EquipmentModifiedDate') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
