$(function(){
    var jqTimeCardFilter    = $('#timecard_filter');
    var jqTCDropDowns       = $('#timeCardDropdownContainer');
    var jqWeekSelection     = jqTimeCardFilter.find('#timeCardDateRange');
    var jqTCPageSize        = jqTCDropDowns.find('#timeCardPageSize');
    var projectFilterDD     = $('#projectFilterDD');
    entries                 = [];           
    pmSubmit();
	$(document).ready(function () {
		if(jqWeekSelection.length > 0)
		{
			//set to and from values for date picker based on current date range
			refreshDatePicker();	
		}
	});
	
    $(document).off('change', "#timeCardDateRange").on('change', "#timeCardDateRange", function (event) {
        event.preventDefault();
        var selected = $(this).find(":selected").val();
        if(selected == "other") {
			//reset date picker
			resetDatePicker();
			//show date picker
            $('#datePickerContainer').css("display", "block");
        }else {
			//hide date picker
            $('#datePickerContainer').css("display", "none");
            reloadTimeCardGridView();
        }
    });

    $(document).off('change', "#timeCardPageSize").on('change', "#timeCardPageSize", function (event) {
        $('#timeCardPageNumber').val(1);
        reloadTimeCardGridView();
        event.preventDefault();
        return false;
    });

     $(document).off('change', '#projectFilterDD').on('change', '#projectFilterDD', function (event) {
        reloadTimeCardGridView();
        event.preventDefault();
        return false;
    });

    $(document).off('click', "#TCPagination ul li a").on('click', "#TCPagination ul li a", function (event) {
        var page = $(this).data('page') + 1; // Shift by one to 1-index instead of 0-index.
        $('#timeCardPageNumber').val(page);
        reloadTimeCardGridView();
        event.preventDefault();
        return false;
    });
	
	// timecard filter listener
    $(document).off('keypress', '#timeCardFilter').on('keypress', '#timeCardFilter', function (event) {
        if (event.keyCode === 13 || event.keyCode === 10) {
            event.preventDefault();
			$('#timeCardPageNumber').val(1);
            reloadTimeCardGridView();
        }
    });

    $(document).off('click', '#timeCardSearchCleanFilterButton').on('click', '#timeCardSearchCleanFilterButton', function (){
        $('#timeCardFilter').val("");
		$('#timeCardPageNumber').val(1);
        reloadTimeCardGridView();
    });

	//function called when other is selected in week dropdown to reset widget to default
	function resetDatePicker(){
		//get date picker object
		var datePicker = $('#dynamicmodel-daterangepicker-container').data('daterangepicker');
		//create default start end
		var fm = moment().startOf('day') || '';
		var to = moment() || '';
		//set default selections in widget
		datePicker.setStartDate(fm);
		datePicker.setEndDate(to);
		//set default date range
		daterange = fm.format('YYYY-MM-DD') + ' - ' + to.format('YYYY-MM-DD');
		$('#dynamicmodel-daterangepicker-container').find('.kv-drp-dropdown').find('.range-value').html(daterange);
	}
	
	//function to set to and from values for date picker based on current date range
	function refreshDatePicker(){
		//get current date range
		dateRange = $('#dynamicmodel-daterangepicker-container').find('.kv-drp-dropdown').find('.range-value').html();
		//probably a cleaner way to determine if is the initial page load vs refresh
		//check if initial page load and skip refresh if this is the case
		if(dateRange.indexOf('text-muted') > -1) return;
		//parse date range
		dateRangeArray = dateRange.split(' ');
		var fm = moment(dateRangeArray[0]);
		var to = moment(dateRangeArray[2]);
		//get date picker object
		var datePicker = $('#dynamicmodel-daterangepicker-container').data('daterangepicker');
		//set date range values
		datePicker.setStartDate(fm);
		datePicker.setEndDate(to);
	}
});

function pmSubmit() {
	// redundant method; same as multiple_approve_btn_id in approve_multiple_timecard.js
	$('#pm_submit_btn_id').on('click').click(function (event) {
		var projectID = new Array();
		if($('#projectFilterDD option:selected').text().toLowerCase() == 'All'.toLowerCase() || $('#projectFilterDD').val().toLowerCase() == '< All >'.toLowerCase()) {
			// get all project ids
			projectID = new Array();
			for ( var i = 0, len = projectFilterDD.options.length; i < len; i++ ) {
				opt = projectFilterDD.options[i];
				if(opt.value.length > 0)
					projectID.push(opt.value);
			}
		} else
			projectID.push($('#projectFilterDD').val());
		var dateRangeArray = $('#timeCardDateRange').val().split(',');
		if(dateRangeArray.length == 1) {
			dateRangeArray = $('#dynamicmodel-daterangepicker-container').find('.kv-drp-dropdown').find('.range-value').html().split(" - ");
			var selectedDate = new Date(dateRangeArray[0]);
			var prevSunday = new Date(selectedDate.setDate(selectedDate.getDate()-selectedDate.getDay()));
			dateRangeArray[0] = prevSunday.getFullYear() + "-"+(prevSunday.getMonth()+1)+"-"+prevSunday.getDate(); // getMonth is 0 indexed
		}
		console.log("date range: " + JSON.stringify(dateRangeArray) + ", projects: " + JSON.stringify(projectID));
		krajeeDialog.defaults.confirm.title = 'Submit';
		krajeeDialog.confirm('Are you sure you want to submit the selected items?', function (resp) {
			if (resp) {
				$('#loading').show();
				$.ajax({
					type: 'POST',
					url: '/time-card/p-m-submit',
					data: {
						projectIDArray: projectID,
						dateRangeArray: dateRangeArray
					},
					success: function(data){
						$('#loading').hide();
					}
				});
			} else {
				event.stopImmediatePropagation();
				event.preventDefault();
			}
		});
	});
}

  $( function() {
    $( document ).tooltip();
    
    if($('#multiple_submit_btn_id').hasClass('off-btn')){
       $('#multiple_submit_btn_id').attr("title", "Not all time cards have been approved.");
    } 
    if($('#multiple_submit_btn_id').attr('submitted') == 'true'){
      $('#multiple_submit_btn_id').attr("title", "All time cards have been submitted.");
    }
});

//reload table
function reloadTimeCardGridView() {
	var form = $('#timeCardDropdownContainer').find("#TimeCardForm");
	if (form.find(".has-error").length){
		return false;
	}
	$('#loading').show();
	$.pjax.reload({
		type: 'GET',
		url: form.attr("action"),
		container: '#timeCardGridview', // id to update content
		data: form.serialize(),
		timeout: 99999
	});
	$('#timeCardGridview').off('pjax:success').on('pjax:success', function () {
		$.pjax.reload({
			container: '#submitApproveButtons',
			timeout:false,
		}).done(function (){
			$.pjax.reload({container: '#projectDropDownPjax', async:false});
			if($('#multiple_submit_btn_id').hasClass('off-btn')){
		   $('#multiple_submit_btn_id').attr("title", "Not all time cards have been approved.");
			} 
			if($('#multiple_submit_btn_id').attr('submitted') == 'true'){
				 $('#multiple_submit_btn_id').attr("title", "All time cards have been submitted.");
			}

			 });
		$('#submitApproveButtons').off('pjax:success').on('pjax:success', function () {
			applyTimeCardOnClickListeners();
			applyTimeCardSubmitButtonListener();
			pmSubmit();
			$('#loading').hide();
		});
		$('#submitApproveButtons').off('pjax:error').on('pjax:error', function () {
			location.reload();
		});
	});
	$('#timeCardGridview').off('pjax:error').on('pjax:error', function () {
		location.reload();
	});
}
