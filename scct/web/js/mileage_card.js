$(function(){
    var jqMileageCardFilter = $('#mileage_card_filter');
    var jqMCDropDowns = $('#mileageCardDropdownContainer');
    var jqWeekSelection = jqMileageCardFilter.find('#mileageCardDateRange');
    var jqMCPageSize = jqMCDropDowns.find('#mileageCardPageSize');
	var mileageProjectFilterDD = $('#mileageProjectFilterDD');
    mileageEntries = [];           
    mileageCardPmSubmit();
	mileageCardAccountantSubmit();
	
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
	
	$(document).off('click', '#mileageCardClearDropdownFilterButton').on('click', '#mileageCardClearDropdownFilterButton', function (){
        $('#mileageProjectFilterDD').val("All");
        $('#mileageEmployeeFilterDD').val("All");
		$('#mileageCardPageNumber').val(1);
        reloadMileageCardGridView();
    });

    $(document).off('click', '#mileageCardSearchCleanFilterButton').on('click', '#mileageCardSearchCleanFilterButton', function (){
        $('#mileageCardFilter').val("");
		$('#mileageCardPageNumber').val(1);
        reloadMileageCardGridView();
    }); 
});

function mileageCardPmSubmit() {
	$('#mileage_pm_submit_btn_id').on('click').click(function (event) {
		//check css class that gives
        //the appearance of being disabled via css
        //add returns false to prevent submission in
        //this state.
		//mimics accountant submit may be be better to just disable since no tooltip exist
        if($(this).hasClass('off-btn')){
			return false;
        }
		
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

function mileageCardAccountantSubmit() {
	//apply tooltip for button status
	$( document ).tooltip();
    
    if($('#mileage_acc_submit_btn_id').hasClass('off-btn')){
		$('#mileage_acc_submit_btn_id').attr("title", "Not all mileage cards have been approved.");
    } 
    if($('#mileage_acc_submit_btn_id').attr('submitted') == 'true'){
		$('#mileage_acc_submit_btn_id').attr("title", "All mileage cards have been submitted.");
    }
	
    $('#mileage_acc_submit_btn_id').on('click').click(function (event) {
        //apply css class that gives the tooltip gives
        //the appearance of being disabled via css
        //add returns false to prevent submission in
        //this state.
        if($(this).hasClass('off-btn')){
			return false;
        }

        var quantifier  = "";
        var projectIDs  = [];
        
        $('#mileageProjectFilterDD option').each(function(){
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

        var primaryKeys = $('#GridViewForMileageCard').yiiGridView('getSelectedRows');
        if(primaryKeys.length <= 1 ) {
            quantifier = "this item?";
        } else {
            quantifier = "these items?"
        }

        krajeeDialog.defaults.confirm.title = 'Submit';
        krajeeDialog.confirm('Are you sure you want to submit ' + quantifier, function (confirmation) {
			if (confirmation) {
				$('#loading').show();
				
				//usage $.ctGrow(msg,title,boostrap text class)
				$.ctGrowl.msg('Initiating the Submission.','Success','bg-success');

				payload = {
					projectIDs : projectIDs,
					weekStart : weekStart,
					weekEnd : weekEnd,
				}
				
				$.ajax({
					type: 'POST',
					url: '/mileage-card/accountant-submit',
					data:payload,
					success: function(response) {
						response = JSON.parse(response);
						if(response.success){
							$.ctGrowl.msg(response.message,'Success','bg-success');
							reloadMileageCardGridView();
						} else {
							$.ctGrowl.msg(response.message,'Error','bg-danger');
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
			});
		$('#mileageSubmitApproveButtons').off('pjax:success').on('pjax:success', function () {
			applyMileageCardOnClickListeners();
			mileageCardAccountantSubmit();
			mileageCardPmSubmit();
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
