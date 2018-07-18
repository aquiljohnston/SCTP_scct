/**
 * Created by tzhang on 9/28/2017.
 */
$(function () {
    // global variable
    var currentSelectedDate = null;

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
            //reset page number to 1
            $('#cgePageNumber').val(1);
            cgeGridViewReload();
        }
    });

    // Datepicker listener
    $(document).off('change', '.ScheduledDate').on('change', '.ScheduledDate', function () {
        currentSelectedDate = $(this).find("input[name=ScheduledDate]").val();
        $(this).closest('tr').find('td.cgeDispatchAssetsCheckbox input[type="checkbox"]').attr("scheduleddate", currentSelectedDate);
        scheduleRequired = $(this).closest('tr').find('td.cgeDispatchAssetsCheckbox input[type="checkbox"]').attr('ScheduleRequired');
        if ((currentSelectedDate == "" || currentSelectedDate.length == 0) && scheduleRequired == 1){
            $(this).closest('tr').find('td.cgeDispatchAssetsCheckbox input[type="checkbox"]').prop("disabled", true);
        }else{
            $(this).closest('tr').find('td.cgeDispatchAssetsCheckbox input[type="checkbox"]').prop("disabled", false);
        }
    });

    // checkbox listener at map grid level
    $(document).off('click', '.cgeDispatchCheckbox input[type=checkbox]').on('click', '.cgeDispatchCheckbox input[type=checkbox]', function () {
        cgeSelectedMapGrid = $('#cgeGV').yiiGridView('getSelectedRows');
        if (cgeSelectedMapGrid.length > 0)
            $("#cgeDispatchButton").prop('disabled', false);
        else
            $("#cgeDispatchButton").prop('disabled', true);
    });

    // checkbox listener at assets table level
    $(document).off('click', '.cgeDispatchAssetsCheckbox input[type=checkbox]').on('click', '.cgeDispatchAssetsCheckbox input[type=checkbox]', function () {
        cgeSelectedMapGrid = $('#cgeGV').yiiGridView('getSelectedRows');
		cgeSelectedAssets = $("#cgeGridview #cgeAssetsGV").yiiGridView('getSelectedRows');
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

    $(document).off('click', '#cgeSearchCleanFilterButton').on('click', '#cgeSearchCleanFilterButton', function (){
        $('#cgeFilter').val("");
        cgeGridViewReload();
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

function getCgeDispatchAssetsData(assignedUserIDs) {
    var cgeDispatchAssetsData = [];
	$('#cgeAssetsGV-container .cgeDispatchAssetsCheckbox input:checked').each(function() {
		cgeDispatchAssetsData.push({
			WorkOrderID: $(this).attr("WorkOrderID"),
			AssignedUserID: assignedUserIDs,
			ScheduledDate: $(this).attr("ScheduledDate"),
			SectionNumber: $(this).attr("SectionNumber"),
			IsCge: true
		});
    });
	console.log('AssetData '  + JSON.stringify(cgeDispatchAssetsData));
    return cgeDispatchAssetsData;
}

function getCgeDispatchMapGridData(assignedUserIDs) {
    var cgeDispatchMapGridData = [];	
	$('#cgeGV-container .cgeDispatchCheckbox input:checked').each(function() {
		cgeDispatchMapGridData.push({
			MapGrid: $(this).attr("MapGrid"),
			AssignedUserID: assignedUserIDs,
			BillingCode: $(this).attr("BillingCode"),
			InspectionType: $(this).attr("InspectionType"),
			IsCge: true
		});
	});
	console.log('MapGridData '  + JSON.stringify(cgeDispatchMapGridData));
	return cgeDispatchMapGridData;
}

function resetCge_Global_Variable() {
    cgeSelectedMapGrid = "";
    cgeSelectedAssets = "";
}
