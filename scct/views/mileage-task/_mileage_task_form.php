<?php

use kartik\form\ActiveForm;
use kartik\datetime\DateTimePicker;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
?>
<div id="mileageWarningMessage" style="color: red; display: none;">
	<p></p>
</div>
<br>
<div class="mileage-entry-form">
    <?php $form = ActiveForm::begin([
        'id' => 'MileageTaskForm',
        'type' => ActiveForm::TYPE_HORIZONTAL,
        'action' => Url::to('/mileage-task/add-mileage-entry-task'),
        'formConfig' => ['labelSpan' => 1, 'deviceSize' => ActiveForm::SIZE_SMALL],
    ]); ?>
		<div class="form-group kv-fieldset-inline" id="mileage_entry_form">
			<div class="row">
				<?= Html::activeLabel($model, 'Date', [
					'label' => 'Date',
					'class' => 'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-4">
					<?= $form->field($model, 'Date', [
						'showLabels' => false
					])->widget(\kartik\widgets\DatePicker::classname(), [
						'pluginOptions' => [
							'placeholder' => 'Enter Date...',
							'startDate' => $sundayDate,
							'endDate' => $saturdayDate,
							'autoclose'=>true],
						'type' => \kartik\widgets\DatePicker::TYPE_COMPONENT_APPEND
					]); ?>
				</div>
				<?= Html::activeLabel($model, 'TotalMiles', [
					'label' => 'Total Miles',
					'class' => 'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-4">
					<?= $form->field($model, 'TotalMiles', [
						'showLabels' => false
					])->textInput(['placeholder' => 'Total Miles', 'type' => 'number']); ?>
				</div>
				<?= Html::activeHiddenInput($model, 'MileageCardID', ['value' => $model->MileageCardID]); ?>
				<?= Html::activeHiddenInput($model, 'WeekStart', ['value' => $model->WeekStart]); ?>
				<?= Html::activeHiddenInput($model, 'WeekEnd', ['value' => $model->WeekEnd]); ?>
			</div>
		</div>
		<br>
		<br>
		<div class="form-group">
			<?= Html::Button('Submit', ['class' => 'btn btn-success', 'id' => 'create_mileage_task_submit_btn', 'disabled' => 'disabled']) ?>
		</div>
    <?php ActiveForm::end(); ?>
	
    <script>

        $(document).off('change', '#MileageTaskForm :input').on('change', '#MileageTaskForm :input', function (){
            if (inputFieldValidator()){
				$('#create_mileage_task_submit_btn').prop('disabled', false); 
            }else{
				$('#create_mileage_task_submit_btn').prop('disabled', true); 
            }   
        });

        $('#create_mileage_task_submit_btn').click(function (event) {
             if (inputFieldValidator()) {
                mileageTaskCreate();
                event.preventDefault();
                return false;
            } else {
                $('#create_mileage_task_submit_btn').prop('disabled', true);
            }
        });

        function inputFieldValidator() {
			var date = $('#dynamicmodel-date').val();
			var totalMiles = $("#dynamicmodel-totalmiles").val();

            if (date !="" && totalMiles != ""){
                return true;
            } else {
                return false; 
            }    
        }
		
		function mileageTaskCreate() {
			var form = $('#MileageTaskForm');

			$('#loading').show();

			$.ajax({
				type: 'POST',
				url: form.attr("action"),
				data: form.serialize(),
				success: function (response) {
					responseObj = JSON.parse(response);
					if(responseObj.SuccessFlag == 1)
					{
						$.pjax.reload({container:"#ShowMileageEntriesView", timeout: 99999}).done(function(){
							validateMileageToolTip();
							$('#create_mileage_task_submit_btn').closest('.modal-dialog').parent().modal('hide');
							$('#loading').hide();
						});
					} else {
						console.log(responseObj.warningMessage);
						$('#mileageWarningMessage').css("display", "block");
						$('#mileageWarningMessage').html(responseObj.WarningMessage);
						$('#loading').hide();
					}
				},
				error : function (){
					console.log("internal server error");
				}
			});
		}
    </script>
</div>