<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\UserSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'UserID') ?>

    <?= $form->field($model, 'UserName') ?>

    <?= $form->field($model, 'UserFirstName') ?>

    <?= $form->field($model, 'UserLastName') ?>

    <?= $form->field($model, 'UserLoginID') ?>

    <?php // echo $form->field($model, 'UserEmployeeType') ?>

    <?php // echo $form->field($model, 'UserPhone') ?>

    <?php // echo $form->field($model, 'UserCompanyName') ?>

    <?php // echo $form->field($model, 'UserCompanyPhone') ?>

    <?php // echo $form->field($model, 'UserAppRoleType') ?>

    <?php // echo $form->field($model, 'UserComments') ?>

    <?php // echo $form->field($model, 'UserKey') ?>

    <?php // echo $form->field($model, 'UserActiveFlag') ?>

    <?php // echo $form->field($model, 'UserCreatedDate') ?>

    <?php // echo $form->field($model, 'UserModifiedDate') ?>

    <?php // echo $form->field($model, 'UserCreatedBy') ?>

    <?php // echo $form->field($model, 'UserModifiedBy') ?>

    <?php // echo $form->field($model, 'UserCreateDTLTOffset') ?>

    <?php // echo $form->field($model, 'UserModifiedDTLTOffset') ?>

    <?php // echo $form->field($model, 'UserInactiveDTLTOffset') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
