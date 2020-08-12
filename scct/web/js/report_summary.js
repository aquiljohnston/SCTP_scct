$(function () {
    $(document).ready(function () {
        if ($('#reportSummaryGridview').length > 0) {
			console.log('apply listeners');
			applyReportSummaryListeners();
            validateTaskToolTip();
			reportSummaryApproveMultiple();
        }
		if($('#reportSummaryDateRange').length > 0){
			//set to and from values for date picker based on current date range
			refreshDatePicker();
		}
    });
});

//apply listeners on report-summary/index
function applyReportSummaryListeners() {
	// apply listeners on cells with data for employee detail redirect
	$(document).off('click', '#GridViewForReportSummaryUser tbody tr td').on('click', '#GridViewForReportSummaryUser tbody tr td',function (){
		//restrict click to only day of the week fields
		//with values in the .text()
		if($(this).attr('data-col-seq') > 0 && $(this).attr('data-col-seq') < 8 && ($(this).text()!= "-") 
			&& JSON.parse($(this).parent().attr('data-key')).UserID != null){
			//get data for redirect
			var userid = JSON.parse($(this).parent().attr('data-key')).UserID;
			console.log(userid);
			//current column
			var col = $(this).attr('data-col-seq');
			var startDate = $(this).closest('table').find('th').eq(col)[0].innerHTML;//.innerHTML;
			console.log(startDate);
			var currentURL = window.location;
			var baseUrl = currentURL .protocol + "//" + currentURL.host + "/" + currentURL.pathname.split('/')[1];
			var url = baseUrl + "/employee-detail?userID="+userid+"&startDate="+startDate;
			console.log(url);
			//reirect to employee detail screen
			window.location.href = url;
		}
	});
	
	$(document).off('change', "#reportSummaryUserGV input[type=checkbox]").on('change', "#reportSummaryUserGV input[type=checkbox]", function (e) {
		//enable button when items are selected depending on what is available
        if ($("#GridViewForReportSummaryUser").yiiGridView('getSelectedRows') != 0) {
            $('#rs_multiple_approve_btn_id').prop('disabled', false); //TO ENABLE
        } else {
            $('#rs_multiple_approve_btn_id').prop('disabled', true);
        }
    });
	
	$(document).off('change', "#reportSummaryDateRange").on('change', "#reportSummaryDateRange", function (event) {
		console.log('date change');
        event.preventDefault();
        var selected = $(this).find(":selected").val();
        if(selected == "other") {
			//reset date picker
			resetDatePicker();
			//show date picker
            $('#reportSummaryDatePickerContainer').css("display", "block");
        }else {
			//hide date picker
            $('#reportSummaryDatePickerContainer').css("display", "none");
            reloadReportSummaryGridView();
        }
    });
}

function validateTaskToolTip() {
    $.each($('#GridViewForReportSummaryUser tbody tr td'),function(){
        if($(this).attr('data-col-seq') > 0 && $(this).attr('data-col-seq') < 8 && ($(this).text()!= "-") 
			&& JSON.parse($(this).parent().attr('data-key')).UserID != null) {
				$(this).attr("title","Click to review this day.")
        } 
    });
}

function reportSummaryApproveMultiple() {	
    $('#rs_multiple_approve_btn_id').off('click').click(function (event) {
        var primaryKeys = $('#GridViewForReportSummaryUser').yiiGridView('getSelectedRows');
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
                url: '/report-summary/approve-multiple',
                data: {
                    userid: primaryKeys
                },
				success: function(data){
					//TODO reloadGridView();
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
function reloadReportSummaryGridView() {
	var form = $('#reportSummaryDropdownContainer').find('#ReportSummaryForm');
	//append sort to form values
	var dataParams = form.serialize();
	if (form.find(".has-error").length){
		return false;
	}
	$('#loading').show();
	$.pjax.reload({
		type: 'GET',
		url: form.attr("action"),
		container: '#reportSummaryGridview', // id to update content
		data: dataParams,
		timeout: 99999
	});
	$('#reportSummaryGridview').off('pjax:success').on('pjax:success', function () {
		applyReportSummaryListeners();
		validateTaskToolTip();
		reportSummaryApproveMultiple();
		$('#loading').hide();
		//TODO add button reloads if neccessary
	}).off('pjax:error').on('pjax:error', function () {
		location.reload();
	});
}