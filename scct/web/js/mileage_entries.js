/**
 * Created by tzhang on 12/20/2017.
 */
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
	
	//apply on click listener on deactivate button to deactivate all checked rows
	$(document).on('click','#deactive_mileageEntry_btn_id',function(e){
		var name = "";
		var tasks = [];
		var entries = [];
		var mileageCardID = $('#mileageCardId').val();

	   $(".entryData").each(function(k,value){
			if($(this).is(":checked")){
				//get task name for payload and confirm message
				name = $(this).attr('taskName');
				tasks.push(name);
			}
		});
		entries.push({taskName : tasks, mileageCardID : mileageCardID});
		tasks.join(', ');
		data = {entries};

		krajeeDialog.defaults.confirm.title = 'Deactivate Mileage';
		krajeeDialog.confirm('Are you sure you want to deactivate all ' +tasks+ '? Please confirm...', function (resp) {
			if (resp) {
				$('#loading').show();
				$.ajax({
					type: 'POST',
					url: '/mileage-card/deactivate/',
					data: data,
					success: function(data) {
						$.pjax.reload({container:"#ShowMileageEntriesView", timeout: 99999}).done(function(){
							$('#loading').hide();
							$('#deactive_MileageEntry_btn_id').prop('disabled',true);
						});
					}
				});
			} else {
				$('#w0').modal('toggle');
				return false;
			}
		});
	});
	
	//apply listeners for deactivate check boxes to enable deactivate button
	$(document).on('change','.entryData', function (e) {
		input = $(this);
		tr = $(this).closest('tr');

		if($(this).is(":checked")){
			tr.find('td').each(function(index,value){
				if(index != 0 && $(this).text()!=""){
					th_class = $(this).closest('table').find('th').eq(index).attr('class');
				}
			});
			input.attr('entry',th_class);
		}
		$('#deactive_mileageEntry_btn_id').prop('disabled',
			!($("#allMileageEntries-container input:checkbox:checked").length > 0));
	});
	
	//apply listeners on cells with data for deactivation
	$(document).off('click', '#allMileageEntries tbody tr td').on('click', '#allMileageEntries tbody tr td',function (){
		id = $('#mileageCardId').val();
		seq_num = $(this).attr('data-col-seq');
		taskName = $(this).closest('tr').find("td[data-col-seq='0']").text();
		date = $("tr[data-key='0']").find("td[data-col-seq='"+seq_num+"']").text();
		var entries = [];
		//clean up date format for sending
		date = date.replace(/\-/g, '/');

		//restrict click to only day of the week fields
		//with values in the .text()
		if($(this).attr('data-col-seq') >=1 && ($(this).text()!="")
			&& (!$("#approve_mileageCard_btn_id").prop("disabled") || $('#isAccountant').val())
			&& !$('#isSubmitted').val()){
				krajeeDialog.defaults.confirm.title = 'Deactivate Time Entry';
				krajeeDialog.confirm('Are you sure you want to deactivate this time entry for '+date+'?', function (resp) {
					if (resp) {
						$('#loading').show();
						//build and send payload to deactivate single entry
						entries.push({taskName : [taskName], day : date, mileageCardID : id});
						data = {entries};
						$.ajax({
							type: 'POST',
							url: '/mileage-card/deactivate/',
							data: data,
							beforeSend: function() {
							},
							success: function(data) {
								$.pjax.reload({container:"#ShowMileageEntriesView", timeout: 99999}).done(function (){
									$('#loading').hide();
								});
							}
						});
					}
				});
				$('#loading').hide();
		}
	});
}

function validateMileageToolTip() {
    $.each($('#allMileageEntries tbody tr td'),function(){
        if($(this).attr('data-col-seq') >=1 && ($(this).text()!="") && ($(this).parent().attr('data-key')>0)
            && (!$("#approve_mileageCard_btn_id").prop("disabled"))) 
		{
            $(this).attr("title","Click to deactivate this mileage entry!")
        } 
		else if ($('#isAccountant').val() && !$('#isSubmitted').val() &&
			$(this).attr('data-col-seq') >=1 &&
			($(this).text()!="") &&
			($(this).parent().attr('data-key')>0))
		{
            $(this).attr("title","Click to deactivate this mileage entry!")
        }
    });
}

function validateMileageCheckEnabled() {
    $(".entryData").each(function(){
        if ($("#approve_mileageCard_btn_id").prop("disabled")
            && ($('#isSubmitted').val() || !$('#isAccountant').val())) {
            $(this).prop('disabled',true);
        }
    });
}