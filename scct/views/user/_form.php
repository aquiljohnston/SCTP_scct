<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\user */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'UserName')->textInput() ?>

    <?= $form->field($model, 'UserFirstName')->textInput() ?>

    <?= $form->field($model, 'UserLastName')->textInput() ?>

    <?= $form->field($model, 'UserLoginID')->textInput() ?>

    <?= $form->field($model, 'UserEmployeeType')->textInput() ?>

    <?= $form->field($model, 'UserPhone')->textInput() ?>

    <?= $form->field($model, 'UserCompanyName')->textInput() ?>

    <?= $form->field($model, 'UserCompanyPhone')->textInput() ?>

    <?= $form->field($model, 'UserAppRoleType')->textInput() ?>

    <?= $form->field($model, 'UserComments')->textInput() ?>

    <?= $form->field($model, 'UserKey')->textInput() ?>

    <?= $form->field($model, 'UserActiveFlag')->textInput() ?>

    <?= $form->field($model, 'UserCreatedDate')->textInput() ?>

    <?= $form->field($model, 'UserModifiedDate')->textInput() ?>

    <?= $form->field($model, 'UserCreatedBy')->textInput() ?>

    <?= $form->field($model, 'UserModifiedBy')->textInput() ?>

    <?= $form->field($model, 'UserCreateDTLTOffset')->textInput() ?>

    <?= $form->field($model, 'UserModifiedDTLTOffset')->textInput() ?>

    <?= $form->field($model, 'UserInactiveDTLTOffset')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton( 'Submit', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
