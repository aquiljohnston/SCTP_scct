<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;
use kartik\date\DatePicker;

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
					'label'=>'Serial Number', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'EquipmentSerialNumber',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'EquipmentSerialNumber']); ?>
				</div>
				<?= Html::activeLabel($model, 'EquipmentSCNumber', [
					'label'=>'SC Number', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'EquipmentSCNumber',[
						'showLabels'=>false
					])->textInput(['placeholder'=>'EquipmentSCNumber']); ?>
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
					])->dropDownList($types);  ?>
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
					'label'=>'Manufacture Year', 
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
					])->dropDownList($conditions);  ?>
				</div>
				<?= Html::activeLabel($model, 'EquipmentStatus', [
					'label'=>'Status', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'EquipmentStatus',[
						'showLabels'=>false
					])->dropDownList($statuses);  ?>
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
					'label'=>'Warranty Detail', 
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
					'label'=>'Client', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'EquipmentClientID',[
						'showLabels'=>false
					])->dropDownList($clients); ?>
				</div>
				<?= Html::activeLabel($model, 'EquipmentProjectID', [
					'label'=>'Project', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'EquipmentProjectID',[
						'showLabels'=>false
					])->dropDownList($projects); ?>
				</div>
				<?= Html::activeLabel($model, 'EquipmentAnnualCalibrationDate', [
					'label'=>'Annual Calibration Date', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
					<?= $form->field($model, 'EquipmentAnnualCalibrationDate',[
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
				<?= Html::activeLabel($model, 'EquipmentAssignedUserID', [
					'label'=>'Assigned User', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-2">
				<?php if($model["EquipmentAcceptedFlag"] == "Yes"){?>
				<?php	$model["EquipmentAssignedUserID"] = "Unassigned"; ?>
					<?= $form->field($model, 'EquipmentAssignedUserID',[
						'showLabels'=>false
					])->dropDownList($users);  ?>
				<?php }else{ ?>
				<?php	$model["EquipmentAssignedUserID"] = "Unassigned"; ?>
					<?= $form->field($model, 'EquipmentAssignedUserID',[
						'showLabels'=>false
					])->dropDownList($users, array("disabled"=>"disabled"));  ?>
				<?php }?>
				</div>
			</div>

    <div class="form-group">
        <?= Html::submitButton( 'Submit', ['class' => 'btn btn-success', 'id'=> 'equipment_submit_btn']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
