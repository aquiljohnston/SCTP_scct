// BULK DELETE
$(function() {

    //$("#equipmentPagination a").click(applyEquipmentCheckboxesOnClickListeners); // Send function as argument

    $('#equipmentGridview').on('pjax:success', function(event, data, status, xhr, options) {
        applyEquipmentCheckboxesOnClickListeners();
    });

	applyEquipmentApproveOnClickListeners();
	applyEquipmentCheckboxesOnClickListeners()
 });

function applyEquipmentCheckboxesOnClickListeners() { var counter = 0;
    var flag = "Yes";
    var totalworkhours = -1;
    $('#multiple_approve_btn_id_equipment').prop('disabled', true); //TO DISABLED
    $('.select-on-check-all').change(function () {
        var checkApproved = $(this).attr("accepted");

        //alert("checkApproved is :　"+checkApproved);
        var model = $(this).attr('model');
        var pks = $('#w0').yiiGridView('getSelectedRows');
        //alert("pks length is ：　"+pks);
        for (var i = 0; i < pks.length; i++) {
            // get approved value for this timecard
            flag = $(".kv-row-select input[equipmentid=" + pks[i] + "]").attr("accepted");

            //alert("loop flag is "+flag+" i is : "+i);
            if (flag == "Yes" || flag == "yes") {
                flag = "Yes";
                break;
            } else {
                continue;
            }
        }

        if (!pks || pks.length != 0 && flag != "Yes") {
            $('#multiple_approve_btn_id_equipment').prop('disabled', false); //TO ENABLE
        } else {
            $('#multiple_approve_btn_id_equipment').prop('disabled', true);
        }
    });

    $("#GridViewForEquipment .kv-row-select input[type=checkbox]").click(function () {
        var checkApproved = $(this).attr("accepted");

        //alert("checkApproved is :　"+checkApproved);
        var model = $(this).attr('model');
        var pks = $('#GridViewForEquipment').yiiGridView('getSelectedRows');
        //alert("pks length is ：　"+pks);
        for (var i = 0; i < pks.length; i++) {
            // get approved value for this timecard
            flag = $(".kv-row-select input[equipmentid=" + pks[i] + "]").attr("accepted");

            //alert("loop flag is "+flag+" i is : "+i);
            if (flag == "Yes" || flag == "yes") {
                flag = "Yes";
                break;
            } else {
                continue;
            }
        }

        if (!pks || pks.length != 0 && flag != "Yes") {
            $('#multiple_approve_btn_id_equipment').prop('disabled', false); //TO ENABLE
        } else {
            $('#multiple_approve_btn_id_equipment').prop('disabled', true);
        }
    });


}
function applyEquipmentApproveOnClickListeners() {
    $('#multiple_approve_btn_id_equipment').click(function () {
        var primaryKeys = $('#GridViewForEquipment').yiiGridView('getSelectedRows');
        var quantifier = "";
        if(primaryKeys.length <= 1 ) { // We don't expect 0 or negative but we need to handle it
            quantifier = "this item?";
        } else {
            quantifier = "these items?"
        }
        var confirmBox = confirm('Are you sure you want to approve ' + quantifier);
        if (confirmBox) {
            $.ajax({
                type: 'POST',
                url: '/equipment/approve-multiple-equipment',
                data: {equipmentid: primaryKeys},
                success: function (data) {
                    $.pjax.reload({container: '#w0'});
                }
            });
        } else {
            e.stopImmediatePropagation();
            e.preventDefault();
        }
    });
}

	 