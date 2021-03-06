<?php

use kartik\form\ActiveForm;
use kartik\widgets\TimePicker;
use kartik\datetime\DateTimePicker;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Html;
use yii\helpers\Url;

$columns = [
		[
			'label' => 'Task',
			'attribute' => 'Task',
			'headerOptions' => ['class' => 'text-center'],
			'contentOptions' => ['class' => 'text-center'],
		],
		[
			'label' => 'Date',
			'attribute' => 'Date',
			'headerOptions' => ['class' => 'text-center'],
			'contentOptions' => ['class' => 'text-center'],
		],
		[
			'label' => 'Start Time',
			'attribute' => 'Start Time',
			'headerOptions' => ['class' => 'text-center'],
			'contentOptions' => ['class' => 'text-center'],
		],
		[
			'label' => 'End Time',
			'attribute' => 'End Time',
			'headerOptions' => ['class' => 'text-center'],
			'contentOptions' => ['class' => 'text-center'],
		]
		// ,[
			// 'class' => 'kartik\grid\ActionColumn',
			// 'template' => '{delete}',
			// 'header' => '',
			// 'buttons' => [
				// 'delete' => function ($url, $model, $key) {
					// return Html::a('<span id="taskModalDeactivateAction" class="glyphicon glyphicon-trash" title="Deactivate"></span>');
				// }
			// ]
		// ]
	];

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
?>
<div id="taskWarningMessage" style="color: red; display: none;">
	<p></p>
</div>
<div class="hours-overview-table">
	<h5 id="HoursOverviewHeader" style="display: none;"><b>Hours Overview</b></h5>
	<?php Pjax::begin(['id' => 'hoursOverviewPjaxContainer', 'timeout' => false]) ?>
		<?= GridView::widget([
			'id' => 'HoursOverviewGridview',
			'dataProvider' => $hoursOverviewDataProvider,
			'export' => false,
			'bootstrap' => false,
			'pjax' => true,
			'summary' => '',
			'showOnEmpty' => true,
			'emptyText' => 'No task entries exist for this day.',
			'columns' => $columns,
			'options' => [
				'style' => 'display: none;'
			]
		]); ?>
    <?php Pjax::end() ?>
</div>
<br>
<div class="time-entry-form">
    <?php $form = ActiveForm::begin([
        'id' => 'TaskEntryForm',//$model->formName(),
        'type' => ActiveForm::TYPE_HORIZONTAL,
        'action' => Url::to('/task/add-task-entry'),
        'formConfig' => ['labelSpan' => 1, 'deviceSize' => ActiveForm::SIZE_SMALL],
    ]); ?>
    <div class="form-group kv-fieldset-inline" id="time_entry_form">
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
                        'startDate' => $SundayDate,
                        'endDate' => $SaturdayDate,
                        'autoclose'=>true],
                    'type' => \kartik\widgets\DatePicker::TYPE_COMPONENT_APPEND,
                    'pluginEvents' => [
                        "changeDate" => 'function(e) {
                            var date = $("#dynamicmodel-date").val();
                            var StartTime = $("#dynamicmodel-starttime").val();
                            var EndTime = $("#dynamicmodel-endtime").val();
                            var TaskName = $("#dynamicmodel-taskname").val();
                            var ChangeOfAccountType = $("#dynamicmodel-chargeofaccounttype").val();

                            if (date !="" && StartTime != "" &&
                                EndTime != "" && TaskName != "" && 
                                 ChangeOfAccountType != ""){
                                      $("#create_task_entry_submit_btn").prop("disabled", false); 
                                 }
                         }'
                    ]
                ]); ?>
            </div>

            <?= Html::activeLabel($model, 'TaskName', [
                'label' => 'Task Name',
                'class' => 'col-sm-2 control-label'
            ]) ?>
            <div class="col-sm-4">
                <?= $form->field($model, 
                    'TaskName', [
                    'showLabels' => false
                ])->dropDownList($allTask,
                      array('prompt'=>'--Select a Task--')
    ); ?>
            </div>
        </div>
        <div class="row">
            <?= Html::activeLabel($model, 'StartTime', [
                'label' => 'Start Time',
                'class' => 'col-sm-2 control-label'
            ]) ?>
            <div class="col-sm-4">
                <?= $form->field($model, 'StartTime', [
                    'showLabels' => false
                ])->widget(\kartik\widgets\TimePicker::classname(), [
                    'id' => 'StartTimePicker',
                    'pluginOptions' => ['placeholder' => 'Enter time...','defaultTime' => FALSE]
                ]); ?>
            </div>
            <?= Html::activeLabel($model, 'EndTime', [
                'label' => 'End Time',
                'class' => 'col-sm-2 control-label'
            ]) ?>
            <div class="col-sm-4">
                <?= $form->field($model, 'EndTime', [
                    'showLabels' => false
                ])->widget(\kartik\widgets\TimePicker::classname(), [
                    'pluginOptions' => ['placeholder' => 'Enter time...','defaultTime' => FALSE]
                ]); ?>
            </div>
        </div>
        <div class="row">
			<?= Html::activeLabel($model, 'TimeReason', [
                'label' => 'Reason',
                'class' => 'col-sm-2 control-label'
            ]) ?>
            <div class="col-sm-4">
                <?= $form->field($model, 'TimeReason', [
                    'showLabels' => false
                ])->dropDownList($timeReasonDropdown); ?>
            </div>
            <?= Html::activeLabel($model, 'ChargeOfAccountType', [
                'label' => 'Account Type',
                'class' => 'col-sm-2 control-label'
            ]) ?>
            <div class="col-sm-4">
                <?= $form->field($model, 'ChargeOfAccountType', [
                    'showLabels' => false
                ])->dropDownList($chartOfAccountType,
                    array('prompt'=>'--Select an Account Type--')); ?>
            </div>
        </div>
		<div class="row" id='timeReasonCommentRow' style='display: none'>
            <?= Html::activeLabel($model, 'Comments', [
				'label' => 'Comments',
				'class' => 'col-sm-2 control-label',
			]) ?>
			<div class="col-sm-6">
				<?= $form->field($model, 'Comments', [
					'showLabels' => false
				])->textarea(['placeholder' => 'Comments']); ?>
			</div>
        </div>
        <?= Html::activeHiddenInput($model, 'TimeCardID', ['value' => $model->TimeCardID]); ?>
		<?= Html::activeHiddenInput($model, 'WeekStart', ['value' => $model->WeekStart]); ?>
		<?= Html::activeHiddenInput($model, 'WeekEnd', ['value' => $model->WeekEnd]); ?>
    </div>
		<input type="hidden" name="timeCardProjectID" value=<?=Yii::$app->getRequest()->getQueryParam('timeCardProjectID') ?> />
		<input type="hidden" name="inOvertime" value=<?=Yii::$app->getRequest()->getQueryParam('inOvertime') ?> />
    <br>
    <br>
    <div class="form-group">
        <?= Html::Button('Submit', ['class' => 'btn btn-success', 'id' => 'create_task_entry_submit_btn', 'disabled' => 'disabled']) ?>
    </div>
    <?php ActiveForm::end(); ?>
	
    <script>
		
		//check for valid form to determine when submit should be available
		$('#TaskEntryForm :input').keyup(function (){
			if (InputFieldValidator()){
				$('#create_task_entry_submit_btn').prop('disabled', false); 
            }else{
				$('#create_task_entry_submit_btn').prop('disabled', true); 
            }
        });
		
		$(document).off('change', '#TaskEntryForm :input').on('change', '#TaskEntryForm :input', function (){
			if (InputFieldValidator()){
				$('#create_task_entry_submit_btn').prop('disabled', false); 
            }else{
				$('#create_task_entry_submit_btn').prop('disabled', true); 
            }
        });

        $('#create_task_entry_submit_btn').click(function (event) {
             if (InputFieldValidator()) {
				//enable potentially disable field so data may be passed
				$('#dynamicmodel-taskname').attr("disabled", false);
                TaskEntryCreation();
                event.preventDefault();
                return false;
            } else {
                $('#create_task_entry_submit_btn').prop('disabled', true);
            }
        });
		
		$(document).off('change', '#TaskEntryForm #dynamicmodel-date').on('change', '#TaskEntryForm #dynamicmodel-date', function (){
			reloadHoursOverview();
        });
		
		$(document).off('change', '#TaskEntryForm #dynamicmodel-timereason').on('change', '#TaskEntryForm #dynamicmodel-timereason', function (){
			var timeReason = $('#dynamicmodel-timereason').val();
			//hide/show dynamicmodel-comments based on time reason selection
			if(timeReason == 'Other'){
				$('#timeReasonCommentRow').css("display", "block");
			}else{
				$('#timeReasonCommentRow').css("display", "none");
				$('#dynamicmodel-comments').val('');
			}
        });
		
		$(document).off('change', '#TaskEntryForm #dynamicmodel-chargeofaccounttype').on('change', '#TaskEntryForm #dynamicmodel-chargeofaccounttype', function (){
			var accountType = $('#dynamicmodel-chargeofaccounttype').val();
			if(accountType == '5015' || accountType == '5020'){
				//default time to 8 hours if 5015(OH Holiday Pay/Bereavement) or 5020(PTO) is selected
				$('#dynamicmodel-starttime').val('08:00 AM');
				$('#dynamicmodel-endtime').val('04:00 PM');
				//default task name to 'OTHER' and lock selection if 5015(OH Holiday Pay/Bereavement) or 5020(PTO) is selected
				$('#dynamicmodel-taskname').val('OTHER');
				$('#dynamicmodel-taskname').attr("disabled", true);
				//validate new form values
				if (InputFieldValidator()){
					$('#create_task_entry_submit_btn').prop('disabled', false); 
				}else{
					$('#create_task_entry_submit_btn').prop('disabled', true); 
				}
			} else {
				//unlock selection of task name if 5015(OH Holiday Pay/Bereavement) or 5020(PTO) is not selected
				$('#dynamicmodel-taskname').attr("disabled", false);
			}
        });
		
		//deactivate entry
		$(document).off('click', '#taskModalDeactivateAction').on('click', '#taskModalDeactivateAction',function (){
			//get entry id
			var entryID = $(this).closest('tr').attr('data-key');
			deactivateTimeReason('deactivateEntry', entryID);
		});

        function InputFieldValidator() {
			var date = $('#dynamicmodel-date').val();
			var TaskName = $('#dynamicmodel-taskname').val();
			var ChangeOfAccountType = $('#dynamicmodel-chargeofaccounttype').val();
			//convert times to 24
			var StartTime = $('#dynamicmodel-starttime').val();
			var EndTime = $('#dynamicmodel-endtime').val();
			var timeReason = $('#dynamicmodel-timereason').val();
			var comments = $('#dynamicmodel-comments').val();

            if (date != '' && StartTime != '' && EndTime != '' && TaskName != '' && ChangeOfAccountType != '' && timeReason != '' && comments.length <= 225){
               //only convert when not empty
                StartTime = ConvertToTwentyFourHourTime(StartTime);
                EndTime = ConvertToTwentyFourHourTime(EndTime);
                //now compare 
                if(EndTime > StartTime)
					return true;
            }
            return false;
        }
        
		
		//expected format of "hh:mm AM/PM"
		//returns string in 24 hour format "hh:mm"
		function ConvertToTwentyFourHourTime(twelveHourTime) {
			var hours = Number(twelveHourTime.match(/^(\d+)/)[1]);
			var minutes = Number(twelveHourTime.match(/:(\d+)/)[1]);
			var AMPM = twelveHourTime.match(/\s(.*)$/)[1];
			if(AMPM == "PM" && hours<12) hours = hours+12;
			if(AMPM == "AM" && hours==12) hours = hours-12;
			var sHours = hours.toString();
			var sMinutes = minutes.toString();
			if(hours<10) sHours = "0" + sHours;
			if(minutes<10) sMinutes = "0" + sMinutes;
			return sHours + ":" + sMinutes;
		}
		
		//reloads hours overview table in the task entry modal
		function reloadHoursOverview()
		{
			var form = $('#TaskEntryForm');
			$('#loading').show();
			$.pjax.reload({
				type: 'GET',
				url: form.attr("action"),
				container: '#hoursOverviewPjaxContainer', // id to update content
				data: form.serialize(),
				timeout: 99999,
				//not sure how many of these three I actually need to prevent url overwrite
				push: false,
				replace: false,
			});
			$('#hoursOverviewPjaxContainer').off('pjax:success').on('pjax:success', function () {
				if($('#TaskEntryForm #dynamicmodel-date').val() != '')
				{
					$('#HoursOverviewHeader').css("display", "block");
					$('#HoursOverviewGridview').css("display", "block");
				} else {
					$('#HoursOverviewHeader').css("display", "none");
					$('#HoursOverviewGridview').css("display", "none");
				}
				$('#loading').hide();
			});
		}
		
		function TaskEntryCreation() {
			var form = $('#TaskEntryForm');

			$('#loading').show();

			$.ajax({
				type: 'POST',
				url: form.attr("action"),
				data: form.serialize(),
				success: function (response) {
					responseObj = JSON.parse(response);
					if(responseObj.SuccessFlag == 1)
					{
						$.pjax.reload({container:"#ShowTimeEntriesView", timeout: 99999}).done(function(){
							validateTaskToolTip();
							$('#create_task_entry_submit_btn').closest('.modal-dialog').parent().modal('hide');
							$('#loading').hide();
						});
					} else {
						$('#taskWarningMessage').css("display", "block");
						$('#taskWarningMessage').html(responseObj.warningMessage);
						$('#loading').hide();
					}
				},
				error : function (){
					console.log("internal server error");
				}
			});
		}
		
		function deactivateEntry(timeReason, entryID){
			krajeeDialog.defaults.confirm.title = 'Deactivate';
			krajeeDialog.confirm('Are you sure you want to deactivate this entry?', function (resp) {
				if(resp){	
					$('#timeReasonModal').modal('hide');
					$('#loading').show();
					data = {entryID: entryID, timeReason : timeReason};
					entry = {data: data};
					//post form data
					$.ajax({
						type: 'POST',
						data: entry,
						url: '/task/deactivate',
						success: function (response) {
							reloadTimeTaskGridViews();
						}
					});		
				}
			})
		}
		
		function reloadTimeTaskGridViews(){
			//reload show entries gridview
			$.pjax.reload({
				container:"#ShowTimeEntriesView",
				timeout: 99999
			}).done(function(){
				reloadHoursOverview();
			});
		}

    </script>
</div>