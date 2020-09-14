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
		//get current row of data
		id = $(this).find("td[data-col-seq='0']").text();
		projectName = $(this).find("td[data-col-seq='1']").text();
		task = $(this).find("td[data-col-seq='2']").text();
		startTime = $(this).find("td[data-col-seq='3']").text();
		endTime = $(this).find("td[data-col-seq='4']").text();
		//grab previous row of data
		prevRow = $(this).prev('tr');
		prevId = prevRow.find("td[data-col-seq='0']").text();
		prevProjectName = prevRow.find("td[data-col-seq='1']").text();
		prevTask = prevRow.find("td[data-col-seq='2']").text();
		prevStartTime = prevRow.find("td[data-col-seq='3']").text();
		prevEndTime = prevRow.find("td[data-col-seq='4']").text();
		//grab next row of data
		nextRow = $(this).next('tr');
		nextId = nextRow.find("td[data-col-seq='0']").text();
		nextProjectName = nextRow.find("td[data-col-seq='1']").text();
		nextTask = nextRow.find("td[data-col-seq='2']").text();
		nextStartTime = nextRow.find("td[data-col-seq='3']").text();
		nextEndTime = nextRow.find("td[data-col-seq='4']").text();
		
		data = {
			Current: {ID: id, ProjectName: projectName, Task: task, StartTime: startTime, EndTime: endTime},
			Prev: {ID: prevId, ProjectName: prevProjectName, Task: prevTask, StartTime: prevStartTime, EndTime: prevEndTime},
			Next: {ID: nextId, ProjectName: nextProjectName, Task: nextTask, StartTime: nextStartTime, EndTime: nextEndTime}
		};
		
		$('#editTimeModal').modal('show').find('#editTimeModalContentSpan').html("Loading...");
		$.pjax.reload({
			type: 'POST',
			replace: false,
			url: '/employee-approval/employee-detail-modal/',
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