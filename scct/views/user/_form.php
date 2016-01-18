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
					'label'=>'UserName', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'UserName',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'UserName']); ?>
				</div>
				<?= Html::activeLabel($model, 'Password', [
					'label'=>'Password', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'Password',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'Password']); ?>
				</div>
				<?= Html::activeLabel($model, 'UserFirstName', [
					'label'=>'UserFirstName', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'UserFirstName',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'UserFirstName']); ?>
				</div>
				<?= Html::activeLabel($model, 'UserLastName', [
					'label'=>'UserLastName', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'UserLastName',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'UserLastName']); ?>
				</div>
				<?= Html::activeLabel($model, 'UserEmployeeType', [
					'label'=>'UserEmployeeType', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'UserEmployeeType',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'UserEmployeeType']); ?>
				</div>
				<?= Html::activeLabel($model, 'UserPhone', [
					'label'=>'UserPhone', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'UserPhone',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'UserPhone']); ?>
				</div>
				<?= Html::activeLabel($model, 'UserCompanyName', [
					'label'=>'UserCompanyName', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'UserCompanyName',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'UserCompanyName']); ?>
				</div>
				<?= Html::activeLabel($model, 'UserCompanyPhone', [
					'label'=>'UserCompanyPhone', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'UserCompanyPhone',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'UserCompanyPhone']); ?>
				</div>
				<?= Html::activeLabel($model, 'UserAppRoleType', [
					'label'=>'UserAppRoleType', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'UserAppRoleType',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'UserAppRoleType']); ?>
				</div>
				<?= Html::activeLabel($model, 'UserComments', [
					'label'=>'UserComments', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'UserComments',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'UserComments']); ?>
				</div>
				<?= Html::activeLabel($model, 'UserActiveFlag', [
					'label'=>'UserActiveFlag', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'UserActiveFlag',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'UserActiveFlag']); ?>
				</div>
				<?= Html::activeLabel($model, 'UserCreatedDate', [
					'label'=>'UserCreatedDate', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'UserCreatedDate',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'UserCreatedDate']); ?>
				</div>
				<?= Html::activeLabel($model, 'UserModifiedDate', [
					'label'=>'UserModifiedDate', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'UserModifiedDate',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'UserModifiedDate']); ?>
				</div>
				<?= Html::activeLabel($model, 'UserCreatedBy', [
					'label'=>'UserCreatedBy', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'UserCreatedBy',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'UserCreatedBy']); ?>
				</div>
				<?= Html::activeLabel($model, 'UserModifiedBy', [
					'label'=>'UserModifiedBy', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'UserModifiedBy',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'UserModifiedBy']); ?>
				</div>
				<?= Html::activeLabel($model, 'UserCreateDTLTOffset', [
					'label'=>'UserCreateDTLTOffset', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'UserCreateDTLTOffset',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'UserCreateDTLTOffset']); ?>
				</div>
				<?= Html::activeLabel($model, 'UserModifiedDTLTOffset', [
					'label'=>'UserModifiedDTLTOffset', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'UserModifiedDTLTOffset',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'UserModifiedDTLTOffset']); ?>
				</div>
				<?= Html::activeLabel($model, 'UserInactiveDTLTOffset', [
					'label'=>'UserInactiveDTLTOffset', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'UserInactiveDTLTOffset',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'UserInactiveDTLTOffset']); ?>
				</div>
			</div>	

    <div class="form-group">
        <?= Html::submitButton( 'Submit', ['class' => 'btn btn-success', 'id'=> 'user_mgt_submit_btn']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
