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
    $(document).off('click', '.dispatchCheckbox input[type=checkbox]').on('click', '.dispatchCheckbox input[type=checkbox]', function (){
        var pks = $("#dispatchUnassignedTable #dispatchGV").yiiGridView('getSelectedRows');
        console.log(pks);
        if (pks.length == 1){
            $("#dispatchButton").prop('disabled', false);
        }else
            $("#dispatchButton").prop('disabled', true);
    });
});

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

