<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;
use kartik\time\TimePicker;
use kartik\datetime\DateTimePicker;

/* @var $this yii\web\View */
/* @var $model app\models\TimeCard */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="time-entry-form">

    <?php $form = ActiveForm::begin([
				'id' => $model->formName(),
				'type' => ActiveForm::TYPE_HORIZONTAL,
				'formConfig' => ['labelSpan' => 1, 'deviceSize' => ActiveForm::SIZE_SMALL],
			]); ?>
			<div class="form-group kv-fieldset-inline" id="time_entry_form">
				<?= Html::activeLabel($model, 'TimeEntryStartTime', [
					'label'=>'Start Time', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-4">
				<?= $form->field($model, 'TimeEntryStartTime',[
					'showLabels'=>false
					])->widget(TimePicker::classname(),[
						'options' => ['placeholder' => 'Enter time...'],
						'pluginOptions' => [
							'autoclose' => true,
							'format' => 'hh:ii:ss'
						]
					]); ?>
				</div>
				
				<?= Html::activeLabel($model, 'TimeEntryEndTime', [
					'label'=>'End Time', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-4">
					<?= $form->field($model, 'TimeEntryEndTime',[
					'showLabels'=>false
					])->widget(TimePicker::classname(),[
						'options' => ['placeholder' => 'Enter time...'],
						'pluginOptions' => [
							'autoclose' => true,
							'format' => 'hh:ii:ss'
						]
					]); ?>
				</div>

				
				
				<?= Html::activeLabel($model, 'TimeEntryActivityID', [
					'label'=>'Activity Type', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-4">
					<?= $form->field($model, 'TimeEntryActivityID',[
					'showLabels'=>false
					])->dropDownList($activityCode); ?>
				</div>
	
				<!--div class="col-sm-4">
					<?/*= $form->field($model, 'TimeEntryDate')
					  ->hiddenInput(['value' =>$model["TimeEntryStartTime"]])
					  ->label(false);

					*/?>
				</div-->
	
				<?=Html::activeHiddenInput($model, 'TimeEntryCreatedBy', ['value' => Yii::$app->user->identity->id]); ?>
			</div>
			<br>
			<br>
			<div class="form-group">
				<?= Html::submitButton( 'Submit', ['class' => 'btn btn-success', 'id'=> 'time_card_submit_btn']) ?>
			</div>

    <?php ActiveForm::end(); ?>
</div>

