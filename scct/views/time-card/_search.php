<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\TimeCardSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="time-card-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'TimeCardID') ?>

    <?= $form->field($model, 'TimeCardStartDate') ?>

    <?= $form->field($model, 'TimeCardEndDate') ?>

    <?= $form->field($model, 'TimeCardHoursWorked') ?>

    <?= $form->field($model, 'TimeCardProjectID') ?>

    <?php // echo $form->field($model, 'TimeCardTechID') ?>

    <?php // echo $form->field($model, 'TimeCardApproved') ?>

    <?php // echo $form->field($model, 'TimeCardSupervisorName') ?>

    <?php // echo $form->field($model, 'TimeCardComment') ?>

    <?php // echo $form->field($model, 'TimeCardCreateDate') ?>

    <?php // echo $form->field($model, 'TimeCardCreatedBy') ?>

    <?php // echo $form->field($model, 'TimeCardModifiedDate') ?>

    <?php // echo $form->field($model, 'TimeCardModifiedBy') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
