$(function(){
    var jqTimeCardFilter = $('#timecard_filter');
    var jqTCDropDowns = $('#timeCardDropdownContainer');
    var jqWeekSelection = jqTimeCardFilter.find('#timeCardDateRange');
    var jqTCPageSize = jqTCDropDowns.find('#timeCardPageSize');
    var timeCardProjectFilterDD = $('#timeCardProjectFilterDD');
    entries = [];   
	timeCardApproveMultiple();
    timeCardPmSubmit();
    timeCardAccountantSubmit();
	timeCardPMReset();
	timeCardRequestPMReset();
	//may need to add to index in pluginEvent
	$.ctGrowl.init( { position: 'absolute', bottom: '70px', left: '8px' });
	
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
            reloadTimeCardGridView(1);
        }
    });

    $(document).off('change', "#timeCardPageSize").on('change', "#timeCardPageSize", function (event) {
        reloadTimeCardGridView(1);
        event.preventDefault();
        return false;
    });

	$(document).off('change', '#timeCardClientFilterDD').on('change', '#timeCardClientFilterDD', function (event) {
		$('#timeCardProjectFilterDD').val("All");
		$('#timeCardEmployeeFilterDD').val("All");
        reloadTimeCardGridView(1);
        event.preventDefault();
        return false;
    });

    $(document).off('change', '#timeCardProjectFilterDD').on('change', '#timeCardProjectFilterDD', function (event) {
		$('#timeCardEmployeeFilterDD').val("All");
        reloadTimeCardGridView(1);
        event.preventDefault();
        return false;
    });
	
	$(document).off('change', '#timeCardEmployeeFilterDD').on('change', '#timeCardEmployeeFilterDD', function (event) {
        reloadTimeCardGridView(1);
        event.preventDefault();
        return false;
    });

    $(document).off('click', "#TCPagination ul li a").on('click', "#TCPagination ul li a", function (event) {
		event.preventDefault();
        var page = $(this).data('page') + 1; // Shift by one to 1-index instead of 0-index.
        reloadTimeCardGridView(page);
    });
	
	// timecard filter listener
    $(document).off('keypress', '#timeCardFilter').on('keypress', '#timeCardFilter', function (event) {
        if (event.keyCode === 13 || event.keyCode === 10) {
            event.preventDefault();
            reloadTimeCardGridView(1);
        }
    });
	
	$(document).off('click', '#timeCardClearDropdownFilterButton').on('click', '#timeCardClearDropdownFilterButton', function (){
        $('#timeCardClientFilterDD').val("All");
        $('#timeCardProjectFilterDD').val("All");
        $('#timeCardEmployeeFilterDD').val("All");
        reloadTimeCardGridView(1);
    });

    $(document).off('click', '#timeCardSearchCleanFilterButton').on('click', '#timeCardSearchCleanFilterButton', function (){
        $('#timeCardFilter').val("");
        reloadTimeCardGridView(1);
    });

    $(document).off('change', "#timeCardGV input[type=checkbox]").on('change', "#timeCardGV input[type=checkbox]", function (e) {
		//enable button when items are selected depending on what is available
        if ($("#GridViewForTimeCard").yiiGridView('getSelectedRows') != 0) {
            $('#tc_multiple_approve_btn_id').prop('disabled', true); //TO ENABLE
            $('#pm_time_card_reset').prop('disabled', true);
        } else {
            $('#tc_multiple_approve_btn_id').prop('disabled', true);
            $('#pm_time_card_reset').prop('disabled', true);
        }
    });  
	
	//add filter to extra data of expand row ajax request
	$(document).off('kvexprow:beforeLoad', '#timeCardGV').on('kvexprow:beforeLoad', '#timeCardGV', function (event, ind, key, extra) {
		//add filter data to keys
		key.Filter = $('#timeCardFilter').val();
		key.EmployeeID = $('#timeCardEmployeeFilterDD').val();
	});
});

function timeCardApproveMultiple() {	
    $('#tc_multiple_approve_btn_id').off('click').click(function (event) {
        var primaryKeys = $('#GridViewForTimeCard').yiiGridView('getSelectedRows');
        var quantifier = "";

        if(primaryKeys.length <= 1 ) { // We don't expect 0 or negative but we need to handle it
            quantifier = "this item?";
        } else {
            quantifier = "these items?"
        }

        krajeeDialog.defaults.confirm.title = 'Approve';
        krajeeDialog.confirm('Are you sure you want to approve ' + quantifier, function (resp) {
        
        if (resp) {
			$('#loading').show();
            $.ajax({
                type: 'POST',
                url: '/time-card/approve-multiple',
                data: {
                    timecardid: primaryKeys
                },
				success: function(data){
					reloadTimeCardGridView();
				}
            });
        } else {
            event.stopImmediatePropagation();
            event.preventDefault();
        }
      })
    });
}

function timeCardPmSubmit() {
	$('#time_card_pm_submit_btn_id').on('click').click(function (event) {
		//check css class that gives
        //the appearance of being disabled via css
        //add returns false to prevent submission in
        //this state.
		//mimics accountant submit may be be better to just disable since no tooltip exist
        if($(this).hasClass('off-btn')){
			return false;
        }
		
		var projectID = timeCardGetSelectedProjectID();
		var dateRangeArray = $('#timeCardDateRange').val().split(',');
		//if the range value is 'other'
		if(dateRangeArray.length == 1) {
			dateRangeArray = $('#dynamicmodel-daterangepicker-container').find('.kv-drp-dropdown').find('.range-value').html().split(" - ");
			var selectedDate = new Date(dateRangeArray[0]);
			selectedDate.setHours(selectedDate.getHours()+(selectedDate.getTimezoneOffset()/60));//to handle timezone offset on sat/sun
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
						reloadTimeCardGridView();
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

function timeCardRequestPMReset() {
	$('#tc_pm_reset_request_btn_id').on('click').click(function (event) {
		var projectID = timeCardGetSelectedProjectID();		
		var dateRangeArray = $('#timeCardDateRange').val().split(',');
		//TODO see when this is triggered. 
		if(dateRangeArray.length == 1) {
			dateRangeArray = $('#dynamicmodel-daterangepicker-container').find('.kv-drp-dropdown').find('.range-value').html().split(" - ");
			var selectedDate = new Date(dateRangeArray[0]);
			var prevSunday = new Date(selectedDate.setDate(selectedDate.getDate()-selectedDate.getDay()));
			dateRangeArray[0] = prevSunday.getFullYear() + "-"+(prevSunday.getMonth()+1)+"-"+prevSunday.getDate(); // getMonth is 0 indexed
		}
		krajeeDialog.defaults.confirm.title = 'Request Submission Reset';
		krajeeDialog.confirm('Are you sure you want to request a reset of submitted cards?', function (resp) {
			if (resp) {
				$('#loading').show();
				$.ajax({
					type: 'POST',
					url: '/time-card/p-m-reset-request',
					data: {
						projectIDArray: projectID,
						dateRangeArray: dateRangeArray
					},
					success: function(data){
						$('#loading').hide();
						krajeeDialog.defaults.alert.title = 'Request';
						krajeeDialog.alert('Sent Successfully.', function (resp) {
							
						});
					}
				});
			} else {
				event.stopImmediatePropagation();
				event.preventDefault();
			}
		});
	});
}

function timeCardPMReset(){
	$('#pm_time_card_reset').off('click').click(function (event) {
        var primaryKeys = $('#GridViewForTimeCard').yiiGridView('getSelectedRows');
		//removes EndDate attribute from keys
        var quantifier = "";

        if(primaryKeys.length <= 1 ) { // We don't expect 0 or negative but we need to handle it
            quantifier = "this item?";
        } else {
            quantifier = "these items?"
        }

        krajeeDialog.defaults.confirm.title = 'PM Reset';
        krajeeDialog.confirm('Are you sure you want to reset ' + quantifier, function (resp) {
			if (resp) {
				$('#loading').show();
				$.ajax({
					type: 'POST',
					url: '/time-card/p-m-reset',
					data: {
						data: primaryKeys
					},
					success: function(resp) {
						if(resp){
							reloadTimeCardGridView();
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

function timeCardGetSelectedProjectID(){
	var projectID = new Array();
	if($('#timeCardProjectFilterDD option:selected').text().toLowerCase() == 'All'.toLowerCase() || $('#timeCardProjectFilterDD').val().toLowerCase() == '< All >'.toLowerCase()) {
		// get all project ids
		for ( var i = 0, len = timeCardProjectFilterDD.options.length; i < len; i++ ) {
			opt = timeCardProjectFilterDD.options[i];
			if(opt.value.length > 0)
				projectID.push(opt.value);
		}
	} else
		projectID.push($('#timeCardProjectFilterDD').val());
	
	return projectID;
}

//reload table
//page is page to be reloaded to, if no value is sent will fetch current page
function reloadTimeCardGridView(page = null) {
	//if no page is passed get the current active page
    if(page == null){
        // Get Current page and shift by one to 1-index instead of 0-index. Shifting will cause undefined value to become NaN
        currentPage = $("#TCPagination ul li.active a").data('page') + 1;
        //if page is Not a Number becuase no pagination is present set to page 1
        page = isNaN(currentPage) ? 1 : currentPage;
    }
	var form = $('#timeCardDropdownContainer').find("#TimeCardForm");
	//get sort value
	var ascSort = $("#GridViewForTimeCard-container").find(".asc").attr('data-sort');
	var descSort = $("#GridViewForTimeCard-container").find(".desc").attr('data-sort');
	var sort = (ascSort !== undefined) ? ascSort.replace('-', ''): '-' + descSort;
	//append sort to form values
	var dataParams = form.serialize()+ "&DynamicModel%5Bpage%5D=" + page + "&sort=" + sort;
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
			timeCardApproveMultiple();
			timeCardAccountantSubmit();
			timeCardPmSubmit();
			timeCardPMReset();
			timeCardRequestPMReset();
			$('#loading').hide();
		}).off('pjax:error').on('pjax:error', function () {
			location.reload();
		});
	}).off('pjax:error').on('pjax:error', function () {
		location.reload();
	});
}
