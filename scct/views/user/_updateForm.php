<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\user */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin([
				'type' => ActiveForm::TYPE_HORIZONTAL,
				'formConfig' => ['labelSpan' => 3, 'deviceSize' => ActiveForm::SIZE_SMALL],
			]); ?>
			<div class="form-group kv-fieldset-inline" id="user_mgt_form">
				<?= Html::activeLabel($model, 'UserName', [
					'label'=>'Username', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'UserName',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'Username', 'readonly' => true]); ?>
				</div>
				<?= Html::activeLabel($model, 'UserPassword', [
					'label'=>'Password', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'UserPassword',[
						'showLabels'=>false
					])->passwordInput(['placeholder'=>'Password']); ?>
				</div>
				<?= Html::activeLabel($model, 'UserFirstName', [
					'label'=>'First Name', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'UserFirstName',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'UserFirstName']); ?>
				</div>
				<?= Html::activeLabel($model, 'UserLastName', [
					'label'=>'Last Name', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'UserLastName',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'UserLastName']); ?>
				</div>
				<?= Html::activeLabel($model, 'UserEmployeeType', [
					'label'=>'Employee Type', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'UserEmployeeType',[
						'showLabels'=>false
					])->dropDownList($types); ?>
				</div>
				<?= Html::activeLabel($model, 'UserPhone', [
					'label'=>'Phone', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'UserPhone',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'UserPhone']); ?>
				</div>
				<?= Html::activeLabel($model, 'UserCompanyName', [
					'label'=>'Company Name', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'UserCompanyName',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'UserCompanyName']); ?>
				</div>
				<?= Html::activeLabel($model, 'UserCompanyPhone', [
					'label'=>'Company Phone', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'UserCompanyPhone',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'UserCompanyPhone']); ?>
				</div>
				<?= Html::activeLabel($model, 'UserAppRoleType', [
					'label'=>'App Role Type', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'UserAppRoleType',[
						'showLabels'=>false
					])->dropDownList($roles); ?>
				</div>
				<?= Html::activeLabel($model, 'UserComments', [
					'label'=>'Comments', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'UserComments',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'UserComments']); ?>
				</div>
				<?= Html::activeLabel($model, 'UserActiveFlag', [
					'label'=>'Active Flag', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'UserActiveFlag',[
						'showLabels'=>false
					])->dropDownList($flag); ?>
				</div>
			</div>	

    <div class="form-group">
        <?= Html::submitButton( 'Submit', ['class' => 'btn btn-success', 'id'=> 'user_mgt_submit_btn']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
