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
				<?= Html::activeLabel($model, 'ProjectName', [
					'label'=>'ProjectName', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'ProjectName',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'ProjectName']); ?>
				</div>
				<?= Html::activeLabel($model, 'ProjectDescription', [
					'label'=>'ProjectDescription', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'ProjectDescription',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'ProjectDescription']); ?>
				</div>
				<?= Html::activeLabel($model, 'ProjectNotes', [
					'label'=>'ProjectNotes', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'ProjectNotes',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'ProjectNotes']); ?>
				</div>
				<?= Html::activeLabel($model, 'ProjectType', [
					'label'=>'ProjectType', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'ProjectType',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'ProjectType']); ?>
				</div>
				<?= Html::activeLabel($model, 'ProjectStatus', [
					'label'=>'ProjectStatus', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'ProjectStatus',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'ProjectStatus']); ?>
				</div>
				<?= Html::activeLabel($model, 'ProjectClientID', [
					'label'=>'ProjectClientID', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'ProjectClientID',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'ProjectClientID']); ?>
				</div>
				<?= Html::activeLabel($model, 'ProjectStartDate', [
					'label'=>'ProjectStartDate', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'ProjectStartDate',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'ProjectStartDate']); ?>
				</div>
				<?= Html::activeLabel($model, 'ProjectEndDate', [
					'label'=>'ProjectEndDate', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'ProjectEndDate',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'ProjectEndDate']); ?>
				</div>
			</div>

    <div class="form-group" id="">
       <?= Html::submitButton( 'Submit', ['class' => 'btn btn-success', 'id'=> 'project_submit_btn']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
