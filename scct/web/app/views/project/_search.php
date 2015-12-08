<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ProjectSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="project-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'ProjectID') ?>

    <?= $form->field($model, 'ProjectName') ?>

    <?= $form->field($model, 'ProjectDescription') ?>

    <?= $form->field($model, 'ProjectNotes') ?>

    <?= $form->field($model, 'ProjectType') ?>

    <?php // echo $form->field($model, 'ProjectStatus') ?>

    <?php // echo $form->field($model, 'ProjectClientID') ?>

    <?php // echo $form->field($model, 'ProjectStartDate') ?>

    <?php // echo $form->field($model, 'ProjectEndDate') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
