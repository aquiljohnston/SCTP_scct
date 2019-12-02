$(function(){
	expenseShowEntriesApproveMultiple();
	expenseShowEntriesDeactivate();
	
	$(document).off('change', "#ShowExpenseEntriesView input[type=checkbox]").on('change', "#ShowExpenseEntriesView input[type=checkbox]", function (e) {
        if ($("#allExpenseEntries").yiiGridView('getSelectedRows') != 0) {
            $('#approve_expense_btn_id').prop('disabled', false);
            $('#exp_entries_deactivate_btn_id').prop('disabled', false);
        } else {
            $('#approve_expense_btn_id').prop('disabled', true);
            $('#exp_entries_deactivate_btn_id').prop('disabled', true);
        }
    });
	
	//listener on show entries add expense button to launch modal and pass data to it
	$(document).off('click', '#exp_entries_add_btn_id').on('click', '#exp_entries_add_btn_id', function (){
		entriesAddExpense();
    });
});

function expenseShowEntriesApproveMultiple() {	
	$('#approve_expense_btn_id').off('click').click(function (event) {
        var primaryKeys = $('#allExpenseEntries').yiiGridView('getSelectedRows');
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
					reloadExpenseEntriesGridView();
				}
            });
        } else {
            event.stopImmediatePropagation();
            event.preventDefault();
        }
      })
    });
}

function expenseShowEntriesDeactivate() {	
	$('#exp_entries_deactivate_btn_id').off('click').click(function (event) {
        var primaryKeys = $('#allExpenseEntries').yiiGridView('getSelectedRows');
        var quantifier = "";

        if(primaryKeys.length <= 1 ) { // We don't expect 0 or negative but we need to handle it
            quantifier = "this item?";
        } else {
            quantifier = "these items?"
        }

        krajeeDialog.defaults.confirm.title = 'Deactivate';
        krajeeDialog.confirm('Are you sure you want to deactivate ' + quantifier, function (resp) {
        
        if (resp) {
			$('#loading').show();
            $.ajax({
                type: 'POST',
                url: '/expense/deactivate',
                data: {
                    id: primaryKeys
                },
				success: function(data){
					reloadExpenseEntriesGridView();
				}
            });
        } else {
            event.stopImmediatePropagation();
            event.preventDefault();
        }
      })
    });
}

function entriesAddExpense(){
	var projectID = $('#projectID').val();
	var userID = $('#userID').val();
	$('#addExpenseModal').modal('show').find('#modalContentSpan').html("Loading...");
	//Fetch modal content via pjax
	$.pjax.reload({
		type: 'GET',
		replace:false,
		url: '/expense/add?projectID='+projectID+'&userID='+userID+'&isEntries=true',
		container: '#modalContentSpan', // id to update content
		timeout: 99999
	})
}

//reload table
//page is page to be reloaded to, if no value is sent will fetch current page
function reloadExpenseEntriesGridView(){
	var form = $('#ExpenseEntriesFormContainer').find("#ExpenseEntriesForm");
	//append sort to form values
	var dataParams = "&userID=" + $('#userID').val() +
	"&projectID=" + $('#projectID').val() +
	"&startDate=" + $('#startDate').val() +
	"&endDate=" + $('#endDate').val();
	if (form.find(".has-error").length){
		return false;
	}
	$('#loading').show();
	$.pjax.reload({
		type: 'GET',
		url: '/expense/show-entries',
		container: '#ShowExpenseEntriesView', // id to update content
		data: dataParams,
		timeout: 99999
	});
	$('#ShowExpenseEntriesView').off('pjax:success').on('pjax:success', function () {
		$('#approve_expense_btn_id').prop('disabled', true);
		$('#exp_entries_deactivate_btn_id').prop('disabled', true);
		expenseShowEntriesApproveMultiple();
		expenseShowEntriesDeactivate();
		$('#loading').hide();
	}).off('pjax:error').on('pjax:error', function () {
		location.reload();
	});
}
