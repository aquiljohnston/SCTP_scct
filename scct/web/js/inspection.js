/**
 * Created by tzhang on 5/31/2017.
 */
$(function () {

    var inspectionGV = $("#inspectionGV");

    //inspection Search filter listener
    $(document).off('keypress', '#inspectionFilter').on('keypress', '#inspectionFilter', function (e) {
        if (e.keyCode === 13 || e.keyCode === 10) {
            e.preventDefault();
            reloadInspectionGridView();
        }
    });
    $(document).off('change', '#inspectionPageSize').on('change', '#inspectionPageSize', function () {
        $('#inspectionTableRecordsUpdate').val(true);
        reloadInspectionGridView();
    });

    //pagination listener on assigned page
    $(document).off('click', '#InspectionTablePagination .pagination li a').on('click', '#InspectionTablePagination .pagination li a', function (event) {
        event.preventDefault();
        var page = $(this).data('page') + 1; // Shift by one to 1-index instead of 0-index.

        $('#inspectionPageNumber').val(page);
        var form = $("#inspectionActiveForm");
        $('#loading').show();
        $.pjax.reload({
            container: "#inspectionGridview",
            timeout: 99999,
            url: form.attr("action"),
            type: "GET",
            data: form.serialize()
        }).done(function () {
        });
        $('#inspectionGridview').on('pjax:success', function (event, data, status, xhr, options) {
            console.log("Success");
            $('#loading').hide();
        });
        $('#inspectionGridview').on('pjax:error', function (event, data, status, xhr, options) {
            console.log("Error");
            //window.location.reload(); // Can't leave them stuck
        });
    });

    inspectionGV.on('kvexprow:toggle', function (event, ind, key, extra, state) {
    //inspectionGV.on('kvexprow.toggle.kvExpandRowColumn', function (event, ind, key, extra, state) {
        if (state){
            inspectionGV.css({"overflow-y": "auto", "max-height": "51vh"});
        }else{
            inspectionGV.css('overflow-y', 'auto');
            inspectionGV.css('overflow-x', 'hidden');
        }
    });
});


function reloadInspectionGridView() {
    var jqInspectionDropDowns = $('#inspection-dropDownList-form');
    var form = jqInspectionDropDowns.find("#inspectionActiveForm");
    if (form.find(".has-error").length) {
        return false;
    }
    $('#loading').show();
    $.pjax.reload({
        type: 'GET',
        url: form.attr("action"),
        container: '#inspectionGridview', // id to update content
        data: form.serialize(),
        timeout: 99999
    }).done(function () {
        $('#loading').hide();
        $('#assignedTableRecordsUpdate').val(false);
    });
}