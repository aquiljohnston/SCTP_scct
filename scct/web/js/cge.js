/**
 * Created by tzhang on 9/28/2017.
 */
$(function () {
    //pagination listener on CGE page
    $(document).off('click', '#cgeTablePagination .pagination li a').on('click', '#cgeTablePagination .pagination li a', function (event) {
        event.preventDefault();
        var page = $(this).data('page') + 1; // Shift by one to 1-index instead of 0-index.
        $('#cgePageNumber').val(page);
        cgeGridViewReload();
    });

    //page size listener
    $(document).off('change', '#cgePageSize').on('change', '#cgePageSize', function () {
        $('#cgeTableRecordsUpdate').val(true);
        cgeGridViewReload();
    });

    // cge filter listener
    $(document).off('keypress', '#cgeFilter').on('keypress', '#cgeFilter', function (e) {
        if (e.keyCode === 13 || e.keyCode === 10) {
            e.preventDefault();
            cgeGridViewReload();
        }
    });
});

function cgeGridViewReload() {
    var form = $("#cgeActiveForm");
    $('#loading').show();
    $.pjax.reload({
        container: "#cgeGridview",
        timeout: 99999,
        url: form.attr("action"),
        type: "GET",
        data: form.serialize()
    }).done(function () {
    });
    $('#cgeGridview').on('pjax:success', function (event, data, status, xhr, options) {
        $('#loading').hide();
    });
    $('#cgeGridview').on('pjax:error', function (event, data, status, xhr, options) {
        console.log("Error");
    });
}
