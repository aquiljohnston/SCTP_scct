$(function () {
    $('#tc_multiple_approve_btn_id').prop('disabled', true); //TO DISABLED

    $(document).off('change', "#timeCardGV input[type=checkbox]").on('change', "#timeCardGV input[type=checkbox]", function (e) {
        if ($("#GridViewForTimeCard").yiiGridView('getSelectedRows') != 0) {
            $('#tc_multiple_approve_btn_id').prop('disabled', false); //TO ENABLE
        } else {
            $('#tc_multiple_approve_btn_id').prop('disabled', true);
        }
    });

    applyTimeCardOnClickListeners();

    $.ctGrowl.init( { position: 'absolute', bottom: '70px', left: '8px' });
});

function applyTimeCardOnClickListeners() {	
    $('#tc_multiple_approve_btn_id').off('click').click(function (event) {
        var primaryKeys = $('#GridViewForTimeCard').yiiGridView('getSelectedRows');
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
                url: '/time-card/approve-multiple',
                data: {
                    timecardid: primaryKeys
                }
            });
        } else {
            event.stopImmediatePropagation();
            event.preventDefault();
        }
      })
    });
}