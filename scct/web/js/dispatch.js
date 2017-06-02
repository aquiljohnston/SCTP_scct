$(function () {
    $("#dispatchButton").prop('disabled', true);

    // dispatch filter listener
    $(document).off('keypress', '#dispatchFilter').on('keypress', '#dispatchFilter', function (e) {
        if (e.keyCode === 13 || e.keyCode === 10) {
            e.preventDefault();
            reloadDispatchGridView();
        }
    });

    //page size listener
    $(document).off('change', '#dispatchPageSize').on('change', '#dispatchPageSize', function () {
        reloadDispatchGridView();
    });

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
        /*console.log($("#dispatchUnassignedGridview #dispatchGV").yiiGridView('getSelectedRows'));*/
        MapGrid = $("#dispatchUnassignedGridview #dispatchGV").yiiGridView('getSelectedRows');
        SectionNumber = $("#dispatchUnassignedGridview #dispatchGV input[MapGrid=" + MapGrid + "]").attr("SectionNumber");
        //console.log("get section number: "+SectionNumber);
        $('#addSurveyorModal').modal('show')
            .find('#modalAddSurveyor')
            .load('/dispatch/add-surveyor-modal/add-surveyor-modal', {
                "mapplat[]": [MapPlatArr],
                "IRUID[]": [IRUIDArr]
            });
    });

    // Refer the modal in assigned page
    /*$('#addSurveyor').click(function () {
        /!*var pks = $('#dispatchGV').yiiGridView('getSelectedRows');
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
         });*!/
        var MapPlatArr = [];
        var IRUIDArr = [];
        console.log("clicked");
        $('#addSurveyorModal').modal('show')
            .find('#modalAddSurveyor')
            .load('/dispatch/add-surveyor-modal/add-surveyor-modal', {
                "mapplat[]": [MapPlatArr],
                "IRUID[]": [IRUIDArr]
            });
    });*/

    // set constrains: user can only dispatch one map to one surveyor at a time
    $('.dispatchCheckbox input[type=checkbox]').click(function () {
        var pks = $("#dispatchUnassignedTable #dispatchGV").yiiGridView('getSelectedRows');
        //console.log(pks);
        if (pks.length == 1){
            $("#dispatchButton").prop('disabled', false);
        }else
            $("#dispatchButton").prop('disabled', true);
    });
});

function dispatchButtonListener() {

    var pks_dispatch = $('#dispatchUnassignedGridview #dispatchGV').yiiGridView('getSelectedRows');
    var pks_surveyors = $('#dispatchSurveyorsGridview #surveyor').yiiGridView('getSelectedRows');
    var sectionNumber = $(".kv-row-select input[SectionNumber=" + pks_dispatch + "]").attr("SectionNumber");
    var form = $("#dispatchActiveForm");

    $('#loading').show();
    $('#UnassignedTableRecordsUpdate').val(true);
    $('#SurveyorTableRecordsUpdate').val(true);
    $.ajax({
        timeout: 99999,
        url: '/dispatch/dispatch/dispatch',
        data: {MapGrid: pks_dispatch, AssignedUserID: pks_surveyors, SectionNumber: sectionNumber},
        type: 'POST'
    }).done(function () {
        $.pjax.reload({
            container: '#dispatchUnassignedGridview',
            timeout: 99999,
            type: 'POST',
            url: form.attr("action"),
            data: form.serialize()
        });
        $('#loading').hide();
        $('#dispatchUnassignedGridview').on('pjax:success', function () {
            /*$.pjax.reload({
                container: '#dispatchSurveyorsGridview',
                timeout: 99999,
                type: 'POST',
                url: form.attr("action"),
                data: form.serialize()
            });
            $('#dispatchSurveyorsGridview').on('pjax:success', function () {
                $('#loading').hide();
            });
            $('#dispatchSurveyorsGridview').on('pjax:error', function (e) {
                e.preventDefault();
            });*/
        });
        $('#dispatchUnassignedGridview').on('pjax:error', function (e) {
            e.preventDefault();
        });
    });

    // disable dispatch button again
    $('.dispatch_btn').prop('disabled', true); //TO DISABLED
}

function reloadDispatchGridView() {
    var jqDispatchDropDowns = $('#dispatchTab');
    var form = jqDispatchDropDowns.find("#dispatchActiveForm");
    if (form.find(".has-error").length) {
        return false;
    }
    $('#loading').show();
    $.pjax.reload({
        type: 'GET',
        url: form.attr("action"),
        container: '#dispatchUnassignedGridview', // id to update content
        data: form.serialize(),
        timeout: 99999
    }).done(function () {
        $('#loading').hide();
    });
}

