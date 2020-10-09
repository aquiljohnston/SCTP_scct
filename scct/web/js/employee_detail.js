$(function () {


        if ($('#DailyBreakdownHours').length > 0) {
			applyEmployeeDetailListeners();
            editTimeToolTip();
        }

	//
	addTask();
	timeOfDayCheckbox();
	setTaskId();
	reloadTaskDropdownListner();


});

function applyEmployeeDetailListeners() {
	$(document).off('click', '#DailyBreakdownHours tbody tr').on('click', '#DailyBreakdownHours tbody tr',function (){
		//get current user for project dropdown
		userID = $('#userID').val();
		date = $('#date').val();
		//get current row of data
		id = $(this).find("td[data-col-seq='0']").text();
		projectID = $(this).find("td[data-col-seq='1']").text();
		taskID = $(this).find("td[data-col-seq='3']").text();
		taskName = $(this).find("td[data-col-seq='4']").text();
		startTime = $(this).find("td[data-col-seq='5']").text();
		endTime = $(this).find("td[data-col-seq='6']").text();
		//grab previous row of data
		prevRow = $(this).prev('tr');
		prevId = prevRow.find("td[data-col-seq='0']").text();
		prevProjectID = prevRow.find("td[data-col-seq='1']").text();
		prevTaskID = prevRow.find("td[data-col-seq='3']").text();
		prevTaskName = prevRow.find("td[data-col-seq='4']").text();
		prevStartTime = prevRow.find("td[data-col-seq='5']").text();
		prevEndTime = prevRow.find("td[data-col-seq='6']").text();
		//grab next row of data
		nextRow = $(this).next('tr');
		nextId = nextRow.find("td[data-col-seq='0']").text();
		nextProjectID = nextRow.find("td[data-col-seq='1']").text();
		nextTaskID = nextRow.find("td[data-col-seq='3']").text();
		nextTaskName = nextRow.find("td[data-col-seq='4']").text();
		nextStartTime = nextRow.find("td[data-col-seq='5']").text();
		nextEndTime = nextRow.find("td[data-col-seq='6']").text();
		
		data = {
			Current: {ID: id, ProjectID: projectID, TaskID: taskID, TaskName: taskName, StartTime: startTime, EndTime: endTime},
			Prev: {ID: prevId, ProjectID: prevProjectID, TaskID: prevTaskID, TaskName: prevTaskName, StartTime: prevStartTime, EndTime: prevEndTime},
			Next: {ID: nextId, ProjectID: nextProjectID, TaskID: nextTaskID, TaskName: nextTaskName, StartTime: nextStartTime, EndTime: nextEndTime}
		};
		
		$('#editTimeModal').modal('show').find('#editTimeModalContentSpan').html("Loading...");
		$.pjax.reload({
			type: 'POST',
			replace: false,
			url: '/employee-approval/employee-detail-modal?userID=' + userID + '&date=' + date,
			data: data,
			container: '#editTimeModalContentSpan',
			timeout: 99999
		});
		
	});
}

function editTimeToolTip() {
	//apply edit tooltip on table
	$.each($('#DailyBreakdownHours tbody tr'),function(){
		$(this).attr("title","Click to edit.")
	});
}

// serializes form data including disabled fields
$.fn.serializeIncludeDisabled = function() {
	let disabled = this.find(':input:disabled').removeAttr('disabled');
	let serialized = this.serialize();
	disabled.attr('disabled', 'disabled');
	return serialized;
};


// send add task request
function addTask() {

	$('body').on('click', '#employee_detail_add_task_submit_btn', function(e) {

		//
		let formData = $('#EmployeeDetailAddTaskModalForm').
			serializeIncludeDisabled();

		$('#loading').show();

		// let url = '/employee-approval/add-task?userID=" . $userID . "&date=" . $date . "';
		let url = $(this).data('url');

		$.ajax({
			url: url,
			data: formData,
			type: 'POST',
			dataType: 'JSON',
		}).done(function(data) {

			if (data.success) {
				$('#addTaskModal').modal('hide');
				$.pjax.reload({container: '#EmployeeDetailView', async: false});
			} else {
				alert(data.msg);
			}
			$('#loading').hide();

		}).fail(function(jqXHR, textStatus, errorThrown) {
			alert(errorThrown);
			$('#loading').hide();
		});
	});
}

// handles enabling / disabling input fields based on morning or afternoon selection
function timeOfDayCheckbox(){

	//
	$('body').on('click', '.time-of-day-checkbox', function(e) {

		let timeOfDay = $(this).data('time-of-day');
		let time = $(this).data('time');
		let isChecked = $(this).is(':checked');

		// remove disabled attrs
		$('#employeedetailtime-starttime').removeAttr('disabled');
		$('#employeedetailtime-endtime').removeAttr('disabled');
		$('#employee_detail_add_task_submit_btn').removeAttr('disabled');

		if (timeOfDay == 'morning') {

			if (isChecked) {

				//
				$('#employeedetailtime-endtime').val(time);
				$('#employeedetailtime-endtime').attr('disabled', true);

				//
				$('#employeedetailtime-starttime').val('');
				$('#employeedetailtime-starttime').removeAttr('disabled');

				$('#employeedetailtime-timeofdayname').val('morning');
			}

		} else if (timeOfDay == 'afternoon') {

			if (isChecked) {

				//
				$('#employeedetailtime-starttime').val(time);
				$('#employeedetailtime-starttime').attr('disabled', true);

				//
				$('#employeedetailtime-endtime').val('');
				$('#employeedetailtime-endtime').removeAttr('disabled');

				$('#employeedetailtime-timeofdayname').val('afternoon');
			}
		}
	});
}

// sets taskID hidden input
function setTaskId() {
	$('body').on('change', '#employeedetailtime-taskid', function(e) {

		let taskName = $('#employeedetailtime-taskid option:selected').text();
		$('#employeedetailtime-taskname').val('Task ' + taskName);
	});
}

//
function reloadTaskDropdownListner(){
	$(document).
		off('change', '#employeedetailtime-projectid').
		on('change', '#employeedetailtime-projectid', function() {
			reloadTaskDropdown();
		});
}


// reloads task dropdown
function reloadTaskDropdown() {
	//get current user for project dropdown
	let userID = $('#userID').val();
	let date = $('#date').val();

	$('#loading').show();
	$.pjax.reload({
		type: 'POST',
		replace: false,
		url: '/employee-approval/add-task-modal?userID=' + userID + '&date=' + date,
		data: {projectID: $('#employeedetailtime-projectid').val()},
		container: '#addTaskDropDownPjax',
		timeout: 99999,
	});
	$('#addTaskDropDownPjax').
		off('pjax:success').
		on('pjax:success', function() {
			$('#loading').hide();
		});
}