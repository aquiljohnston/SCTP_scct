$(function () {
    $(document).ready(function () {
        if ($('#DailyBreakdownHours').length > 0) {
			applyEmployeeDetailListeners();
            editTimeToolTip();
        }
    });
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