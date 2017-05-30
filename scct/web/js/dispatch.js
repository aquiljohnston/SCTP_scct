// Refer the modal in dispatch page
$('#dispatchButton').click(function () {
    /*var pks = $('#dispatchGV').yiiGridView('getSelectedRows');
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
    });*/
    var MapPlatArr = [];
    var IRUIDArr = [];
    console.log("clicked");
    $('#addSurveyorModal').modal('show')
        .find('#modalAddSurveyor')
        .load('/dispatch/add-surveyor-modal/add-surveyor-modal', {
            "mapplat[]": [MapPlatArr],
            "IRUID[]": [IRUIDArr]
        });
});

// Refer the modal in assigned page
$('#addSurveyor').click(function () {
    /*var pks = $('#dispatchGV').yiiGridView('getSelectedRows');
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
     });*/
    var MapPlatArr = [];
    var IRUIDArr = [];
    console.log("clicked");
    $('#addSurveyorModal').modal('show')
        .find('#modalAddSurveyor')
        .load('/dispatch/add-surveyor-modal/add-surveyor-modal', {
            "mapplat[]": [MapPlatArr],
            "IRUID[]": [IRUIDArr]
        });
});

$(function () {
    //$("#dispatchButton").prop('disabled', true);
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

