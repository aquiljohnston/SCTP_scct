<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\mileagecard */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'MileageCardTechID')->textInput() ?>

    <?= $form->field($model, 'MileageCardProjectID')->textInput() ?>

    <?= $form->field($model, 'MileageCardType')->textInput() ?>

    <?= $form->field($model, 'MileageCardAppStatus')->textInput() ?>

    <?= $form->field($model, 'MileageCardCreateDate')->textInput() ?>

    <?= $form->field($model, 'MileageCardCreatedBy')->textInput() ?>

    <?= $form->field($model, 'MileageCardModifiedDate')->textInput() ?>

    <?= $form->field($model, 'MileageCardModifiedBy')->textInput() ?>

    <?= $form->field($model, 'MileagCardBusinessMiles')->textInput() ?>

    <?= $form->field($model, 'MileagCardPersonalMiles')->textInput() ?>


    <div class="form-group">
        <?= Html::submitButton( 'Create', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
