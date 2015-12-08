<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\MileageCard */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="mileage-card-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'MileageCardEmpID')->textInput() ?>

    <?= $form->field($model, 'MileageCardTechID')->textInput() ?>

    <?= $form->field($model, 'MileageCardProjectID')->textInput() ?>

    <?= $form->field($model, 'MileageCardType')->textInput() ?>

    <?= $form->field($model, 'MileageCardAppStatus')->textInput() ?>

    <?= $form->field($model, 'MileageCardCreateDate')->textInput() ?>

    <?= $form->field($model, 'MileageCardCreatedBy')->textInput() ?>

    <?= $form->field($model, 'MileageCardModifiedDate')->textInput() ?>

    <?= $form->field($model, 'MileageCardModifiedBy')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
