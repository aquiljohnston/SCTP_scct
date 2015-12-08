<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\TimeCard */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="time-card-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'TimeCardStartDate')->textInput() ?>

    <?= $form->field($model, 'TimeCardEndDate')->textInput() ?>

    <?= $form->field($model, 'TimeCardHoursWorked')->textInput() ?>

    <?= $form->field($model, 'TimeCardProjectID')->textInput() ?>

    <?= $form->field($model, 'TimeCardTechID')->textInput() ?>

    <?= $form->field($model, 'TimeCardApproved')->textInput() ?>

    <?= $form->field($model, 'TimeCardSupervisorName')->textInput() ?>

    <?= $form->field($model, 'TimeCardComment')->textInput() ?>

    <?= $form->field($model, 'TimeCardCreateDate')->textInput() ?>

    <?= $form->field($model, 'TimeCardCreatedBy')->textInput() ?>

    <?= $form->field($model, 'TimeCardModifiedDate')->textInput() ?>

    <?= $form->field($model, 'TimeCardModifiedBy')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
