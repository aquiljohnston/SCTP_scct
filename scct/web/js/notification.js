/**
 * Created by tzhang on 6/27/2017.
 */
$(function () {
    // notification filter listener
    $(document).off('keypress', '#notificationFilter').on('keypress', '#notificationFilter', function (e) {
        if (e.keyCode === 13 || e.keyCode === 10) {
            e.preventDefault();
            reloadNotificationGridView();
        }
    });

    //page size listener
    $(document).off('change', '#notificationPageSize').on('change', '#notificationPageSize', function () {
        reloadNotificationGridView();
    });
});

function reloadNotificationGridView() {
    var jqNotificationDropDowns = $('#notificationTab');
    var form = jqNotificationDropDowns.find("#notificationActiveForm");
    if (form.find(".has-error").length) {
        return false;
    }
    $('#loading').show();
    $.pjax.reload({
        type: 'GET',
        url: form.attr("action"),
        container: '#notificationGridview', // id to update content
        data: form.serialize(),
        timeout: 99999
    }).done(function () {
        $('#loading').hide();
    });
}