<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;
use kartik\date\DatePicker;

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
					'label'=>'Name', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'ProjectName',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'ProjectName']); ?>
				</div>
				<?= Html::activeLabel($model, 'ProjectDescription', [
					'label'=>'Description', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'ProjectDescription',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'ProjectDescription']); ?>
				</div>
				<?= Html::activeLabel($model, 'ProjectNotes', [
					'label'=>'Notes', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'ProjectNotes',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'ProjectNotes']); ?>
				</div>
				<?= Html::activeLabel($model, 'ProjectType', [
					'label'=>'Type', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'ProjectType',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'ProjectType']); ?>
				</div>
				<?= Html::activeLabel($model, 'ProjectStatus', [
					'label'=>'Status', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'ProjectStatus',[
						'showLabels'=>false
					])->dropDownList($flag); ?>
				</div>
				<?= Html::activeLabel($model, 'ProjectClientID', [
					'label'=>'ClientID', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'ProjectClientID',[
						'showLabels'=>false
					])->dropDownList($clients); ?>
				</div>
				<?= Html::activeLabel($model, 'ProjectState', [
					'label'=>'State', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'ProjectState',[
						'showLabels'=>false
					])->dropDownList($states); ?>
				</div>
				<?= Html::activeLabel($model, 'ProjectStartDate', [
					'label'=>'Start Date', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'ProjectStartDate',[
					'showLabels'=>false
					])->widget(DatePicker::classname(),[
						'options' => ['placeholder' => 'Enter time...'],
						'readonly' => true,
						'pluginOptions' => [
							'autoclose' => true,
							'format' => 'yyyy-mm-dd'
						]
					]); ?>
				</div>
				<?= Html::activeLabel($model, 'ProjectEndDate', [
					'label'=>'EndDate', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'ProjectEndDate',[
					'showLabels'=>false
					])->widget(DatePicker::classname(),[
						'options' => ['placeholder' => 'Enter time...'],
						'readonly' => true,
						'pluginOptions' => [
							'autoclose' => true,
							'format' => 'yyyy-mm-dd'
						]
					]); ?>
				</div>
			</div>

    <div class="form-group" id="">
       <?= Html::submitButton( 'Submit', ['class' => 'btn btn-success', 'id'=> 'project_submit_btn']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
