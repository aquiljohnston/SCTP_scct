$(function(){
    var jqTimeCardFilter = $('#timecard_filter');
    var jqTCDropDowns = $('#timeCardDropdownContainer');
    var jqWeekSelection = jqTimeCardFilter.find('#timeCardDateRange');
    var jqTCPageSize = jqTCDropDowns.find('#timeCardPageSize');
    var timeCardProjectFilterDD = $('#timeCardProjectFilterDD');
    entries = [];           
    timeCardPmSubmit();
    timeCardAccountantSubmit();
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
            $('#timeCardDatePickerContainer').css("display", "block");
        }else {
			//hide date picker
            $('#timeCardDatePickerContainer').css("display", "none");
            reloadTimeCardGridView();
        }
    });

    $(document).off('change', "#timeCardPageSize").on('change', "#timeCardPageSize", function (event) {
        $('#timeCardPageNumber').val(1);
        reloadTimeCardGridView();
        event.preventDefault();
        return false;
    });

    $(document).off('change', '#timeCardProjectFilterDD').on('change', '#timeCardProjectFilterDD', function (event) {
		$('#timeCardEmployeeFilterDD').val("All");
        reloadTimeCardGridView();
        event.preventDefault();
        return false;
    });
	
	$(document).off('change', '#timeCardEmployeeFilterDD').on('change', '#timeCardEmployeeFilterDD', function (event) {
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
	
	$(document).off('click', '#timeCardClearDropdownFilterButton').on('click', '#timeCardClearDropdownFilterButton', function (){
        $('#timeCardProjectFilterDD').val("All");
        $('#timeCardEmployeeFilterDD').val("All");
		$('#timeCardPageNumber').val(1);
        reloadTimeCardGridView();
    });

    $(document).off('click', '#timeCardSearchCleanFilterButton').on('click', '#timeCardSearchCleanFilterButton', function (){
        $('#timeCardFilter').val("");
		$('#timeCardPageNumber').val(1);
        reloadTimeCardGridView();
    });
});

function timeCardPmSubmit() {
	$('#time_card_pm_submit_btn_id').on('click').click(function (event) {
		var projectID = new Array();
		if($('#timeCardProjectFilterDD option:selected').text().toLowerCase() == 'All'.toLowerCase() || $('#timeCardProjectFilterDD').val().toLowerCase() == '< All >'.toLowerCase()) {
			// get all project ids
			projectID = new Array();
			for ( var i = 0, len = timeCardProjectFilterDD.options.length; i < len; i++ ) {
				opt = timeCardProjectFilterDD.options[i];
				if(opt.value.length > 0)
					projectID.push(opt.value);
			}
		} else
			projectID.push($('#timeCardProjectFilterDD').val());
		var dateRangeArray = $('#timeCardDateRange').val().split(',');
		if(dateRangeArray.length == 1) {
			dateRangeArray = $('#dynamicmodel-daterangepicker-container').find('.kv-drp-dropdown').find('.range-value').html().split(" - ");
			var selectedDate = new Date(dateRangeArray[0]);
			var prevSunday = new Date(selectedDate.setDate(selectedDate.getDate()-selectedDate.getDay()));
			dateRangeArray[0] = prevSunday.getFullYear() + "-"+(prevSunday.getMonth()+1)+"-"+prevSunday.getDate(); // getMonth is 0 indexed
		}
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

function timeCardAccountantSubmit() {
	//apply tooltip for button status
	$( document ).tooltip();
    
    if($('#time_card_submit_btn_id').hasClass('off-btn')){
		$('#time_card_submit_btn_id').attr("title", "Not all time cards have been approved.");
    } 
    if($('#time_card_submit_btn_id').attr('submitted') == 'true'){
		$('#time_card_submit_btn_id').attr("title", "All time cards have been submitted.");
    }
	
    $('#time_card_submit_btn_id').off('click').click(function (event) {
        //apply css class that gives the tooltip gives
        //the appearance of being disabled via css
        //add returns false to prevent submission in
        //this state.
        if($(this).hasClass('off-btn')){
            return false;
        }

        var quantifier = "";
        var projectIDs = [];
        
        $('#timeCardProjectFilterDD option').each(function(){
            if($(this).val()!=""){
                projectIDs.push($(this).val());
            }
        })

        dateRangeDD = $('[name="DynamicModel[dateRangeValue]"]').val();
		//check if date picker is active
		if(dateRangeDD == 'other'){
			dateRange = $('#dynamicmodel-daterangepicker-container').find('.kv-drp-dropdown').find('.range-value').html();
			dateRange = dateRange.split(" - ");
		}else{
			dateRange = dateRangeDD.split(",");
		}
        weekStart = dateRange[0];
        weekEnd = $.trim(dateRange[1]);

        var primaryKeys = $('#GridViewForTimeCard').yiiGridView('getSelectedRows');
        if(primaryKeys.length <= 1 ) { // We don't expect 0 or negative but we need to handle it
            quantifier = "this item?";
        } else {
            quantifier = "these items?"
        }

        krajeeDialog.defaults.confirm.title = 'Submit';
        krajeeDialog.confirm('Are you sure you want to submit ' + quantifier, function (resp) {
			if (resp) {
				$('#loading').show();
				
				var primaryKeys = $('#GridViewForTimeCard').yiiGridView('getSelectedRows');

				//usage $.ctGrow(msg,title,boostrap text class)
				$.ctGrowl.msg('Initiating the Submission.','Success','bg-success');

				payload = {
					projectIDs : projectIDs,
					weekStart : weekStart,
					weekEnd : weekEnd
				}
					   
				$.ajax({
					type: 'POST',
					url: '/time-card/accountant-submit',
					data:payload,
					success: function(data) {
						data = JSON.parse(data);
						if(data.success){
							$.ctGrowl.msg(data.message,'Success','bg-success');
							//calls time_card.js reload function
							reloadTimeCardGridView();
						} else {
							$.ctGrowl.msg(data.message,'Error','bg-danger');
							$('#loading').hide();
						}
					}
				});
			} else {
				event.stopImmediatePropagation();
				event.preventDefault();
			}
		}) 
    });
}

//reload table
function reloadTimeCardGridView() {
	var form = $('#timeCardDropdownContainer').find("#TimeCardForm");
	//get sort value
	var ascSort = $("#GridViewForTimeCard-container").find(".asc").attr('data-sort');
	var descSort = $("#GridViewForTimeCard-container").find(".desc").attr('data-sort');
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
		container: '#timeCardGridview', // id to update content
		data: dataParams,
		timeout: 99999
	});
	$('#timeCardGridview').off('pjax:success').on('pjax:success', function () {
		$.pjax.reload({
			container: '#timeCardSubmitApproveButtons',
			timeout:false,
		}).done(function (){
				//reload dropdown values
				$.pjax.reload({container: '#timeCardDropDownPjax', async:false});
			});
		$('#timeCardSubmitApproveButtons').off('pjax:success').on('pjax:success', function () {
			applyTimeCardOnClickListeners();
			timeCardAccountantSubmit();
			timeCardPmSubmit();
			$('#loading').hide();
		});
		$('#timeCardSubmitApproveButtons').off('pjax:error').on('pjax:error', function () {
			location.reload();
		});
	});
	$('#timeCardGridview').off('pjax:error').on('pjax:error', function () {
		location.reload();
	});
}
