/**
 * Created by tzhang on 9/28/2017.
 */
$(function () {
    // global variable
    var currentSelectedDate = null;
        cgeSelectedMapGrid = "";
        cgeSelectedAssets = "";
        cgeSelectedScheduledDate = [];

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

    // Datepicker listener
    $(document).off('change', '.ScheduledDate').on('change', '.ScheduledDate', function () {
        currentSelectedDate = $(this).find("input[name=ScheduledDate]").val();
        $(this).closest('tr').find('td.cgeDispatchAssetsCheckbox input[type="checkbox"]').attr("scheduleddate", currentSelectedDate);
        //console.log("SCHEDULED DATE: "+$(this).closest('tr').find('td.cgeCheckBox input[type="checkbox"]').attr("scheduleddate"));
        if (currentSelectedDate == "" || currentSelectedDate.length == 0){
            $(this).closest('tr').find('td.cgeDispatchAssetsCheckbox input[type="checkbox"]').prop("disabled", true);
        }else{
            $(this).closest('tr').find('td.cgeDispatchAssetsCheckbox input[type="checkbox"]').prop("disabled", false);
        }
    });

    // checkbox listener at map grid level
    $(document).off('click', '.cgeDispatchCheckbox input[type=checkbox]').on('click', '.cgeDispatchCheckbox input[type=checkbox]', function () {
        cgeSelectedMapGrid = $('#cgeGV').yiiGridView('getSelectedRows');
        console.log("SELECTED MAP GRIDS: "+cgeSelectedMapGrid);
        if (cgeSelectedMapGrid.length > 0 || cgeSelectedAssets.length > 0)
            $("#cgeDispatchButton").prop('disabled', false);
        else
            $("#cgeDispatchButton").prop('disabled', true);
    });

    // checkbox listener at assets table level
    $(document).off('click', '.cgeDispatchAssetsCheckbox input[type=checkbox]').on('click', '.cgeDispatchAssetsCheckbox input[type=checkbox]', function () {
        cgeSelectedAssets = $("#cgeGridview #cgeAssetsGV").yiiGridView('getSelectedRows');
        console.log("SELECTED ASSETS: "+cgeSelectedAssets);
        if ($(this).is(':checked')){
            cgeSelectedScheduledDate.push($(this).attr("ScheduledDate"));
        }
        console.log("SELECTED cgeSelectedScheduledDate: "+cgeSelectedScheduledDate);
        if (cgeSelectedMapGrid.length > 0 || cgeSelectedAssets.length > 0)
            $("#cgeDispatchButton").prop('disabled', false);
        else
            $("#cgeDispatchButton").prop('disabled', true);
    });

    // Refer the modal in dispatch page
    $('#cgeDispatchButton').click(function () {
        console.log("cgeDispatchButton clicked!");
        $('#addSurveyorCgeModal').modal('show')
            .find('#modalAddSurveyorCge').html("Loading...");
        $('#addSurveyorCgeModal').modal('show')
            .find('#modalAddSurveyorCge')
            .load('/dispatch/add-surveyor-modal/add-surveyor-modal?modalName=cge');
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

function getCgeDispatchAssetsData(cgeSelectedAssets, AssignedUserID, ScheduledDate) {
    var cgeDispatchAssetsData = [];
    if (cgeSelectedAssets.length > 0) {
        for (var i = 0; i < cgeSelectedAssets.length; i++) {
            cgeDispatchAssetsData.push({
                WorkOrderID: cgeSelectedAssets[i],
                AssignedUserID: AssignedUserID,
                ScheduledDate: ScheduledDate[i]
            });
        }
    }
    return cgeDispatchAssetsData;
}

function getCgeDispatchMapGridData(cgeSelectedMapGrid, assignedUserID) {
    var cgeDispatchMapGridData = [];
    if (cgeSelectedMapGrid.length > 0){
        for (var i = 0; i < cgeSelectedMapGrid.length; i++){
            cgeDispatchMapGridData.push({
                MapGrid: cgeSelectedMapGrid[i],
                AssignedUserID: assignedUserID
            })
        }
        return cgeDispatchMapGridData;
    }else{
        return cgeDispatchMapGridData;
    }
}

function resetCge_Global_Variable() {
    cgeSelectedMapGrid = "";
    cgeSelectedAssets = "";
    cgeSelectedScheduledDate = [];
}
