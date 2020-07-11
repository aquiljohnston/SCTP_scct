$(function () {
    $(document).ready(function () {
        if ($('#ShowTimeEntriesView').length > 0) {
			applyTimeEntryListeners();
            validateTaskCheckEnabled();
            validateTaskToolTip();
        }
    });
});

//apply listeners on time-card/show-entries
function applyTimeEntryListeners() {
	$('#approve_timeCard_btn_id').click(function (e) {
		var timeCardId = $('#timeCardId').val();
		krajeeDialog.defaults.confirm.title = 'Approve';
		krajeeDialog.confirm('Are you sure you want to approve this?', function (resp) {
			if (resp) {
				$('#loading').show();
				$.ajax({
					type: 'POST',
					url: '/time-card/approve?id='+timeCardId,
					success: function() {
						$.pjax.reload({container:"#ShowTimeEntriesView", timeout: 99999}).done(function(){ //for pjax update
							$('#approve_timeCard_btn_id').prop('disabled', true);
							$('#add_task_btn_id').prop('disabled', true);
							validateTaskCheckEnabled();
							$('#loading').hide();
						});
					}
				});
			}
		})
	});

	//apply listeners for deactivate check boxes to enable deactivate button
	$(document).on('change','.entryData', function (e) {
		input = $(this);
		tr = $(this).closest('tr');

		if($(this).is(":checked")){
			tr.find('td').each(function(index,value){
				if(index != 0 && $(this).text()!=""){
					th_class = $(this).closest('table').find('th').eq(index).attr('class');
				}
			});
			input.attr('entry',th_class);
		}
		$('#deactive_timeEntry_btn_id').prop('disabled',
			!($("#allTaskEntries-container input:checkbox:checked").length > 0));
	});

	$(document).on('click','#deactive_timeEntry_btn_id',function(e){
		if($('#isSubmitted').val()){
			resetSubmissionStatusDialog('deactivateRowSelection');
		}else{
			deactivateTimeReason('deactivateRowSelection');
		}
	});
	
	//apply listeners on cells with data for deactivation
	$(document).off('click', '#allTaskEntries tbody tr td').on('click', '#allTaskEntries tbody tr td',function (){
		//get task for disable check and deactivate call
		var taskName = $(this).closest('tr').find("td[data-col-seq='0']").text();
		//boolean representing if actions are available based on role and card status
		disabledBoolean = ((($('#isPMApproved').val() || ($('#isApproved').val() && !$('#isProjectManager').val())) && !$('#isAccountant').val()) || taskName == 'Total');
		//restrict click to only day of the week fields
		//with values in the .text()
		if($(this).attr('data-col-seq') >=1 && ($(this).text()!="") && (!disabledBoolean)){
			//get data for deactivate
			var seq_num = $(this).attr('data-col-seq');
			var dataObj = {seq_num: seq_num, taskName: taskName};
			var dataString = JSON.stringify(dataObj);
			if($('#isSubmitted').val()){
				resetSubmissionStatusDialog('deactivateCellSelection', dataString);
			}else{
				deactivateTimeReason('deactivateCellSelection', dataString);
			}
		}
	});
	
	//listener on add task button to launch modal and pass data to it
	$(document).off('click', '.add_task_btn').on('click', '.add_task_btn', function (){
		if($('#isSubmitted').val()){
			resetSubmissionStatusDialog('addTaskEntry');
		}else{
			addTaskEntry();
		}
    });
}

function validateTaskToolTip() {
    $.each($('#allTaskEntries tbody tr td'),function(){
		//get task name for current row
		var taskName = $(this).closest('tr').find("td[data-col-seq='0']").text();
        if($(this).attr('data-col-seq') > 0 && ($(this).text()!="") && ($(this).parent().attr('data-key') > 0)
            && (!$("#approve_timeCard_btn_id").prop("disabled")) && (taskName != 'Total')) 
		{
            $(this).attr("title","Click to deactivate this time!")
        } 
		else if ($('#isAccountant').val() &&
			$(this).attr('data-col-seq') >=1 &&
			($(this).text()!="") &&
			($(this).parent().attr('data-key')>0) &&
			(taskName != 'Total'))
		{
            $(this).attr("title","Click to deactivate this time!")
        }
    });
}

function validateTaskCheckEnabled() {
	$(".entryData").each(function(){
		//boolean representing if actions are available based on role and card status
		disabledBoolean = (($('#isPMApproved').val() || ($('#isApproved').val() && !$('#isProjectManager').val())) && !$('#isAccountant').val());
        if (disabledBoolean) {
            $(this).prop('disabled',true);
        }
    });
}

function deactivateCellSelection(timeReason, dataString){
	id = $('#timeCardId').val();
	cellData = JSON.parse(dataString);
	seq_num = cellData.seq_num;
	date = $("#allTaskEntries tr[data-key='0']").find("td[data-col-seq='"+seq_num+"']").text();
	var entries = [];
	//clean up date format for sending
	date = date.replace(/\-/g, '/');

	krajeeDialog.defaults.confirm.title = 'Deactivate Time';
	krajeeDialog.confirm('Are you sure you want to deactivate this time for '+date+'?', function (resp) {
		if (resp) {
			$('#timeReasonModal').modal('hide');
			$('#loading').show();
			//build and send payload to deactivate single entry
			entries.push({day : date, timeCardID : id, timeReason : timeReason});
			data = {entries};
			$.ajax({
				type: 'POST',
				url: '/time-card/deactivate-by-day/',
				data: data,
				beforeSend: function() {
				},
				success: function(data) {
					$.pjax.reload({container:"#ShowTimeEntriesView", timeout: 99999}).done(function (){
						$('#loading').hide();
					});
				}
			});
		}
	});
	$('#loading').hide();
}

function deactivateRowSelection(timeReason){
	var name = "";
	var tasks = [];
	var entries = [];
	var timeCardID = $('#timeCardId').val();

   $(".entryData").each(function(k,value){
		if($(this).is(":checked")){
			//get task name for payload and confirm message
			name = $(this).attr('taskName');
			tasks.push(name);
		}
	});
	entries.push({taskName : tasks, timeCardID : timeCardID, timeReason : timeReason});
	tasks.join(', ');
	data = {entries};

	krajeeDialog.defaults.confirm.title = 'Deactivate All Task';
	krajeeDialog.confirm('Are you sure you want to deactivate all ' +tasks+ '? Please confirm...', function (resp) {
		if (resp) {
			$('#timeReasonModal').modal('hide');
			$('#loading').show();
			$.ajax({
				type: 'POST',
				url: '/time-card/deactivate-by-task/',
				data: data,
				success: function(data) {
					$.pjax.reload({container:"#ShowTimeEntriesView", timeout: 99999}).done(function(){
						$('#loading').hide();
						$('#deactive_timeEntry_btn_id').prop('disabled',true);
					});
				}
			});
		} else {
			$('#w0').modal('toggle');
			return false;
		}
	});
}

function addTaskEntry(){
	var weekStart = $("#allTaskEntries table th").eq(0).attr('class');
	var weekEnd = $("#allTaskEntries table th").eq(6).attr('class');
	var timeCardID = $('#timeCardId').val();
	var SundayDate = $('#SundayDate').val();
	var SaturdayDate = $('#SaturdayDate').val();
	var timeCardProjectID = $('#TimeCardProjectID').val();
	var inOvertime = $('#inOvertime').val();
	$('#addTaskModal').modal('show').find('#modalContentSpan').html("Loading...");
	//Fetch modal content via pjax
	$.pjax.reload({
		type: 'GET',
		replace:false,
		url: '/task/add-task-entry?weekStart='+weekStart+'&weekEnd='+weekEnd+'&TimeCardID=' + timeCardID + '&SundayDate=' + SundayDate + '&SaturdayDate=' + SaturdayDate + '&timeCardProjectID=' + timeCardProjectID + '&inOvertime=' + inOvertime,
		container: '#modalContentSpan', // id to update content
		timeout: 99999
	})
}

function resetSubmissionStatusDialog(action, dataString){
	krajeeDialog.defaults.confirm.title = 'RESET SUBMISSION STATUS!';
	krajeeDialog.confirm("The card you're attempting to edit has already been submitted. " +
		"Continuing with this action will reset all cards for the week and require you to resubmit. " + 
		"Do you wish to proceed?", function (resp) {
		if (resp) {
			startDate = $('#SundayDate').val();
			endDate = $('#SaturdayDate').val();
			data = { dates: {startDate: startDate, endDate: endDate}};
			$('#loading').show();
			//ajax call to reset submission status
			$.ajax({
				type: 'POST',
				url: '/time-card/accountant-reset/',
				data: data,
				success: function(resp) {
					if(resp){
						$('#isSubmitted').val('');
						$('#loading').hide();
						//execute next action based on trigger
						//handle potentially passing selected cell from grid view click
						if(dataString){
							deactivateTimeReason(action, dataString);
						}else{
							deactivateTimeReason(action);
						}
					}
				}
			});
			
		}
	});
}

//pass deactivate action and data to universial time reason modal
function deactivateTimeReason(action, data = null){
	$('#timeReasonModal').modal('show').find('#timeReasonModalContentSpan').html("Loading...");
	//build data object
	data = {Action: action, Data: data};
	//Fetch modal content via pjax
	$.pjax.reload({
		type: 'POST',
		replace:false,
		push:false,
		url: '/task/deactivate-time-reason',
		data: data,
		container: '#timeReasonModalContentSpan', // id to update content
		timeout: 99999
	})
}