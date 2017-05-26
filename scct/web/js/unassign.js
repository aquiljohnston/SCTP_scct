// BULK DELETE
$(function () {

    $('#UnassignedButton').prop('disabled', false); //TO DISABLED
    $('#dialog-unassign').dialog({autoOpen: false, modal: true, show: "blind", hide: "blind"});
    $('#dialog-add-surveyor').dialog({autoOpen: false, modal: true, show: "blind", hide: "blind"});

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
    $('#AssignedTableRecordsUpdate').val(true);
    var pks = $("#assignedGridview #assign").yiiGridView('getSelectedRows');
    var form = $("#AssignForm");
    $('#loading').show();
    $.ajax({
        url: '/dispatch/assigned/unassign',
        data: {AssignedWorkQueueUID: pks},
        type: 'POST',
        beforeSend: function () {
            $('#loading').show();
        }
    }).done(function () {
        var MapPlatArr = [];
        var IRUIDArr = [];
        $.pjax.reload({
            container: '#assignedGridview',
            timeout: 99999,
            type: 'POST',
            url: form.attr("action"),
            data: form.serialize()
        });
        $('#assignedGridview').on('pjax:success', function () {
            $('#AssignedTableRecordsUpdate').val(false);
            $('#loading').hide();
            unassignCheckboxListener();
            resetAddSurveyorButton(MapPlatArr, IRUIDArr);
        });
        $('#assignedGridview').on('pjax:error', function (e) {
            e.preventDefault();
        });
    });

    // disable dispatch button again
    $('#UnassignedButton').prop('disabled', true); //TO DISABLED
}

function unassignCheckboxListener() {
    $(".Unassign input[type=checkbox]").click(function () {
        var pks = $("#assignedGridview #assign").yiiGridView('getSelectedRows');
        if (!pks || pks.length != 0) {
            $('#UnassignedButton').prop('disabled', false); //TO ENABLE
        } else {
            $('#UnassignedButton').prop('disabled', true);
        }
    });
}
	 