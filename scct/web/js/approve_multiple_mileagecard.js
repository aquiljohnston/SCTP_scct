$(function () {
	$('#mc_multiple_approve_btn_id').prop('disabled', true); //TO DISABLED

    $(document).off('change', "#mileageCardGV input[type=checkbox]").on('change', "#mileageCardGV input[type=checkbox]", function (e) {
        if ($("#GridViewForMileageCard").yiiGridView('getSelectedRows') != 0) {
            $('#mc_multiple_approve_btn_id').prop('disabled', false); //TO ENABLE
        } else {
            $('#mc_multiple_approve_btn_id').prop('disabled', true);
        }
    });

    applyMileageCardOnClickListeners();
	
	$.ctGrowl.init( { position: 'absolute', bottom: '70px', left: '8px' });
});

function applyMileageCardOnClickListeners() {	
    $('#mc_multiple_approve_btn_id').off('click').click(function (event) {
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