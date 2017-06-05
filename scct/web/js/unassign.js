// BULK DELETE
$(function () {

    $('#UnassignedButton').prop('disabled', false); //TO DISABLED
    /*$('#dialog-unassign').dialog({autoOpen: false, modal: true, show: "blind", hide: "blind"});
    $('#dialog-add-surveyor').dialog({autoOpen: false, modal: true, show: "blind", hide: "blind"});*/

    unassignCheckboxListener();
});

$(document).off('click', '#UnassignedButton').on('click', '#UnassignedButton', function () {
    $("#unassigned-message").css("display", "block");
    // When the user clicks buttons, close the modal
    $('#unassignedCancelBtn').click(function () {
        $("#unassigned-message").css("display", "none");
    });
    $('#unassignedConfirmBtn').click(function () {
        $("#unassigned-message").css("display", "none");
        unassignButtonListener();
    });
});

function unassignButtonListener() {
    var pks = $("#assignedGridview #assign").yiiGridView('getSelectedRows');
    var form = $("#AssignForm");
    $('#loading').show();
    $.ajax({
        url: '/dispatch/assigned/unassign',
        data: {MapGrid: pks, AssignedUserID: assignedUserIDArr},
        type: 'POST',
        beforeSend: function () {
            $('#loading').show();
        }
    }).done(function () {
        $.pjax.reload({
            container: '#assignedGridview',
            timeout: 99999,
            type: 'POST',
            url: form.attr("action"),
            data: form.serialize()
        });
        $('#assignedGridview').on('pjax:success', function () {
            $('#loading').hide();
            unassignCheckboxListener();
        });
        $('#assignedGridview').on('pjax:error', function (e) {
            e.preventDefault();
        });
    });

    // disable dispatch button again
    $('#UnassignedButton').prop('disabled', true); //TO DISABLED
}

function unassignCheckboxListener() {
    $(".unassignCheckbox input[type=checkbox]").click(function () {
        var pks = $("#assignedGridview #assign").yiiGridView('getSelectedRows');
        if (!pks || pks.length != 0) {
            $('#UnassignedButton').prop('disabled', false); //TO ENABLE
        } else {
            $('#UnassignedButton').prop('disabled', true);
        }
    });
}
	 