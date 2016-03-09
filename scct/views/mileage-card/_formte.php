<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\MileageCard */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="mileage-entry-form">

    <?php $form = ActiveForm::begin([
				'id' => $model->formName(),
				'type' => ActiveForm::TYPE_HORIZONTAL,
				'formConfig' => ['labelSpan' => 1, 'deviceSize' => ActiveForm::SIZE_SMALL],
			]); ?>
			<div class="form-group kv-fieldset-inline" id="mileage_entry_form">
				<?= Html::activeLabel($model, 'MileageEntryStartingMileage', [
					'label'=>'Start Mileage', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'MileageEntryStartingMileage',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'Starting Mileage']); ?>
				</div>
				<?= Html::activeLabel($model, 'MileageEntryEndingMileage', [
					'label'=>'End Mileage', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'MileageEntryEndingMileage',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'Ending Mileage']); ?>
				</div>				
			
				<!--div class="col-sm-2">
					<?= $form->field($model, 'MileageEntryMileageCardID',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'MileageEntryMileageCardID']); ?>
				</div-->
				
				<!--div class="col-sm-2">
					<?= $form->field($model, 'MileageEntryActivityID',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'MileageEntryActivityID']); ?>
				</div-->

				<!--div class="col-sm-2">
					<?= $form->field($model, 'MileageEntryCreatedBy',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'Created By']); ?>
				</div>
			</div>

    <div class="form-group">
        <?= Html::submitButton( 'Submit', ['class' => 'btn btn-success', 'id'=> 'mileage_card_submit_btn']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

