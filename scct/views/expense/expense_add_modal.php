<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use kartik\form\ActiveForm;
use kartik\date\DatePicker;

?>

<div class="expense-add-view">
	<?php $form = ActiveForm::begin([
        'id' => 'ExpenseAddModalForm',
        'type' => ActiveForm::TYPE_HORIZONTAL,
		'action' => Url::to('/expense/add'),
        'formConfig' => ['labelSpan' => 1, 'deviceSize' => ActiveForm::SIZE_SMALL],
    ]); ?>
		<div class="form-group kv-fieldset-inline" id="expense_add_form">
			<div class="row">
				<?= Html::activeLabel($model, 'ProjectID', [
					'label'=>'Project', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-4">
					<?= $form->field($model, 'ProjectID',[
						'showLabels'=>false
					])->dropDownList($ProjectDropdown); ?>
				</div>
				<?php Pjax::begin(['id' => 'expenseModalDropdown', 'timeout' => false]) ?>
					<?= Html::activeLabel($model, 'UserID', [
						'label'=>'Employee', 
						'class'=>'col-sm-2 control-label'
					]) ?>
					<div class="col-sm-4">
						<?= $form->field($model, 'UserID',[
							'showLabels'=>false
						])->dropDownList($EmployeeDropdown); ?>
					</div>
				<?php Pjax::end() ?>
				<?= Html::activeLabel($model, 'CreatedDateTime', [
					'label'=>'Created Date', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-4">
					<?= $form->field($model, 'CreatedDateTime',[
					'showLabels'=>false
					])->widget(DatePicker::classname(),[
						'options' => ['placeholder' => 'Enter date...'],
						'readonly' => true,
						'pluginOptions' => [
							'autoclose' => true,
							'format' => 'yyyy-mm-dd'
						]
					]); ?>
				</div>
				<?= Html::activeLabel($model, 'ChargeAccount', [
					'label'=>'Account Type', 
					'class'=>'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-4">
					<?= $form->field($model, 'ChargeAccount',[
						'showLabels'=>false
					])->dropDownList($CoaDropdown); ?>
				</div>
				<?= Html::activeLabel($model, 'Quantity', [
					'label' => 'Quantity',
					'class' => 'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-4">
					<?= $form->field($model, 'Quantity', [
						'showLabels' => false
					])->textInput(['placeholder' => 'Quantity', 'type' => 'number']); ?>
				</div>
			</div>
		</div>
		<br>
		<div id="expenseModalFormButtons" class="form-group">
			<?= Html::Button('Submit', ['class' => 'btn btn-success', 'id' => 'exepnse_modal_submit_btn']) ?>
		</div>
    <?php ActiveForm::end(); ?>
	
	<script>
	
		$(document).off('change', '#expense-projectid').on('change', '#expense-projectid', function (event) {
			reloadModalDropdowns();
			event.preventDefault();
			return false;
		});
		
		$(document).off('click', '#exepnse_modal_submit_btn').on('click', '#exepnse_modal_submit_btn', function (event) {
			expenseCreation();
			event.preventDefault();
			return false;
		});
	
		//reloads modal
		function reloadModalDropdowns(){
			var projectID = $('#expense-projectid').val();
			$('#loading').show();
			$.pjax.reload({
				type: 'GET',
				url: '/expense/add?projectID='+projectID,
				container: '#expenseModalDropdown', // id to update content
				timeout: 99999,
				push: false,
				replace: false,
			});
			$('#expenseModalDropdown').off('pjax:success').on('pjax:success', function () {
				$('#loading').hide();
			});
		}
		
		function expenseCreation() {
			var form = $('#ExpenseAddModalForm');
			expenseModalFormValidate();
			$('#loading').show();
			$.ajax({
				type: 'POST',
				url: form.attr("action"),
				data: form.serialize(),
				error : function (){
					console.log("internal server error");
				}
			});
		}
		
		function expenseModalFormValidate(){
			$('#ExpenseAddModalForm').yiiActiveForm('validateAttribute', 'expense-projectid');
			$('#ExpenseAddModalForm').yiiActiveForm('validateAttribute', 'expense-userid');
			$('#ExpenseAddModalForm').yiiActiveForm('validateAttribute', 'expense-createddatetime');
			$('#ExpenseAddModalForm').yiiActiveForm('validateAttribute', 'expense-chargeaccount');
			$('#ExpenseAddModalForm').yiiActiveForm('validateAttribute', 'expense-quantity');
		}
		
	</script>
</div>