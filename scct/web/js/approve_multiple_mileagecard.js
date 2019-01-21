
$(function () {
	$('#multiple_approve_btn_id').prop('disabled', true); //TO DISABLED

    $(document).off('change', "#mileageCardGV input[type=checkbox]").on('change', "#mileageCardGV input[type=checkbox]", function (e) {
        if ($("#GridViewForMileageCard").yiiGridView('getSelectedRows') != 0) {
            $('#multiple_approve_btn_id').prop('disabled', false); //TO ENABLE
        } else {
            $('#multiple_approve_btn_id').prop('disabled', true);
        }
    });

    applyMileageCardOnClickListeners();
    applyMileageCardSubmitButtonListener();
});

function applyMileageCardOnClickListeners() {
	$(document).off('click', '#mileageCardClearDropdownFilterButton').on('click', '#mileageCardClearDropdownFilterButton', function (){
        $('#mileageProjectFilterDD').val("All");
        $('#mileageEmployeeFilterDD').val("All");
		$('#mileageCardPageNumber').val(1);
        reloadMileageCardGridView();
    });
	
    $('#multiple_approve_btn_id').off('click').click(function (event) {
        var primaryKeys = $('#GridViewForMileageCard').yiiGridView('getSelectedRows');
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
                url: '/mileage-card/approve-multiple',
                data: {
                    mileagecardid: primaryKeys
                }
            });
        } else {
            event.stopImmediatePropagation();
            event.preventDefault();
        }
      })
    });
}

function applyMileageCardSubmitButtonListener() {
    $('#multiple_submit_btn_id').off('click').click(function (event) {

        //apply css class that gives the tooltip gives
        //the appearance of being disabled via css
        //add returns false to prevent submission in
        //this state.
        if($(this).hasClass('off-btn')){
            return false;
        }

        var quantifier = "";
        var name = 'oasis_history_';
        var thIndex = $('th:contains("Project Name")').index();
        var projectIDs = [];
        
        $('#mileageProjectFilterDD option').each(function(){
            if($(this).val()!=""){
                projectIDs.push($(this).val());
            }
            
        })

        var d = new Date();
        var minutes = (d.getMinutes() < 10 ? "0" : "") + d.getMinutes();
        var hours = ((d.getHours() + 11) % 12 + 1);
        dates = $.datepicker.formatDate('yy-mm-dd', new Date());

        mileageCardName = name+dates+"_"+hours+"_"+minutes+"_"+d.getSeconds();
        dateRangeDD = $('[name="DynamicModel[dateRangeValue]"]').val();
		//check if date picker is active
		if(dateRangeDD == 'other')
		{
			dateRange = $('#dynamicmodel-daterangepicker-container').find('.kv-drp-dropdown').find('.range-value').html();
			dateRange = dateRange.split(" - ");
		}else{
			dateRange = dateRangeDD.split(",");
		}
        mileageCardComplete    = false;
        payrollComplete     = false;
        weekStart           = dateRange[0];
        weekEnd             = $.trim(dateRange[1]);


        //return false;
        var primaryKeys = $('#GridViewForMileageCard').yiiGridView('getSelectedRows');
        if(primaryKeys.length <= 1 ) { // We don't expect 0 or negative but we need to handle it
            quantifier = "this item?";
        } else {
            quantifier = "these items?"
        }

        krajeeDialog.defaults.confirm.title = 'Submit';
        krajeeDialog.confirm('Are you sure you want to submit ' + quantifier, function (resp) {
			if (resp) {
				$('#loading').show();
				
				var primaryKeys = $('#GridViewForMileageCard').yiiGridView('getSelectedRows');
				
				//usage $.ctGrow(msg,title,boostrap text class)
				$.ctGrowl.msg('Initiating the Submission.','Success','bg-success');

				payload = {
					mileageCardName : mileageCardName,
					//why is this projectname?
					projectName : projectIDs,
					weekStart : weekStart,
					weekEnd : weekEnd,
				}
					   
				$.ajax({
					type: 'POST',
					url: '/mileage-card/ajax-process-comet-tracker-files',
					data:payload,
					success: function(data) {
						//console.log(data)
						data = JSON.parse(data);
						if(data.success){
							$.ctGrowl.msg(data.message,'Success','bg-success');
							//calls time_card.js reload function
							reloadMileageCardGridView();
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




