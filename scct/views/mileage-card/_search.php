<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'MileageCardID')->textInput() ?>

    <?= $form->field($model, 'MileageCardEmpID')->textInput() ?>

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
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
