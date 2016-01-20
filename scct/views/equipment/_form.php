<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\equipment */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="equipment-form">

    <?php $form = ActiveForm::begin([
				'type' => ActiveForm::TYPE_HORIZONTAL,
				'formConfig' => ['labelSpan' => 3, 'deviceSize' => ActiveForm::SIZE_SMALL],
			]); ?>
			<div class="form-group kv-fieldset-inline" id="equipment_form">
				<?= Html::activeLabel($model, 'EquipmentName', [
					'label'=>'Name', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'EquipmentName',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'EquipmentName']); ?>
				</div>
				<?= Html::activeLabel($model, 'EquipmentSerialNumber', [
					'label'=>'SerialNumber', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'EquipmentSerialNumber',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'EquipmentSerialNumber']); ?>
				</div>
				<?= Html::activeLabel($model, 'EquipmentDetails', [
					'label'=>'Details', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'EquipmentDetails',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'EquipmentDetails']); ?>
				</div>
				<?= Html::activeLabel($model, 'EquipmentType', [
					'label'=>'Type', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'EquipmentType',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'EquipmentType']); ?>
				</div>
				<?= Html::activeLabel($model, 'EquipmentManufacturer', [
					'label'=>'Manufacturer', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'EquipmentManufacturer',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'EquipmentManufacturer']); ?>
				</div>
				<?= Html::activeLabel($model, 'EquipmentManufactureYear', [
					'label'=>'ManufactureYear', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'EquipmentManufactureYear',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'EquipmentManufactureYear']); ?>
				</div>
				<?= Html::activeLabel($model, 'EquipmentCondition', [
					'label'=>'Condition', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'EquipmentCondition',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'EquipmentCondition']); ?>
				</div>
				<?= Html::activeLabel($model, 'EquipmentMACID', [
					'label'=>'MACID', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'EquipmentMACID',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'EquipmentMACID']); ?>
				</div>
				<?= Html::activeLabel($model, 'EquipmentModel', [
					'label'=>'Model', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'EquipmentModel',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'EquipmentModel']); ?>
				</div>
				<?= Html::activeLabel($model, 'EquipmentColor', [
					'label'=>'Color', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'EquipmentColor',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'EquipmentColor']); ?>
				</div>
				<?= Html::activeLabel($model, 'EquipmentWarrantyDetail', [
					'label'=>'WarrantyDetail', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'EquipmentWarrantyDetail',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'EquipmentWarrantyDetail']); ?>
				</div>
				<?= Html::activeLabel($model, 'EquipmentComment', [
					'label'=>'Comment', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'EquipmentComment',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'EquipmentComment']); ?>
				</div>
				<?= Html::activeLabel($model, 'EquipmentClientID', [
					'label'=>'ClientID', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'EquipmentClientID',[
						'showLabels'=>false
					])->dropDownList($clients); ?>
				</div>
				<?= Html::activeLabel($model, 'EquipmentProjectID', [
					'label'=>'ProjectID', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'EquipmentProjectID',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'EquipmentProjectID']); ?>
				</div>
				<?= Html::activeLabel($model, 'EquipmentAnnualCalibrationDate', [
					'label'=>'AnnualCalibrationDate', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'EquipmentAnnualCalibrationDate',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'EquipmentAnnualCalibrationDate']); ?>
				</div>
				<?= Html::activeLabel($model, 'EquipmentAnnualCalibrationStatus', [
					'label'=>'AnnualCalibrationStatus', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'EquipmentAnnualCalibrationStatus',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'EquipmentAnnualCalibrationStatus']); ?>
				</div>
				<?= Html::activeLabel($model, 'EquipmentAssignedUserID', [
					'label'=>'AssignedUserID', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'EquipmentAssignedUserID',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'EquipmentAssignedUserID']); ?>
				</div>
				<?= Html::activeLabel($model, 'EquipmentCreatedByUser', [
					'label'=>'CreatedByUser', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'EquipmentCreatedByUser',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'EquipmentCreatedByUser']); ?>
				</div>
				<?= Html::activeLabel($model, 'EquipmentCreateDate', [
					'label'=>'CreateDate', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'EquipmentCreateDate',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'EquipmentCreateDate']); ?>
				</div>
				<?= Html::activeLabel($model, 'EquipmentModifiedBy', [
					'label'=>'ModifiedBy', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'EquipmentModifiedBy',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'EquipmentModifiedBy']); ?>
				</div>
				<?= Html::activeLabel($model, 'EquipmentModifiedDate', [
					'label'=>'ModifiedDate', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'EquipmentModifiedDate',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'EquipmentModifiedDate']); ?>
				</div>
			</div>

    <div class="form-group">
        <?= Html::submitButton( 'Submit', ['class' => 'btn btn-success', 'id'=> 'equipment_submit_btn']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
