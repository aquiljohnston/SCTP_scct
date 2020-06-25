$(function(){
    var jqExpenseFilter = $('#expense_filter');
    var jqEXPDropDowns = $('#expenseDropdownContainer');
    var jqWeekSelection = jqExpenseFilter.find('#expenseDateRange');
    var jqEXPPageSize = jqEXPDropDowns.find('#expensePageSize');
    var expenseProjectFilterDD = $('#expenseProjectFilterDD');
    entries = [];   
	expenseApproveMultiple();
    expenseAccountantSubmit();
	expensePMReset();
	expenseRequestPMReset();
	//may need to add to index in pluginEvent
	$.ctGrowl.init( { position: 'absolute', bottom: '70px', left: '8px' });
	
	$(document).ready(function () {
		if(jqWeekSelection.length > 0){
			//set to and from values for date picker based on current date range
			refreshDatePicker();	
		}
	});
	
    $(document).off('change', "#expenseDateRange").on('change', "#expenseDateRange", function (event) {
        event.preventDefault();
        var selected = $(this).find(":selected").val();
        if(selected == "other") {
			//reset date picker
			resetDatePicker();
			//show date picker
            $('#expenseDatePickerContainer').css("display", "block");
        }else {
			//hide date picker
            $('#expenseDatePickerContainer').css("display", "none");
            reloadExpenseGridView(1);
        }
    });

    $(document).off('change', "#expensePageSize").on('change', "#expensePageSize", function (event) {
        reloadExpenseGridView(1);
        event.preventDefault();
        return false;
    });

	$(document).off('change', '#expenseClientFilterDD').on('change', '#expenseClientFilterDD', function (event) {
		$('#expenseProjectFilterDD').val("All");
		$('#expenseEmployeeFilterDD').val("All");
        reloadExpenseGridView(1);
        event.preventDefault();
        return false;
    });

    $(document).off('change', '#expenseProjectFilterDD').on('change', '#expenseProjectFilterDD', function (event) {
		$('#expenseEmployeeFilterDD').val("All");
        reloadExpenseGridView(1);
        event.preventDefault();
        return false;
    });
	
	$(document).off('change', '#expenseEmployeeFilterDD').on('change', '#expenseEmployeeFilterDD', function (event) {
        reloadExpenseGridView(1);
        event.preventDefault();
        return false;
    });

    $(document).off('click', "#EXPPagination ul li a").on('click', "#EXPPagination ul li a", function (event) {
		event.preventDefault();
        var page = $(this).data('page') + 1; // Shift by one to 1-index instead of 0-index.
        reloadExpenseGridView(page);
    });
	
	// timecard filter listener
    $(document).off('keypress', '#expenseFilter').on('keypress', '#expenseFilter', function (event) {
        if (event.keyCode === 13 || event.keyCode === 10) {
            event.preventDefault();
            reloadExpenseGridView(1);
        }
    });
	
	$(document).off('click', '#expenseClearDropdownFilterButton').on('click', '#expenseClearDropdownFilterButton', function (){
        $('#expenseClientFilterDD').val("All");
        $('#expenseProjectFilterDD').val("All");
        $('#expenseEmployeeFilterDD').val("All");
        reloadExpenseGridView(1);
    });

    $(document).off('click', '#expenseSearchCleanFilterButton').on('click', '#expenseSearchCleanFilterButton', function (){
        $('#expenseFilter').val("");
        reloadExpenseGridView(1);
    });

    $(document).off('change', "#expenseGV input[type=checkbox]").on('change', "#expenseGV input[type=checkbox]", function (e) {
		//enable button when items are selected depending on what is available
        if ($("#GridViewForExpense").yiiGridView('getSelectedRows') != 0) {
            $('#exp_multiple_approve_btn_id').prop('disabled', false); //TO ENABLE
            $('#pm_expense_reset').prop('disabled', false);
        } else {
            $('#exp_multiple_approve_btn_id').prop('disabled', true);
            $('#pm_expense_reset').prop('disabled', true);
        }
    });  
	
	//listener on add expense button to launch modal and pass data to it
	$(document).off('click', '#exp_add_btn_id').on('click', '#exp_add_btn_id', function (){
		addExpense();
    });
	
	//add filter to extra data of expand row ajax request
	$(document).off('kvexprow:beforeLoad', '#expenseGV').on('kvexprow:beforeLoad', '#expenseGV', function (event, ind, key, extra) {
		//add filter data to keys
		key.Filter = $('#expenseFilter').val();
		key.EmployeeID = $('#expenseEmployeeFilterDD').val();
	});
});

function expenseApproveMultiple() {	
	$('#exp_multiple_approve_btn_id').off('click').click(function (event) {
        var preProcessPrimaryKeys = $('#GridViewForExpense').yiiGridView('getSelectedRows');
		var primaryKeys = [];
		//handle rows with multiple expenses
		preProcessPrimaryKeys.forEach(function(key) {
		  primaryKeys = primaryKeys.concat(key.toString().split(','));
		});
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
                url: '/expense/approve-multiple',
                data: {
                    id: primaryKeys
                },
				success: function(data){
					reloadExpenseGridView();
				}
            });
        } else {
            event.stopImmediatePropagation();
            event.preventDefault();
        }
      })
    });
}

function expenseAccountantSubmit() {
	//apply tooltip for button status
	$( document ).tooltip();
    
    if($('#expense_submit_btn_id').hasClass('off-btn')){
		$('#expense_submit_btn_id').attr("title", "Not all expenses have been approved.");
    } 
    if($('#expense_submit_btn_id').attr('submitted') == 'true'){
		$('#expense_submit_btn_id').attr("title", "All expenses have been submitted.");
    }
	
    $('#expense_submit_btn_id').off('click').click(function (event) {
        //apply css class that gives the tooltip gives
        //the appearance of being disabled via css
        //add returns false to prevent submission in
        //this state.
        if($(this).hasClass('off-btn')){
            return false;
        }

        var quantifier = "";
        var projectIDs = [];
        
        $('#expenseProjectFilterDD option').each(function(){
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

        var primaryKeys = $('#GridViewForExpense').yiiGridView('getSelectedRows');
        if(primaryKeys.length <= 1 ) { // We don't expect 0 or negative but we need to handle it
            quantifier = "this item?";
        } else {
            quantifier = "these items?"
        }

        krajeeDialog.defaults.confirm.title = 'Submit';
        krajeeDialog.confirm('Are you sure you want to submit ' + quantifier, function (resp) {
			if (resp) {
				$('#loading').show();
				
				var primaryKeys = $('#GridViewForExpense').yiiGridView('getSelectedRows');

				//usage $.ctGrow(msg,title,boostrap text class)
				$.ctGrowl.msg('Initiating the Submission.','Success','bg-success');

				payload = {
					projectIDs : projectIDs,
					weekStart : weekStart,
					weekEnd : weekEnd
				}
					   
				$.ajax({
					type: 'POST',
					url: '/expense/accountant-submit',
					data:payload,
					success: function(data) {
						data = JSON.parse(data);
						if(data.success){
							$.ctGrowl.msg(data.message,'Success','bg-success');
							reloadExpenseGridView();
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

function expenseRequestPMReset() {
}

function expensePMReset(){
}

function addExpense(){
	var projectID = $('#expenseProjectFilterDD').val();
	var userID = $('#expenseEmployeeFilterDD').val();
	$('#addExpenseModal').modal('show').find('#modalContentSpan').html("Loading...");
	//Fetch modal content via pjax
	$.pjax.reload({
		type: 'GET',
		replace:false,
		url: '/expense/add?projectID='+projectID+'&userID='+userID,
		container: '#modalContentSpan', // id to update content
		timeout: 99999
	})
}

//reload table
//page is page to be reloaded to, if no value is sent will fetch current page
function reloadExpenseGridView(page = null) {
	//if no page is passed get the current active page
    if(page == null){
        // Get Current page and shift by one to 1-index instead of 0-index. Shifting will cause undefined value to become NaN
        currentPage = $("#EXPPagination ul li.active a").data('page') + 1;
        //if page is Not a Number becuase no pagination is present set to page 1
        page = isNaN(currentPage) ? 1 : currentPage;
    }
	var form = $('#expenseDropdownContainer').find("#ExpenseForm");
	//get sort value
	var ascSort = $("#GridViewForExpense-container").find(".asc").attr('data-sort');
	var descSort = $("#GridViewForExpense-container").find(".desc").attr('data-sort');
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
		container: '#expenseGridview', // id to update content
		data: dataParams,
		timeout: 99999
	});
	$('#expenseGridview').off('pjax:success').on('pjax:success', function () {
		$.pjax.reload({
			container: '#expenseButtons',
			timeout:false,
		}).done(function (){
				//reload dropdown values
				$.pjax.reload({container: '#expenseDropDownPjax', async:false});
			});
		$('#expenseButtons').off('pjax:success').on('pjax:success', function () {
			expenseApproveMultiple();
			expenseAccountantSubmit();
			expensePMReset();
			expenseRequestPMReset();
			$('#loading').hide();
		}).off('pjax:error').on('pjax:error', function () {
			location.reload();
		});
	}).off('pjax:error').on('pjax:error', function () {
		location.reload();
	});
}
