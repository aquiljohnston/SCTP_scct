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
		if(jqWeekSelection.length > 0)
		{
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
        $('#expenseProjectFilterDD').val("All");
        $('#expenseEmployeeFilterDD').val("All");
        reloadExpenseGridView(1);
    });

    $(document).off('click', '#expenseSearchCleanFilterButton').on('click', '#expenseSearchCleanFilterButton', function (){
        $('#expenseCardFilter').val("");
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
});

function expenseApproveMultiple() {	
}

function expenseAccountantSubmit() {
}

function expenseRequestPMReset() {
}

function expensePMReset(){
}

function expenseGetSelectedProjectID(){
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
