<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\user */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="reset-form">

	<?php $form = ActiveForm::begin([
				'type' => ActiveForm::TYPE_VERTICAL,
				'formConfig' => ['labelSpan' => 3, 'deviceSize' => ActiveForm::SIZE_SMALL],
			]); ?>
			<div class="form-group kv-fieldset-inline" id="reset_pass_form">
				<?= Html::activeLabel($model, 'UserName', [
					'label'=>'Username',
					'class'=>'col-sm-3 control-label'
				]) ?>
				<div class="col-sm-9">
					<?= $form->field($model, 'UserName',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'Username']); ?>
				</div>
				<?= Html::activeLabel($model, 'Password', [
					'label'=>'Password', 
					'class'=>'col-sm-3 control-label'
				]) ?>
				<div class="col-sm-9">
					<?= $form->field($model, 'Password',[
						'showLabels'=>false
					])->passwordInput(['placeholder'=>'Password']); ?>
				</div>
				<?= Html::activeLabel($model, 'NewPassword', [
					'label'=>'New Password', 
					'class'=>'col-sm-3 control-label'
				]) ?>
				<div class="col-sm-9">
					<?= $form->field($model, 'NewPassword',[
						'showLabels'=>false
					])->passwordInput(['placeholder'=>'New Password']); ?>
				</div>
			</div>	

	<div class="form-group">
		<?= Html::submitButton( 'Submit', ['class' => 'btn btn-success', 'id'=> 'reset_pass_submit_btn']) ?>
	</div>
	<?php ActiveForm::end(); ?>
</div>