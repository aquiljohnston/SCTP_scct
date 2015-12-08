<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\MileageCardSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="mileage-card-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'MileageCardID') ?>

    <?= $form->field($model, 'MileageCardEmpID') ?>

    <?= $form->field($model, 'MileageCardTechID') ?>

    <?= $form->field($model, 'MileageCardProjectID') ?>

    <?= $form->field($model, 'MileageCardType') ?>

    <?php // echo $form->field($model, 'MileageCardAppStatus') ?>

    <?php // echo $form->field($model, 'MileageCardCreateDate') ?>

    <?php // echo $form->field($model, 'MileageCardCreatedBy') ?>

    <?php // echo $form->field($model, 'MileageCardModifiedDate') ?>

    <?php // echo $form->field($model, 'MileageCardModifiedBy') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
