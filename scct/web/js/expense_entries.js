$(function(){
	expenseShowEntriesApproveMultiple();
	
	$(document).off('change', "#ShowExpenseEntriesView input[type=checkbox]").on('change', "#ShowExpenseEntriesView input[type=checkbox]", function (e) {
        if ($("#allExpenseEntries").yiiGridView('getSelectedRows') != 0 && $('#isAccountant').val() != true) {
            $('#approve_expense_btn_id').prop('disabled', false);
        } else {
            $('#approve_expense_btn_id').prop('disabled', true);
        }
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
			reloadExpenseEntriesGridView();
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

//reload table
//page is page to be reloaded to, if no value is sent will fetch current page
function reloadExpenseEntriesGridView() {
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
		$.pjax.reload({
			container: '#expenseShowEntriesButtons',
			timeout:false,
		});
		$('#expenseShowEntriesButtons').off('pjax:success').on('pjax:success', function () {
			expenseShowEntriesApproveMultiple();
			$('#loading').hide();
		}).off('pjax:error').on('pjax:error', function () {
			location.reload();
		});
	}).off('pjax:error').on('pjax:error', function () {
		location.reload();
	});
}
