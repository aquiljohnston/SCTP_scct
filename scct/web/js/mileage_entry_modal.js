$(function () {

    // Modal view for Sunday
    $('#mileageModalButtonSunday').click(function (event) {
        // get the click of the create button
        $('#mileageModalSunday').modal('show')
            .find('#modalContentMileageSunday')
            .load($(this).attr('value'));
        event.preventDefault();
        return false;
    });
    $('#mileageModalSunday').on('hidden.bs.modal', function (e) {
        // reload page when modal closed
        //location.reload(true);
    });

    // Modal view for Monday
    $('#mileageModalButtonMonday').click(function (event) {
        // get the click of the create button
        $('#mileageModalMonday').modal('show')
            .find('#modalContentMileageMonday')
            .load($(this).attr('value'));
        event.preventDefault();
        return false;
    });
    $('#mileageModalMonday').on('hidden.bs.modal', function (e) {
        // reload page when modal closed
        location.reload(true);
    });

    // Modal view for Tuesday
    $('#mileageModalButtonTuesday').click(function (event) {
        // get the click of the create button
        $('#mileageModalTuesday').modal('show')
            .find('#modalContentMileageTuesday')
            .load($(this).attr('value'));
        event.preventDefault();
        return false;
    });
    $('#mileageModalTuesday').on('hidden.bs.modal', function (e) {
        // reload page when modal closed
        //location.reload(true);
    });

    // Modal view for Wednesday
    $('#mileageModalButtonWednesday').click(function (event) {
        // get the click of the create button
        $('#mileageModalWednesday').modal('show')
            .find('#modalContentMileageWednesday')
            .load($(this).attr('value'));
        event.preventDefault();
        return false;
    });
    $('#mileageModalWednesday').on('hidden.bs.modal', function (e) {
        // reload page when modal closed
        //location.reload(true);
    });

    // Modal view for Thursday
    $('#mileageModalButtonThursday').click(function (event) {
        // get the click of the create button
        $('#mileageModalThursday').modal('show')
            .find('#modalContentMileageThursday')
            .load($(this).attr('value'));
        event.preventDefault();
        return false;
    });
    $('#mileageModalThursday').on('hidden.bs.modal', function (e) {
        // reload page when modal closed
        //location.reload(true);
    });

    // Modal view for Friday
    $('#mileageModalButtonFriday').click(function (event) {
        // get the click of the create button
        $('#mileageModalFriday').modal('show')
            .find('#modalContentMileageFriday')
            .load($(this).attr('value'));
        event.preventDefault();
        return false;
    });
    $('#mileageModalFriday').on('hidden.bs.modal', function (e) {
        // reload page when modal closed
        //location.reload(true);
    });

    // Modal view for Saturday
    $('#mileageModalButtonSaturday').click(function (event) {
        // get the click of the create button
        $('#mileageModalSaturday').modal('show')
            .find('#modalContentMileageSaturday')
            .load($(this).attr('value'));
        event.preventDefault();
        return false;
    });
    $('#mileageModalSaturday').on('hidden.bs.modal', function (e) {
        // reload page when modal closed
        //location.reload(true);
    });
});

function MileageEntryCreation() {
    var form = $('#MileageEntryForm');
    $('#loading').show();
    $.pjax.reload({
        container: '#MileageCardView',
        timeout: 99999,
        type: 'POST',
        url: form.attr("action"),
        data: form.serialize()
    });
    $('#MileageCardView').on('pjax:beforeSend', function () {
        console.log("Going to send pjax request");
    });
    $('#MileageCardView').on('pjax:complete', function () {
        console.log("pjax request complete");
    });
    $('#MileageCardView').on('pjax::success', function () {
        console.log("pjax request success");
        $('#loading').hide();
    });
}