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

	$(document).on('click','#deactive_timeEntry_btn_id',function(e){
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
		entries.push({taskName : tasks, timeCardID : timeCardID});
		tasks.join(', ');
		data = {entries};

		krajeeDialog.defaults.confirm.title = 'Deactivate All Task';
		krajeeDialog.confirm('Are you sure you want to deactivate all ' +tasks+ '? Please confirm...', function (resp) {
			if (resp) {
				$('#loading').show();
				$.ajax({
					type: 'POST',
					url: '/time-card/deactivate/',
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

	//apply listeners on cells with data for deactivation
	$(document).off('click', '#allTaskEntries tbody tr td').on('click', '#allTaskEntries tbody tr td',function (){
		id = $('#timeCardId').val();
		seq_num = $(this).attr('data-col-seq');
		taskName = $(this).closest('tr').find("td[data-col-seq='0']").text();
		date = $("tr[data-key='0']").find("td[data-col-seq='"+seq_num+"']").text();
		var entries = [];
		//clean up date format for sending
		date = date.replace(/\-/g, '/');

		//restrict click to only day of the week fields
		//with values in the .text()
		if($(this).attr('data-col-seq') >=1 && ($(this).text()!="")
			&& (!$("#approve_timeCard_btn_id").prop("disabled") || $('#isAccountant').val())
			&& !$('#isSubmitted').val())
		{
			krajeeDialog.defaults.confirm.title = 'Deactivate Time Entry';
			krajeeDialog.confirm('Are you sure you want to deactivate this time entry for '+date+'?', function (resp) {
				if (resp) {
					$('#loading').show();
					//build and send payload to deactivate single entry
					entries.push({taskName : [taskName], day : date, timeCardID : id});
					data = {entries};
					$.ajax({
						type: 'POST',
						url: '/time-card/deactivate/',
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
	});
	
	//listener on add task button to launch modal and pass data to it
	$(document).off('click', '.add_task_btn').on('click', '.add_task_btn', function (){
		var weekStart = $("table th").eq(1).attr('class');
        var weekEnd = $("table th").eq(7).attr('class');
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
    });
}

function validateTaskToolTip() {
    $.each($('#allTaskEntries tbody tr td'),function(){
        if($(this).attr('data-col-seq') >=1 && ($(this).text()!="") && ($(this).parent().attr('data-key')>0)
            && (!$("#approve_timeCard_btn_id").prop("disabled"))) 
		{
            $(this).attr("title","Click to deactivate this time entry!")
        } 
		else if ($('#isAccountant').val() && !$('#isSubmitted').val() &&
			$(this).attr('data-col-seq') >=1 &&
			($(this).text()!="") &&
			($(this).parent().attr('data-key')>0))
		{
            $(this).attr("title","Click to deactivate this time entry!")
        }
    });
}

function validateTaskCheckEnabled() {
    $(".entryData").each(function(){
        if ($("#approve_timeCard_btn_id").prop("disabled")
            && ($('#isSubmitted').val() || !$('#isAccountant').val())) {
            $(this).prop('disabled',true);
        }
    });
}