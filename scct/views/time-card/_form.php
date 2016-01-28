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
					'label'=>'Start Date', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'TimeCardStartDate',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'TimeCardStartDate']); ?>
				</div>
				<?= Html::activeLabel($model, 'TimeCardEndDate', [
					'label'=>'End Date', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'TimeCardEndDate',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'TimeCardEndDate']); ?>
				</div>
				<?= Html::activeLabel($model, 'TimeCardHoursWorked', [
					'label'=>'Hours Worked', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'TimeCardHoursWorked',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'TimeCardHoursWorked']); ?>
				</div>
				<?= Html::activeLabel($model, 'TimeCardProjectID', [
					'label'=>'Project ID', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'TimeCardProjectID',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'TimeCardProjectID']); ?>
				</div>
				<?= Html::activeLabel($model, 'TimeCardTechID', [
					'label'=>'Tech ID', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'TimeCardTechID',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'TimeCardTechID']); ?>
				</div>
				<?= Html::activeLabel($model, 'TimeCardSupervisorName', [
					'label'=>'Supervisor Name', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'TimeCardSupervisorName',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'TimeCardSupervisorName']); ?>
				</div>
				<?= Html::activeLabel($model, 'TimeCardComment', [
					'label'=>'Comment', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'TimeCardComment',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'TimeCardComment']); ?>
				</div>
				<?= Html::activeLabel($model, 'TimeCardCreateDate', [
					'label'=>'Create Date', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'TimeCardCreateDate',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'TimeCardCreateDate']); ?>
				</div>
				<?= Html::activeLabel($model, 'TimeCardCreatedBy', [
					'label'=>'Created By', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'TimeCardCreatedBy',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'TimeCardCreatedBy']); ?>
				</div>
				<?= Html::activeLabel($model, 'TimeCardModifiedDate', [
					'label'=>'Modified Date', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'TimeCardModifiedDate',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'TimeCardModifiedDate']); ?>
				</div>
				<?= Html::activeLabel($model, 'TimeCardModifiedBy', [
					'label'=>'Modified By', 
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
