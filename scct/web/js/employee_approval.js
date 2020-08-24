$(function () {
    $(document).ready(function () {
        if ($('#employeeApprovalGridview').length > 0) {
			applyEmployeeApprovalListeners();
            employeeDetailToolTip();
			employeeApprovalApproveMultiple();
        }
		if($('#employeeApprovalDateRange').length > 0){
			//set to and from values for date picker based on current date range
			refreshDatePicker();
		}
    });
});

//apply listeners on report-summary/index
function applyEmployeeApprovalListeners() {
	// apply listeners on cells with data for employee detail redirect
	$(document).off('click', '#GridViewForEmployeeApprovalUser tbody tr td').on('click', '#GridViewForEmployeeApprovalUser tbody tr td',function (){
		//restrict click to only day of the week fields
		//with values in the .text()
		if($(this).attr('data-col-seq') > 0 && $(this).attr('data-col-seq') < 8 && ($(this).text()!= "-") 
			&& JSON.parse($(this).parent().attr('data-key')).UserID != null){
			//get data for redirect
			var userid = JSON.parse($(this).parent().attr('data-key')).UserID;
			//current column
			var col = $(this).attr('data-col-seq');
			var dateHeader = $(this).closest('table').find('th').eq(col)[0].innerHTML;//.innerHTML;
			var date = dateHeader.split(" ")[1];
			var currentURL = window.location;
			var baseUrl = currentURL .protocol + "//" + currentURL.host + "/" + currentURL.pathname.split('/')[1];
			var url = baseUrl + "/employee-detail?userID="+userid+"&date="+date;
			//reirect to employee detail screen
			window.location.href = url;
		}
	});
	
	$(document).off('change', "#employeeApprovalUserGV input[type=checkbox]").on('change', "#employeeApprovalUserGV input[type=checkbox]", function (e) {
		//enable button when items are selected depending on what is available
        if ($("#GridViewForEmployeeApprovalUser").yiiGridView('getSelectedRows') != 0) {
            $('#ea_multiple_approve_btn_id').prop('disabled', false); //TO ENABLE
            $('#ea_multiple_submit_btn_id').prop('disabled', false); //TO ENABLE
        } else {
            $('#ea_multiple_approve_btn_id').prop('disabled', true);
            $('#ea_multiple_submit_btn_id').prop('disabled', true);
        }
    });
	
	$(document).off('change', "#employeeApprovalDateRange").on('change', "#employeeApprovalDateRange", function (event) {
        event.preventDefault();
        var selected = $(this).find(":selected").val();
        if(selected == "other") {
			//reset date picker
			resetDatePicker();
			//show date picker
            $('#employeeApprovalDatePickerContainer').css("display", "block");
        }else {
			//hide date picker
            $('#employeeApprovalDatePickerContainer').css("display", "none");
            reloadEmployeeApprovalGridView();
        }
    });
	
	$(document).off('change', '#employeeApprovalProjectFilterDD').on('change', '#employeeApprovalProjectFilterDD', function (event) {
        reloadEmployeeApprovalGridView();
        event.preventDefault();
        return false;
    });
}

function employeeDetailToolTip() {
    $.each($('#GridViewForEmployeeApprovalUser tbody tr td'),function(){
        if($(this).attr('data-col-seq') > 0 && $(this).attr('data-col-seq') < 8 && ($(this).text()!= "-") 
			&& JSON.parse($(this).parent().attr('data-key')).UserID != null) {
				$(this).attr("title","Click to review this day.")
        } 
    });
}

function employeeApprovalApproveMultiple() {	
    $('#ea_multiple_approve_btn_id').off('click').click(function (event) {
        var primaryKeys = $('#GridViewForEmployeeApprovalUser').yiiGridView('getSelectedRows');
        var quantifier = "";

        if(primaryKeys.length <= 1 ) { // We don't expect 0 or negative but we need to handle it
            quantifier = "this item?";
        } else {
            quantifier = "these items?"
        }
		
		var dateRangeArray = $('#employeeApprovalDateRange').val().split(',');
		//if the range value is 'other'
		if(dateRangeArray.length == 1) {
			dateRangeArray = $('#dynamicmodel-daterangepicker-container').find('.kv-drp-dropdown').find('.range-value').html().split(" - ");
			var selectedDate = new Date(dateRangeArray[0]);
			selectedDate.setHours(selectedDate.getHours()+(selectedDate.getTimezoneOffset()/60));//to handle timezone offset on sat/sun
			var prevSunday = new Date(selectedDate.setDate(selectedDate.getDate()-selectedDate.getDay()));
			dateRangeArray[0] = prevSunday.getFullYear() + "-"+(prevSunday.getMonth()+1)+"-"+prevSunday.getDate(); // getMonth is 0 indexed
		}

        krajeeDialog.defaults.confirm.title = 'Approve';
        krajeeDialog.confirm('Are you sure you want to approve ' + quantifier, function (resp) {
        
        if (resp) {
			$('#loading').show();
            $.ajax({
                type: 'POST',
                url: '/employee-approval/approve-multiple',
                data: {
                    userid: primaryKeys
                },
				success: function(data){
					reloadEmployeeApprovalGridView();
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
function reloadEmployeeApprovalGridView() {
	var form = $('#employeeApprovalDropdownContainer').find('#EmployeeApprovalForm');
	//append sort to form values
	var dataParams = form.serialize();
	if (form.find(".has-error").length){
		return false;
	}
	$('#loading').show();
	$.pjax.reload({
		type: 'GET',
		url: form.attr("action"),
		container: '#employeeApprovalGridview', // id to update content
		data: dataParams,
		timeout: 99999
	}).done(function (){
		//reload dropdown values
		$.pjax.reload({container: '#employeeApprovalDropDownPjax', async:false});
	});
	$('#employeeApprovalGridview').off('pjax:success').on('pjax:success', function () {
		applyEmployeeApprovalListeners();
		employeeDetailToolTip();
		employeeApprovalApproveMultiple();
		$('#loading').hide();
		//TODO add button reloads if neccessary
	}).off('pjax:error').on('pjax:error', function () {
		location.reload();
	});
}