<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\mileagecard */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin([
				'type' => ActiveForm::TYPE_HORIZONTAL,
				'formConfig' => ['labelSpan' => 3, 'deviceSize' => ActiveForm::SIZE_SMALL],
			]); ?>
			<div class="form-group kv-fieldset-inline" id="mileage_card_form">
				<?= Html::activeLabel($model, 'MileageCardTechID', [
					'label'=>'Tech ID', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'MileageCardTechID',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'MileageCardTechID']); ?>
				</div>
				<?= Html::activeLabel($model, 'MileageCardProjectID', [
					'label'=>'Project ID', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'MileageCardProjectID',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'MileageCardProjectID']); ?>
				</div>
				<?= Html::activeLabel($model, 'MileageCardCreateDate', [
					'label'=>'Create Date', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'MileageCardCreateDate',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'MileageCardCreateDate']); ?>
				</div>
				<?= Html::activeLabel($model, 'MileageCardCreatedBy', [
					'label'=>'Created By', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'MileageCardCreatedBy',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'MileageCardCreatedBy']); ?>
				</div>
				<?= Html::activeLabel($model, 'MileageCardModifiedDate', [
					'label'=>'Modified Date', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'MileageCardModifiedDate',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'MileageCardModifiedDate']); ?>
				</div>
				<?= Html::activeLabel($model, 'MileageCardModifiedBy', [
					'label'=>'Modified By', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'MileageCardModifiedBy',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'MileageCardModifiedBy']); ?>
				</div>
				<?= Html::activeLabel($model, 'MileageCardBusinessMiles', [
					'label'=>'Business Miles', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'MileageCardBusinessMiles',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'MileageCardBusinessMiles']); ?>
				</div>
				<?= Html::activeLabel($model, 'MileageCardPersonalMiles', [
					'label'=>'Personal Miles', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'MileageCardPersonalMiles',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'MileageCardPersonalMiles']); ?>
				</div>
			</div>	

    <div class="form-group">
        <?= Html::submitButton( 'Submit', ['class' => 'btn btn-success', 'id'=> 'mileage_card_submit_btn']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
