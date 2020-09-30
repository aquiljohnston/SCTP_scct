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
		//get current row of data
		id = $(this).find("td[data-col-seq='0']").text();
		projectID = $(this).find("td[data-col-seq='1']").text();
		projectName = $(this).find("td[data-col-seq='2']").text();
		task = $(this).find("td[data-col-seq='3']").text();
		startTime = $(this).find("td[data-col-seq='4']").text();
		endTime = $(this).find("td[data-col-seq='5']").text();
		//grab previous row of data
		prevRow = $(this).prev('tr');
		prevProjectID = prevRow.find("td[data-col-seq='0']").text();
		prevId = prevRow.find("td[data-col-seq='1']").text();
		prevProjectName = prevRow.find("td[data-col-seq='2']").text();
		prevTask = prevRow.find("td[data-col-seq='3']").text();
		prevStartTime = prevRow.find("td[data-col-seq='4']").text();
		prevEndTime = prevRow.find("td[data-col-seq='5']").text();
		//grab next row of data
		nextRow = $(this).next('tr');
		nextProjectID = nextRow.find("td[data-col-seq='0']").text();
		nextId = nextRow.find("td[data-col-seq='1']").text();
		nextProjectName = nextRow.find("td[data-col-seq='2']").text();
		nextTask = nextRow.find("td[data-col-seq='3']").text();
		nextStartTime = nextRow.find("td[data-col-seq='4']").text();
		nextEndTime = nextRow.find("td[data-col-seq='5']").text();
		
		data = {
			Current: {ID: id, ProjectID: projectID, ProjectName: projectName, Task: task, StartTime: startTime, EndTime: endTime},
			Prev: {ID: prevId, ProjectID: prevProjectID,ProjectName: prevProjectName, Task: prevTask, StartTime: prevStartTime, EndTime: prevEndTime},
			Next: {ID: nextId, ProjectID: nextProjectID,ProjectName: nextProjectName, Task: nextTask, StartTime: nextStartTime, EndTime: nextEndTime}
		};
		
		$('#editTimeModal').modal('show').find('#editTimeModalContentSpan').html("Loading...");
		$.pjax.reload({
			type: 'POST',
			replace: false,
			url: '/employee-approval/employee-detail-modal?userID=' + userID,
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