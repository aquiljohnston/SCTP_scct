<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\project */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="project-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'ProjectName')->textInput() ?>

    <?= $form->field($model, 'ProjectDescription')->textInput() ?>

    <?= $form->field($model, 'ProjectNotes')->textInput() ?>

    <?= $form->field($model, 'ProjectType')->textInput() ?>

    <?= $form->field($model, 'ProjectStatus')->textInput() ?>

    <?= $form->field($model, 'ProjectClientID')->textInput() ?>

    <?= $form->field($model, 'ProjectStartDate')->textInput() ?>

    <?= $form->field($model, 'ProjectEndDate')->textInput() ?>

    <div class="form-group">
       <?= Html::submitButton( 'Submit', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
