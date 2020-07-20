<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use kartik\form\ActiveForm;

?>

<div class="time-reason-view">		
	<!--time reason form-->
	<?php $form = ActiveForm::begin([
        'id' => 'TimeReasonModalForm',
        'type' => ActiveForm::TYPE_HORIZONTAL,
        'formConfig' => ['labelSpan' => 1, 'deviceSize' => ActiveForm::SIZE_SMALL],
    ]); ?>
		<div class="form-group kv-fieldset-inline" id="time_reason_form">
			<div class="row">
				<?= Html::activeLabel($model, 'DeactivateTimeReason', [
					'label' => 'Reason',
					'class' => 'col-sm-2 control-label'
				]) ?>
				<div class="col-sm-4">
					<?= $form->field($model, 'DeactivateTimeReason', [
						'showLabels' => false
					])->dropDownList($timeReasonDropdown); ?>
				</div>
			</div>
			<div class="row" id='timeReasonModalCommentRow' style='display: none'>
				<?= Html::activeLabel($model, 'DeactivateComments', [
					'label' => 'Comments',
					'class' => 'col-sm-2 control-label',
				]) ?>
				<div class="col-sm-6">
					<?= $form->field($model, 'DeactivateComments', [
						'showLabels' => false
					])->textarea(['placeholder' => 'Comments']); ?>
				</div>
			</div>
			<div class="row">
				<?= Html::activeHiddenInput($model, 'DeactivateAction', ['value' => $model->DeactivateAction]); ?>
				<?= Html::activeHiddenInput($model, 'DeactivateData', ['value' => $model->DeactivateData]); ?>
			</div>
		</div>
		<br>
		<div id="timeReasonModalFormButtons" class="form-group">
			<!--<?= Html::Button('Submit', ['class' => 'btn btn-success', 'id' => 'time_reason_submit_btn', 'disabled' => 'disabled']) ?>-->
		</div>
    <?php ActiveForm::end(); ?>
	
	<script>
		$(document).off('change', '#TimeReasonModalForm #dynamicmodel-deactivatetimereason').on('change', '#TimeReasonModalForm #dynamicmodel-deactivatetimereason', function (){
			var timeReason = $('#dynamicmodel-deactivatetimereason').val();
			//hide/show dynamicmodel-deactivatecomments based on time reason selection
			if(timeReason == 'Other'){
				$('#timeReasonModalCommentRow').css("display", "block");
			}else{
				$('#timeReasonModalCommentRow').css("display", "none");
				$('#TimeReasonModalForm dynamicmodel-deactivatecomments').val('');
			}
		});
		
		//check for valid form to determine when submit should be available
		$('#TimeReasonModalForm :input').keyup(function (){
			if (timeReasonModalFormValidate()){
				$('#time_reason_submit_btn').prop('disabled', false); 
            }else{
				$('#time_reason_submit_btn').prop('disabled', true); 
            }
        });
		
		$(document).off('change', '#TimeReasonModalForm :input').on('change', '#TimeReasonModalForm :input', function (){
			if(timeReasonModalFormValidate()){
				$('#time_reason_submit_btn').prop('disabled', false); 
			}else{
				$('#time_reason_submit_btn').prop('disabled', true); 
			}
		});
		
		$(document).off('click', '#time_reason_submit_btn').on('click', '#time_reason_submit_btn',function (){
			callDeactivateAction();
		});
		
		function callDeactivateAction(){
			if(timeReasonModalFormValidate()){
				timeReason = $('#dynamicmodel-deactivatetimereason').val();
				comments = $('#dynamicmodel-deactivatecomments').val();
				action = $('#dynamicmodel-deactivateaction').val();
				data = $('#dynamicmodel-deactivatedata').val();
				//if timeReason 'Other' combine with comments
				if(timeReason == 'Other')
					timeReason = timeReason + ' - ' + comments;
				if(data != null){
					window[action](timeReason, data);
				}else{
					window[action](timeReason);
				}
			}else{
				$('#time_reason_submit_btn').prop('disabled', true);
			}
		}
		
		//validate form fields
		function timeReasonModalFormValidate(){
			timeReason = $('#dynamicmodel-deactivatetimereason').val();
			comments = $('#dynamicmodel-deactivatecomments').val();
			if(timeReason == '')
				return false;
			if(comments.length > 225)
				return false;
			return true;
		}
	</script>
</div>