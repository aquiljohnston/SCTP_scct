$(function(){
    var jqMileageCardFilter = $('#mileage_card_filter');
    var jqMCDropDowns = $('#mileageCardDropdownContainer');
    var jqWeekSelection = jqMileageCardFilter.find('#mileageCardDateRange');
    var jqMCPageSize = jqMCDropDowns.find('#mileageCardPageSize');
	var mileageProjectFilterDD = $('#mileageProjectFilterDD');
    mileageEntries = [];           
    mileageCardPmSubmit();
	
	$(document).ready(function () {
		if(jqWeekSelection.length > 0)
		{
			//set to and from values for date picker based on current date range
			refreshDatePicker();	
		}
	});
	
	$(document).off('change', "#mileageCardDateRange").on('change', "#mileageCardDateRange", function (event) {
        event.preventDefault();
        var selected = $(this).find(":selected").val();
        if(selected == "other") {
			//reset date picker
			resetDatePicker();
			//show date picker
            $('#mileageDatePickerContainer').css("display", "block");
        }else {
			//hide date picker
            $('#mileageDatePickerContainer').css("display", "none");
            reloadMileageCardGridView();
        }
    });

    $(document).off('change', "#mileageCardPageSize").on('change', "#mileageCardPageSize", function (event) {
        $('#mileageCardPageNumber').val(1);
        reloadMileageCardGridView();
        event.preventDefault();
        return false;
    });

    $(document).off('change', '#mileageProjectFilterDD').on('change', '#mileageProjectFilterDD', function (event) {
		$('#mileageEmployeeFilterDD').val("All");
        reloadMileageCardGridView();
        event.preventDefault();
        return false;
    });
	
	$(document).off('change', '#mileageEmployeeFilterDD').on('change', '#mileageEmployeeFilterDD', function (event) {
        reloadMileageCardGridView();
        event.preventDefault();
        return false;
    });

    $(document).off('click', "#MCPagination ul li a").on('click', "#MCPagination ul li a", function (event) {
        var page = $(this).data('page') + 1; // Shift by one to 1-index instead of 0-index.
        $('#mileageCardPageNumber').val(page);
        reloadMileageCardGridView();
        event.preventDefault();
        return false;
    });
	
	// mileagecard filter listener
    $(document).off('keypress', '#mileageCardFilter').on('keypress', '#mileageCardFilter', function (event) {
        if (event.keyCode === 13 || event.keyCode === 10) {
            event.preventDefault();
			$('#mileageCardPageNumber').val(1);
            reloadMileageCardGridView();
        }
    });

    $(document).off('click', '#mileageCardSearchCleanFilterButton').on('click', '#mileageCardSearchCleanFilterButton', function (){
        $('#mileageCardFilter').val("");
		$('#mileageCardPageNumber').val(1);
        reloadMileageCardGridView();
    }); 
	
	//TODO look into extracting out because of redundancies in time card js
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
	
	//TODO look into extracting out because of redundancies in time card js
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

function mileageCardPmSubmit() {
	// redundant method; same as multiple_approve_btn_id in approve_multiple_mileagecard.js
	$('#pm_submit_btn_id').on('click').click(function (event) {
		var projectID = new Array();
		if($('#mileageProjectFilterDD option:selected').text().toLowerCase() == 'All'.toLowerCase() || $('#mileageProjectFilterDD').val().toLowerCase() == '< All >'.toLowerCase()) {
			// get all project ids
			projectID = new Array();
			for ( var i = 0, len = mileageProjectFilterDD.options.length; i < len; i++ ) {
				opt = mileageProjectFilterDD.options[i];
				if(opt.value.length > 0)
					projectID.push(opt.value);
			}
		} else
			projectID.push($('#mileageProjectFilterDD').val());
		var dateRangeArray = $('#mileageCardDateRange').val().split(',');
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
					url: '/mileage-card/p-m-submit',
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

function reloadMileageCardGridView() {
	var form = $('#mileageCardDropdownContainer').find("#MileageCardForm");
	//get sort value
	var ascSort = $("#GridViewForMileageCard-container").find(".asc").attr('data-sort');
	var descSort = $("#GridViewForMileageCard-container").find(".desc").attr('data-sort');
	var sort = (ascSort !== undefined) ? ascSort.replace('-', ''): '-' + descSort;
	//append sort to form values
	var dataParams = form.serialize() + "&sort=" + sort;
	if (form.find(".has-error").length){
		return false;
	}
	$('#loading').show();
	$.pjax.reload({
		type: 'GET',
		url: form.attr("action"),
		container: '#mileageCardGridview', // id to update content
		data: dataParams,
		timeout: 99999
	});
	$('#mileageCardGridview').off('pjax:success').on('pjax:success', function () {
		$.pjax.reload({
			container: '#mileageSubmitApproveButtons',
			timeout:false,
		}).done(function (){
				//reload dropdown values
				$.pjax.reload({container: '#mileageCardDropDownPjax', async:false});
				if($('#multiple_submit_btn_id').hasClass('off-btn')){
					$('#multiple_submit_btn_id').attr("title", "Not all time cards have been approved.");
				} 
				if($('#multiple_submit_btn_id').attr('submitted') == 'true'){
					$('#multiple_submit_btn_id').attr("title", "All time cards have been submitted.");
				}
			});
		$('#mileageSubmitApproveButtons').off('pjax:success').on('pjax:success', function () {
			applyMileageCardOnClickListeners();
			applyMileageCardSubmitButtonListener();
			pmSubmit();
			$('#loading').hide();
		});
		$('#mileageSubmitApproveButtons').off('pjax:error').on('pjax:error', function () {
			location.reload();
		});
	});
	$('#mileageCardGridview').off('pjax:error').on('pjax:error', function () {
		location.reload();
	});
}
