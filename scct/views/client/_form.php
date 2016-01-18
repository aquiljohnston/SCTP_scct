<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\client */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="client-form">

    <?php $form = ActiveForm::begin([
				'type' => ActiveForm::TYPE_HORIZONTAL,
				'formConfig' => ['labelSpan' => 3, 'deviceSize' => ActiveForm::SIZE_SMALL],
			]); ?>
			<div class="form-group kv-fieldset-inline" id="client_form">
				<?= Html::activeLabel($model, 'ClientName', [
					'label'=>'ClientName', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'ClientName',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'ClientName']); ?>
				</div>
				<?= Html::activeLabel($model, 'ClientContactTitle', [
					'label'=>'ClientContactTitle', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'ClientContactTitle',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'ClientContactTitle']); ?>
				</div>
				<?= Html::activeLabel($model, 'ClientContactFName', [
					'label'=>'ClientContactFName', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'ClientContactFName',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'ClientContactFName']); ?>
				</div>
				<?= Html::activeLabel($model, 'ClientContactMI', [
					'label'=>'ClientContactMI', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'ClientContactMI',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'ClientContactMI']); ?>
				</div>
				<?= Html::activeLabel($model, 'ClientContactLName', [
					'label'=>'ClientContactLName', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'ClientContactLName',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'ClientContactLName']); ?>
				</div>
				<?= Html::activeLabel($model, 'ClientPhone', [
					'label'=>'ClientPhone', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'ClientPhone',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'ClientPhone']); ?>
				</div>
				<?= Html::activeLabel($model, 'ClientEmail', [
					'label'=>'ClientEmail', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'ClientEmail',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'Begin Date']); ?>
				</div>
				<?= Html::activeLabel($model, 'ClientAddr1', [
					'label'=>'ClientAddr1', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'ClientAddr1',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'ClientAddr1']); ?>
				</div>
				<?= Html::activeLabel($model, 'ClientAddr2', [
					'label'=>'ClientAddr2', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'ClientAddr2',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'ClientAddr2']); ?>
				</div>
				<?= Html::activeLabel($model, 'ClientCity', [
					'label'=>'ClientCity', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'ClientCity',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'ClientCity']); ?>
				</div>
				<?= Html::activeLabel($model, 'ClientState', [
					'label'=>'ClientState', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'ClientState',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'ClientState']); ?>
				</div>
				<?= Html::activeLabel($model, 'ClientZip4', [
					'label'=>'ClientZip4', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'ClientZip4',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'ClientZip4']); ?>
				</div>
				<?= Html::activeLabel($model, 'ClientTerritory', [
					'label'=>'ClientTerritory', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'ClientTerritory',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'ClientTerritory']); ?>
				</div>
				<?= Html::activeLabel($model, 'ClientActiveFlag', [
					'label'=>'ClientActiveFlag', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'ClientActiveFlag',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'ClientActiveFlag']); ?>
				</div>
				<?= Html::activeLabel($model, 'ClientDivisionsFlag', [
					'label'=>'ClientDivisionsFlag', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'ClientDivisionsFlag',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'ClientDivisionsFlag']); ?>
				</div>
				<?= Html::activeLabel($model, 'ClientComment', [
					'label'=>'ClientComment', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'ClientComment',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'ClientComment']); ?>
				</div>
				<?= Html::activeLabel($model, 'ClientCreateDate', [
					'label'=>'ClientCreateDate', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'ClientCreateDate',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'ClientCreateDate']); ?>
				</div>
				<?= Html::activeLabel($model, 'ClientCreatorUserID', [
					'label'=>'ClientCreatorUserID', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'ClientCreatorUserID',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'ClientCreatorUserID']); ?>
				</div>
				<?= Html::activeLabel($model, 'ClientModifiedDate', [
					'label'=>'ClientModifiedDate', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'ClientModifiedDate',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'ClientModifiedDate']); ?>
				</div>
				<?= Html::activeLabel($model, 'ClientModifiedBy', [
					'label'=>'ClientModifiedBy', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'ClientModifiedBy',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'ClientModifiedBy']); ?>
				</div>								
			</div>

    <div class="form-group">
		<?= Html::submitButton( 'Submit', ['class' => 'btn btn-success', 'id'=> 'client_submit_btn']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
