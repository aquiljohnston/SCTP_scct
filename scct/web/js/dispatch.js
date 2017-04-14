$('#dispatchButton').click(function () {
    var pks = $('#dispatchGV').yiiGridView('getSelectedRows');
    var pks_surveyors = $('#surveyorsGV').yiiGridView('getSelectedRows');
    $.ajax({
        type: 'POST',
        url: '/dispatch/dispatch/post',
        data: {InspectionRequestUID: pks, UserUID: pks_surveyors},
        beforeSend: function () {
            $('#loading').show();
        },
        complete: function () {
            window.location = window.location; // ?
            $('#dispatchActiveForm').submit().done(function () {
                $('#loading').hide();
            });
        }
    });
});
$(function () {
    $("#dispatchButton").prop('disabled', true);
});
$('.dispatch input[type=checkbox]').click(function () {
    // This Section for Dispatch page Dispatch Function
    var checkApproved = $(this).attr("approved");
    var pks = $('#dispatchGV').yiiGridView('getSelectedRows');
    var pks_surveyors = $('#surveyorsGV').yiiGridView('getSelectedRows');
    console.log("unassign: " + pks.length);
    console.log("surveyor: " + pks_surveyors.length);

    if ((!pks || pks.length != 0) && (!pks_surveyors || pks_surveyors.length != 0)) {
        $('#dispatchButton').prop('disabled', false); //TO ENABLE
    } else {
        $('#dispatchButton').prop('disabled', true);
    }
});

// BULK DELETE
/*$(function () {
    $('#dialog-dispatch').dialog({autoOpen: false, modal: true, show: "blind", hide: "blind"});
    $('.dispatch_btn').prop('disabled', true); //TO DISABLED
    $(document).off('click', '.DispatchSurveyors input[type=checkbox]').on('click', '.DispatchSurveyors input[type=checkbox]', function () {

        var checkApproved = $(this).attr("approved");
        var pks = $('#dispatchUnassignedGridview #dispatchGV').yiiGridView('getSelectedRows');
        var pks_surveyors = $('#dispatchSurveyorsGridview #surveyorsGV').yiiGridView('getSelectedRows');
        console.log("unassign: " + pks.length);
        console.log("surveyor: " + pks_surveyors.length);

        if ((!pks || pks.length != 0) && (!pks_surveyors || pks_surveyors.length != 0)) {
            $('.dispatch_btn').prop('disabled', false); //TO ENABLE
        } else {
            $('.dispatch_btn').prop('disabled', true);
        }
    });

    $(document).off('click', '.Dispatch input[type=checkbox]').on('click', '.Dispatch input[type=checkbox]', function () {
        var checkApproved = $(this).attr("approved");
        var pks = $('#dispatchUnassignedGridview #dispatchGV').yiiGridView('getSelectedRows');
        var pks_surveyors = $('#dispatchSurveyorsGridview #surveyor').yiiGridView('getSelectedRows');
        console.log("unassign: " + pks.length);
        console.log("surveyor: " + pks_surveyors.length);

        if ((!pks || pks.length != 0) && (!pks_surveyors || pks_surveyors.length != 0)) {
            $('.dispatch_btn').prop('disabled', false); //TO ENABLE
        } else {
            $('.dispatch_btn').prop('disabled', true);
        }
    });

    // triggered when checkbox selected
    $(document).off('click', '.dispatch_btn').on('click', '.dispatch_btn', function (e) {
        dispatchButtonListener();
        e.preventDefault();
    });
});*/

function dispatchButtonListener() {

    var pks = $('#dispatchUnassignedGridview #dispatchGV').yiiGridView('getSelectedRows');
    var pks_surveyors = $('#dispatchSurveyorsGridview #surveyor').yiiGridView('getSelectedRows');
    var form = $("#dispatchActiveForm");
    $('#loading').show();
    $('#UnassignedTableRecordsUpdate').val(true);
    $('#SurveyorTableRecordsUpdate').val(true);
    $.ajax({
        timeout: 99999,
        url: '/dispatch/dispatch/dispatch',
        data: {InspectionRequestUID: pks, UserUID: pks_surveyors},
        type: 'POST'
    }).done(function () {
        $.pjax.reload({
            container: '#dispatchUnassignedGridview',
            timeout: 99999,
            type: 'POST',
            url: form.attr("action"),
            data: form.serialize()
        });
        $('#dispatchUnassignedGridview').on('pjax:success', function () {
            $.pjax.reload({
                container: '#dispatchSurveyorsGridview',
                timeout: 99999,
                type: 'POST',
                url: form.attr("action"),
                data: form.serialize()
            });
            $('#dispatchSurveyorsGridview').on('pjax:success', function () {
                $('#UnassignedTableRecordsUpdate').val(false);
                $('#SurveyorTableRecordsUpdate').val(false);
                $('#loading').hide();
            });
            $('#dispatchSurveyorsGridview').on('pjax:error', function (e) {
                e.preventDefault();
            });
        });
        $('#dispatchUnassignedGridview').on('pjax:error', function (e) {
            e.preventDefault();
        });
    });

    // disable dispatch button again
    $('.dispatch_btn').prop('disabled', true); //TO DISABLED
}

