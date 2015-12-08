<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ClientSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="client-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'ClientID') ?>

    <?= $form->field($model, 'ClientName') ?>

    <?= $form->field($model, 'ClientContactTitle') ?>

    <?= $form->field($model, 'ClientContactFName') ?>

    <?= $form->field($model, 'ClientContactMI') ?>

    <?php // echo $form->field($model, 'ClientContactLName') ?>

    <?php // echo $form->field($model, 'ClientPhone') ?>

    <?php // echo $form->field($model, 'ClientEmail') ?>

    <?php // echo $form->field($model, 'ClientAddr1') ?>

    <?php // echo $form->field($model, 'ClientAddr2') ?>

    <?php // echo $form->field($model, 'ClientCity') ?>

    <?php // echo $form->field($model, 'ClientState') ?>

    <?php // echo $form->field($model, 'ClientZip4') ?>

    <?php // echo $form->field($model, 'ClientTerritory') ?>

    <?php // echo $form->field($model, 'ClientActiveFlag') ?>

    <?php // echo $form->field($model, 'ClientDivisionsFlag') ?>

    <?php // echo $form->field($model, 'ClientComment') ?>

    <?php // echo $form->field($model, 'ClientCreateDate') ?>

    <?php // echo $form->field($model, 'ClientCreatorUserID') ?>

    <?php // echo $form->field($model, 'ClientModifiedDate') ?>

    <?php // echo $form->field($model, 'ClientModifiedBy') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
