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
					'label'=>'MileageCardTechID', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'MileageCardTechID',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'MileageCardTechID']); ?>
				</div>
				<?= Html::activeLabel($model, 'MileageCardProjectID', [
					'label'=>'MileageCardProjectID', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'MileageCardProjectID',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'MileageCardProjectID']); ?>
				</div>
				<?= Html::activeLabel($model, 'MileageCardType', [
					'label'=>'MileageCardType', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'MileageCardType',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'MileageCardType']); ?>
				</div>
				<?= Html::activeLabel($model, 'MileageCardAppStatus', [
					'label'=>'MileageCardAppStatus', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'MileageCardAppStatus',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'MileageCardAppStatus']); ?>
				</div>
				<?= Html::activeLabel($model, 'MileageCardCreateDate', [
					'label'=>'MileageCardCreateDate', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'MileageCardCreateDate',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'MileageCardCreateDate']); ?>
				</div>
				<?= Html::activeLabel($model, 'MileageCardCreatedBy', [
					'label'=>'MileageCardCreatedBy', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'MileageCardCreatedBy',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'MileageCardCreatedBy']); ?>
				</div>
				<?= Html::activeLabel($model, 'MileageCardModifiedDate', [
					'label'=>'MileageCardModifiedDate', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'MileageCardModifiedDate',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'MileageCardModifiedDate']); ?>
				</div>
				<?= Html::activeLabel($model, 'MileageCardModifiedBy', [
					'label'=>'MileageCardModifiedBy', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'MileageCardModifiedBy',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'MileageCardModifiedBy']); ?>
				</div>
				<?= Html::activeLabel($model, 'MileagCardBusinessMiles', [
					'label'=>'MileagCardBusinessMiles', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'MileagCardBusinessMiles',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'MileagCardBusinessMiles']); ?>
				</div>
				<?= Html::activeLabel($model, 'MileagCardPersonalMiles', [
					'label'=>'MileagCardPersonalMiles', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'MileagCardPersonalMiles',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'MileagCardPersonalMiles']); ?>
				</div>
			</div>	

    <div class="form-group">
        <?= Html::submitButton( 'Submit', ['class' => 'btn btn-success', 'id'=> 'mileage_card_submit_btn']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
