<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\project */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="project-form">

    <?php $form = ActiveForm::begin([
				'type' => ActiveForm::TYPE_HORIZONTAL,
				'formConfig' => ['labelSpan' => 3, 'deviceSize' => ActiveForm::SIZE_SMALL],
			]); ?>
			<div class="form-group kv-fieldset-inline" id="project_form">
				<?= Html::activeLabel($model, 'IsEndOfDayTaskOut', [
					'label'=>'End of Day Task Out',
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'IsEndOfDayTaskOut',[
						'showLabels'=>false
					])->dropDownList($flag); ?>
				</div>
			</div>

    <div class="form-group" id="">
       <?= Html::submitButton( 'Submit', ['class' => 'btn btn-success', 'id'=> 'project_submit_btn']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
