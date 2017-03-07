$('#dispatchButton').click(function(){
    var pks = $('#dispatchGV').yiiGridView('getSelectedRows');
    var pks_surveyors = $('#surveyorsGV').yiiGridView('getSelectedRows');
    $.ajax({
        type: 'POST',
        url: '/dispatch/dispatch/post',
        data: {InspectionRequestUID: pks, UserUID: pks_surveyors },
        beforeSend: function () {
            $('#loading').show();
        },
        complete: function () {
            window.location=window.location; // ?
            $('#dispatchActiveForm').submit().done(function () {
                $('#loading').hide();
            });
        }
    });
});
$(function() {
   $("#dispatchButton").prop('disabled', true);
});
$('.dispatch input[type=checkbox]').click(function(){
    // This Section for Dispatch page Dispatch Function
    var checkApproved = $(this).attr("approved");
    var pks = $('#dispatchGV').yiiGridView('getSelectedRows');
    var pks_surveyors = $('#surveyorsGV').yiiGridView('getSelectedRows');
    console.log("unassign: "+ pks.length);
    console.log("surveyor: "+ pks_surveyors.length);

    if ((!pks || pks.length != 0) && (!pks_surveyors || pks_surveyors.length != 0)){
        $('#dispatchButton').prop('disabled', false); //TO ENABLE
    }else {
        $('#dispatchButton').prop('disabled', true);
    }
});