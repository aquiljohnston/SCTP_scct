$(function () {
    $(document).ready(function () {
        if ($('#ShowMileageEntriesView').length > 0) {
			applyMilageEntryListeners();
            validateMileageCheckEnabled();
            validateMileageToolTip();
        }
    });
});

//apply listeners on mileage-card/show-entries
function applyMilageEntryListeners() {
	//apply click listener on approve button, to approve current card
	$('#approve_mileageCard_btn_id').click(function (e) {
		var mileageCardId = $('#mileageCardId').val();
		krajeeDialog.defaults.confirm.title = 'Approve';
		krajeeDialog.confirm('Are you sure you want to approve this?', function (resp) {
			if (resp) {
				$('#loading').show();
				$.ajax({
					type: 'POST',
					url: '/mileage-card/approve?id='+mileageCardId,
					success: function() {
						$.pjax.reload({container:"#ShowMileageEntriesView", timeout: 99999}).done(function(){ //for pjax update
							$('#approve_mileageCard_btn_id').prop('disabled', true);
							$('#add_mileage_btn_id').prop('disabled', true);
							validateMileageCheckEnabled();
							$('#loading').hide();
						});
					}
				});
			}
		})
	});
	
	//apply listeners on cells with data for view entry by day modal
	$(document).off('click', '#allMileageEntries tbody tr td').on('click', '#allMileageEntries tbody tr td',function (){
		//get row and column of this item
		column = $(this).attr('data-col-seq');
		row = $(this).closest('tr').attr('data-key');
		
		//get record values
		id = $('#mileageCardId').val();
		date = $("#allMileageEntries").find("tr[data-key='0']").find("td[data-col-seq='"+column+"']").text();
		task = $("#allMileageEntries").find("tr[data-key='"+row+"']").find("td[data-col-seq='0']").text();
		dateHeader = $('#allMileageEntries thead tr').find("th[data-col-seq='"+column+"']").text();
		//clean up date format for sending
		date = date.replace(/\-/g, '/');

		//restrict click to only day of the week fields
		//with values in the .text()
		if($(this).attr('data-col-seq') >=1){
			//set readOnly status based on role and approved/submitted state
			readOnly = ($("#approve_mileageCard_btn_id").prop("disabled") && !$('#isAccountant').val()) ? 1 : 0;
			$('#viewMileageModal').modal('show').find('#viewEntriesModalContentSpan').html("Loading...");
			//set header values
			$('#viewMileageModal').find('#viewMileageModalTitle').html(task);
			$('#viewMileageModal').find('#viewMileageModalDate').html(dateHeader);
			$.pjax.reload({
				type: 'GET',
				replace:false,
				url: '/mileage-task/view-mileage-entry-task-by-day?mileageCardID='+id+'&date='+date+'&readOnly='+readOnly,
				container: '#viewEntriesModalContentSpan',
				timeout: 99999
			});
		}
	});
}

//determines if deactivate tooltip should be displayed on table cells
function validateMileageToolTip() {
    $.each($('#allMileageEntries tbody tr td'),function(){
        if($(this).attr('data-col-seq') >=1 && ($(this).parent().attr('data-key')>0)
            && (!$("#approve_mileageCard_btn_id").prop("disabled"))) 
		{
            $(this).attr("title","Click to view details.")
        } 
		else if ($('#isAccountant').val() && !$('#isSubmitted').val() &&
			$(this).attr('data-col-seq') >=1 &&
			($(this).text()!="") &&
			($(this).parent().attr('data-key')>0))
		{
            $(this).attr("title","Click to view details.")
        }
    });
}

//checks if approved button should be disabled
function validateMileageCheckEnabled() {
    $(".entryData").each(function(){
        if ($("#approve_mileageCard_btn_id").prop("disabled")
            && ($('#isSubmitted').val() || !$('#isAccountant').val())) {
            $(this).prop('disabled',true);
        }
    });
}