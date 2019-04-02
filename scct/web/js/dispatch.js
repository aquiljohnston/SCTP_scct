$(function () {
	$("#dispatchButton").prop('disabled', true);
	
	$( document ).ready(function() {
		//pass date picker selector to handle page refreshes
		refreshDispatchAssignedDatePicker('#dispatchDatePickerContainer #dynamicmodel-daterangepicker');
	}); 
	
	//date range listener
	$(document).off('change', '#dispatchDatePickerContainer #dynamicmodel-daterangepicker').on('change', '#dispatchDatePickerContainer #dynamicmodel-daterangepicker', function (){
		$('#dispatchPageNumber').val(1);
        reloadDispatchGridView();
	});
	
	//clear date range filter
	$(document).off('click', '#dispatchClearDateRange').on('click', '#dispatchClearDateRange', function (){
		//get datepicker
		var datePicker = $('#dispatchDatePickerContainer #dynamicmodel-daterangepicker').data('daterangepicker');
		//create default start end
		var fm = moment().startOf('day') || '';//today
		var to = moment() || '';//none
		//set default selections in widget
		datePicker.setStartDate(fm);
		datePicker.setEndDate(to);
		currentVals =  $('#dispatchDatePickerContainer #dynamicmodel-daterangepicker').data();
		dateValue = $('#dispatchDatePickerContainer #dynamicmodel-daterangepicker').val('');
        reloadDispatchGridView();
    });
	
    // dispatch filter listener
    $(document).off('keypress', '#dispatchFilter').on('keypress', '#dispatchFilter', function (e) {
        if (e.keyCode === 13 || e.keyCode === 10) {
            e.preventDefault();
            $('#dispatchPageNumber').val(1);
            reloadDispatchGridView();
        }
    });

    //page size listener
    $(document).off('change', '#dispatchPageSize').on('change', '#dispatchPageSize', function () {
		$('#dispatchPageNumber').val(1);
        reloadDispatchGridView();
    });

    // Refer the modal in dispatch page
    $('#dispatchButton').click(function () {
        $('#addSurveyorModal').modal('show')
            .find('#modalAddSurveyor').html("Loading...");
        $('#addSurveyorModal').modal('show')
            .find('#modalAddSurveyor')
            .load('/dispatch/add-surveyor-modal/add-surveyor-modal');
    });

	//gets map data
	//checkbox listener on table mapgrids
    $(document).off('click', '.dispatchCheckbox input[type=checkbox]').on('click', '.dispatchCheckbox input[type=checkbox]', function () {
		//if checked un-check section items
		if($(this).is(':checked')){
			$(".kv-expanded-row[data-key='"+$(this).val()+"'] #dispatchSectionGV-container>table>tbody>tr").removeClass('danger');
			$(".kv-expanded-row[data-key='"+$(this).val()+"']").find('input[type=checkbox]').prop('checked', false);
		}
        dispatchMap_MapGrid = $("#dispatchUnassignedTable #dispatchGV").yiiGridView('getSelectedRows');
        if (dispatchMap_MapGrid.length > 0) {
            $("#dispatchButton").prop('disabled', false);
        } else {
            $("#dispatchButton").prop('disabled', true);
		}
    });

	//get section data
    //checkbox listener on table sections
    $(document).off('click', '.dispatchSectionCheckbox input[type=checkbox]').on('click', '.dispatchSectionCheckbox input[type=checkbox]', function () {
		dispatchMap_MapGrid = $("#dispatchUnassignedTable #dispatchGV").yiiGridView('getSelectedRows');
        dispatchSection_SectionNumber =$("#dispatchUnassignedTable #dispatchSectionGV").yiiGridView('getSelectedRows');
        // check to see if need to disable/enable add surveyor button
        if (dispatchMap_MapGrid.length > 0 || dispatchSection_SectionNumber.length > 0){
            $("#dispatchButton").prop('disabled', false);
        }else{
            $("#dispatchButton").prop('disabled', true);
        }
    });

    //expandable row column listener
    $(document).off('kvexprow:toggle', "#dispatchUnassignedTable #dispatchGV").on('kvexprow:toggle', "#dispatchUnassignedTable #dispatchGV", function (event, ind, key, extra, state) {
        console.log('Toggled expand row');
		keyStr = JSON.stringify(key);
        var isCheckDisabled = $(this).find("[data-key='"+keyStr+"']").find('input[type=checkbox]').is(':disabled');
        if (isCheckDisabled){
            $(this).find("[data-key='"+keyStr+"']").find('.dispatchCheckbox input[type=checkbox]').prop('disabled', false);
        }else{
			$(this).find("[data-key='"+keyStr+"']").removeClass('danger');
            $(this).find("[data-key='"+keyStr+"']").find('.dispatchCheckbox input[type=checkbox]').prop('checked', false).prop('disabled', true);
            dispatchMap_MapGrid =$("#dispatchUnassignedTable #dispatchGV").yiiGridView('getSelectedRows');
        }
        //check to see if need to disable/enable add surveyor button
        if (dispatchMap_MapGrid.length > 0){
            $("#dispatchButton").prop('disabled', false);
        }else {
            $("#dispatchButton").prop('disabled', true);
        }
    });

    //pagination listener on dispatch page
    $(document).off('click', '#unassignedTablePagination .pagination li a').on('click', '#unassignedTablePagination .pagination li a', function (event) {
        event.preventDefault();
        var page = $(this).data('page') + 1; // Shift by one to 1-index instead of 0-index.
        $('#dispatchPageNumber').val(page);
        reloadDispatchGridView();
    });

    $(document).off('click', '#dispatchSearchCleanFilterButton').on('click', '#dispatchSearchCleanFilterButton', function (){
        $('#dispatchFilter').val("");
		$('#dispatchPageNumber').val(1);
        reloadDispatchGridView();
    });
	
	//don't think this should be necessary
	checkNavBarLoading();
});

function reloadDispatchGridView() {
    var form = $("#dispatchActiveForm");
    if (form.find(".has-error").length) {
        return false;
    }
	//get sort value
	var sort = getDispatchIndexSortParams();
	//append sort to form values
	var dataParams = form.serialize() + "&sort=" + sort;
    $('#loading').show();
    $.pjax.reload({
        type: 'GET',
        url: '/dispatch/dispatch/index',
        container: '#dispatchUnassignedGridview', // id to update content
        data: dataParams,
        timeout: 99999
    }).done(function () {
        $('#loading').hide();
    });
}

// Asset modal view
// Seems to be unused
function ViewAssetClicked(url) {
    console.log("View Asset clicked!");
    console.log(url);
    // get the click of the create button
    $('#assetModal').modal('show')
        .find('#viewAssetModalContent').load(url);
    $(".modal-backdrop.in").css({'opacity': 0});
}

// Generate Dispatch Map Array;
function getDispatchMapArray(assignedUserIDs) {
    var mapGridArray = [];
	$('#dispatchGV-container .dispatchCheckbox input:checked').each(function() {
		mapGridArray.push({
			MapGrid: $(this).attr('MapGrid'),
			AssignedUserID: assignedUserIDs,
			BillingCode: $(this).attr('BillingCode'),
			InspectionType: $(this).attr('InspectionType'),
			OfficeName: $(this).attr("OfficeName")
		});
	});
	return mapGridArray;
}

// Generate Dispatch Section Array;
function getDispatchSectionArray(assignedUserIDs) {
    var dispatchSectionArray = [];
	$('#dispatchSectionGV-container .dispatchSectionCheckbox input:checked').each(function() {
		dispatchSectionArray.push({
			MapGrid: $(this).attr("MapGrid"),
			SectionNumber: $(this).attr("SectionNumber"),
			AssignedUserID: assignedUserIDs,
			BillingCode: $(this).attr("BillingCode"),
			InspectionType: $(this).attr("InspectionType"),
			OfficeName: $(this).attr("OfficeName")
		});
	});
	return dispatchSectionArray;
}

// View Asset Modal (Dispatch, Assigned, CGE, Inspection)
function viewAssetRowClicked(url, modalViewAsset, modalContentViewAsset, mapGrid) {
    $(modalViewAsset).modal('show').find(modalContentViewAsset).html("Loading...");
    $(modalViewAsset).modal('show').find(modalContentViewAsset).load(url);
	if(document.getElementById('assetModalTitle') !=  null)
		document.getElementById('assetModalTitle').innerHTML = '<h4>' + mapGrid + ' - Assets</h4>';
}

//get sort params
function getDispatchIndexSortParams()
{
	var ascSort = $("#dispatchGV-container").find(".asc").attr('data-sort');
	var descSort = $("#dispatchGV-container").find(".desc").attr('data-sort');
	return (ascSort !== undefined) ? ascSort.replace('-', ''): '-' + descSort;
}

