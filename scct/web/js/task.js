/**
 * Created by tzhang on 12/20/2017.
 */
$(function () {
    $(document).off('keypress', '#taskSearchField').on('keypress', '#taskSearchField', function (e) {
        if (e.keyCode === 13 || e.keyCode === 10) {
            e.preventDefault();
            reloadTaskGridView();
        }
    });

    $(document).off('click', '#taskSearchCleanFilterButton').on('click', '#taskSearchCleanFilterButton', function () {
        $('#taskSearchField').val("");
        reloadTaskGridView();
    });

    $(document).off('keypress', '#userSearchField').on('keypress', '#userSearchField', function (e) {
        if (e.keyCode === 13 || e.keyCode === 10) {
            e.preventDefault();
            reloadUserGridView();
        }
    });

    $(document).off('click', '#userSearchCleanFilterButton').on('click', '#userSearchCleanFilterButton', function () {
        $('#userSearchField').val("");
        reloadUserGridView();
    });
});

function reloadTaskGridView() {
    var jqTaskContainer = $('#taskSearchContainer');
    var form = jqTaskContainer.find("#taskForm");
    if (form.find(".has-error").length) {
        return false;
    }
    $('#loading').show();
    $.pjax.reload({
        type: 'GET',
        url: form.attr("action"),
        container: '#taskGridView', // id to update content
        data: form.serialize(),
        timeout: 99999
    }).done(function () {
        $('#loading').hide();
    });
}

function reloadUserGridView() {
    var jqTaskContainer = $('#taskSearchContainer');
    var form = jqTaskContainer.find("#taskForm");
    if (form.find(".has-error").length) {
        return false;
    }
    $('#loading').show();
    $.pjax.reload({
        type: 'GET',
        url: form.attr("action"),
        container: '#userGridView', // id to update content
        data: form.serialize(),
        timeout: 99999
    }).done(function () {
        $('#loading').hide();
    });
}
