<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\TimeCard */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="time-card-form">

    <?php $form = ActiveForm::begin([
				'type' => ActiveForm::TYPE_HORIZONTAL,
				'formConfig' => ['labelSpan' => 3, 'deviceSize' => ActiveForm::SIZE_SMALL],
			]); ?>
			<div class="form-group kv-fieldset-inline" id="time_card_form">
				<?= Html::activeLabel($model, 'TimeCardStartDate', [
					'label'=>'TimeCardStartDate', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'TimeCardStartDate',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'TimeCardStartDate']); ?>
				</div>
				<?= Html::activeLabel($model, 'TimeCardEndDate', [
					'label'=>'TimeCardEndDate', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'TimeCardEndDate',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'TimeCardEndDate']); ?>
				</div>
				<?= Html::activeLabel($model, 'TimeCardHoursWorked', [
					'label'=>'TimeCardHoursWorked', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'TimeCardHoursWorked',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'TimeCardHoursWorked']); ?>
				</div>
				<?= Html::activeLabel($model, 'TimeCardProjectID', [
					'label'=>'TimeCardProjectID', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'TimeCardProjectID',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'TimeCardProjectID']); ?>
				</div>
				<?= Html::activeLabel($model, 'TimeCardTechID', [
					'label'=>'TimeCardTechID', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'TimeCardTechID',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'TimeCardTechID']); ?>
				</div>
				<?= Html::activeLabel($model, 'TimeCardApproved', [
					'label'=>'TimeCardApproved', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'TimeCardApproved',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'TimeCardApproved']); ?>
				</div>
				<?= Html::activeLabel($model, 'TimeCardSupervisorName', [
					'label'=>'TimeCardSupervisorName', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'TimeCardSupervisorName',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'TimeCardSupervisorName']); ?>
				</div>
				<?= Html::activeLabel($model, 'TimeCardComment', [
					'label'=>'TimeCardComment', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'TimeCardComment',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'TimeCardComment']); ?>
				</div>
				<?= Html::activeLabel($model, 'TimeCardCreateDate', [
					'label'=>'TimeCardCreateDate', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'TimeCardCreateDate',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'TimeCardCreateDate']); ?>
				</div>
				<?= Html::activeLabel($model, 'TimeCardCreatedBy', [
					'label'=>'TimeCardCreatedBy', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'TimeCardCreatedBy',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'TimeCardCreatedBy']); ?>
				</div>
				<?= Html::activeLabel($model, 'TimeCardModifiedDate', [
					'label'=>'TimeCardModifiedDate', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'TimeCardModifiedDate',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'TimeCardModifiedDate']); ?>
				</div>
				<?= Html::activeLabel($model, 'TimeCardModifiedBy', [
					'label'=>'TimeCardModifiedBy', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'TimeCardModifiedBy',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'TimeCardModifiedBy']); ?>
				</div>
			</div>

    <div class="form-group">
        <?= Html::submitButton( 'Submit', ['class' => 'btn btn-success', 'id'=> 'time_card_submit_btn']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
