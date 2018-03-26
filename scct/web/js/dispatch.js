function initializeDispatch() {
    var dispatchGV = $("#dispatchGV");

    $("#dispatchButton").prop('disabled', true);

    // dispatch filter listener
    $(document).off('keypress', '#dispatchFilter').on('keypress', '#dispatchFilter', function (e) {
        if (e.keyCode === 13 || e.keyCode === 10) {
            e.preventDefault();
            //reset page number to 1
            $('#dispatchPageNumber').val(1);
            reloadDispatchGridView();
        }
    });

    //page size listener
    $(document).off('change', '#dispatchPageSize').on('change', '#dispatchPageSize', function () {
        $('#dispatchTableRecordsUpdate').val(true);
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
    // set constrains: user can only dispatch one map to one surveyor at a time
    $(document).off('click', '.dispatchCheckbox input[type=checkbox]').on('click', '.dispatchCheckbox input[type=checkbox]', function () {
        dispatchMap_MapGrid = $("#dispatchUnassignedTable #dispatchGV").yiiGridView('getSelectedRows');
        if (dispatchMap_MapGrid.length > 0) {
            $("#dispatchButton").prop('disabled', false);
        } else
            $("#dispatchButton").prop('disabled', true);
    });

	//get section data
    //checkbox listener on section table
    $(document).off('click', '.dispatchSectionCheckbox input[type=checkbox]').on('click', '.dispatchSectionCheckbox input[type=checkbox]', function () {
		dispatchMap_MapGrid = $("#dispatchUnassignedTable #dispatchGV").yiiGridView('getSelectedRows');
        dispatchSection_SectionNumber =$("#dispatchUnassignedTable #dispatchSectionGV").yiiGridView('getSelectedRows');
        // check to see if need to disable/enable add surveyor button
        if (dispatchMap_MapGrid.length > 0 || dispatchSection_SectionNumber.length > 0){
            $("#dispatchButton").prop('disabled', false);
        }else{
            $("#dispatchButton").prop('disabled', true);
        }
        console.log(dispatchSection_SectionNumber);
    });

    //before expand section table, deselect all checkboxes from map table
    $(document).off('kvexprow:toggle', "#dispatchUnassignedTable #dispatchGV").on('kvexprow:toggle', "#dispatchUnassignedTable #dispatchGV", function (event, ind, key, extra, state) {
    //dispatchGV.on('kvexprow.beforeLoad.kvExpandRowColumn', function (event, ind, key, extra, state) {
        console.log('before expand row');
    });

    //expandable row column listener
    $(document).off('kvexprow:toggle', "#dispatchUnassignedTable #dispatchGV").on('kvexprow:toggle', "#dispatchUnassignedTable #dispatchGV", function (event, ind, key, extra, state) {
    //dispatchGV.on('kvexprow.toggle.kvExpandRowColumn', function (event, ind, key, extra, state) {
        console.log('Toggled expand row');
        var isCheckDisabled = $(this).find("[data-key='"+key+"']").find('input[type=checkbox]').is(':disabled');
        if (isCheckDisabled){
            $(this).find("[data-key='"+key+"']").find('.dispatchCheckbox input[type=checkbox]').prop('disabled', false);
        }else{
            $(this).find("[data-key='"+key+"']").find('.dispatchCheckbox input[type=checkbox]').prop('checked', false).prop('disabled', true);
            dispatchMap_MapGrid =$("#dispatchUnassignedTable #dispatchGV").yiiGridView('getSelectedRows');
        }
        console.log("dispatchMap_MapGrid: " +dispatchMap_MapGrid.length);

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
        var form = $("#dispatchActiveForm");
        $('#loading').show();
        $.pjax.reload({
            container: "#dispatchUnassignedGridview",
            timeout: 99999,
            url: form.attr("action"),
            type: "GET",
            data: form.serialize()
        }).done(function () {
        });
        $('#dispatchUnassignedGridview').on('pjax:success', function (event, data, status, xhr, options) {
            $('#loading').hide();
        });
        $('#dispatchUnassignedGridview').on('pjax:error', function (event, data, status, xhr, options) {
            //window.location.reload();
            console.log("Error");
        });
    });

    $('#loading').hide();

    $(document).off('click', '#dispatchSearchCleanFilterButton').on('click', '#dispatchSearchCleanFilterButton', function (){
        $('#dispatchFilter').val("");
        reloadDispatchGridView();
    });
}

function reloadDispatchGridView() {
    var jqDispatchDropDowns = $('#dispatchTab');
    var form = jqDispatchDropDowns.find("#dispatchActiveForm");
    if (form.find(".has-error").length) {
        return false;
    }
    $('#loading').show();
    $.pjax.reload({
        type: 'GET',
        url: '/dispatch/dispatch/heavy-dispatch',
        container: '#dispatchUnassignedGridview', // id to update content
        data: form.serialize(),
        timeout: 99999
    }).done(function () {
        $('#dispatchTableRecordsUpdate').val(false);
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
function getDispatchMapArray(assignedUserID) {
    var mapGridArray = [];
	$('#dispatchUnassignedGridview input:checked').each(function() {
		mapGridArray.push({
			MapGrid: $(this).attr('MapGrid'),
			AssignedUserID: assignedUserID,
			BillingCode: $(this).attr('BillingCode'),
			InspectionType: $(this).attr('InspectionType')
		});
	});
	return mapGridArray;
}

// Generate Dispatch Section Array;
function getDispatchSectionArray(assignedUserID) {
    var dispatchSectionArray = [];
	$('#dispatchUnassignedGridview #dispatchSectionGV input:checked').each(function() {
		dispatchSectionArray.push({
			MapGrid: $(this).attr("MapGrid"),
			SectionNumber: $(this).attr("SectionNumber"),
			AssignedUserID: assignedUserID,
			BillingCode: $(this).attr("BillingCode"),
			InspectionType: $(this).attr("InspectionType")
		});
	});
	return dispatchSectionArray;
}

// View Asset Modal (Dispatch, Assigned)
function viewAssetRowClicked(url, modalViewAsset, modalContentViewAsset, mapGrid) {
    $(modalViewAsset).modal('show')
        .find(modalContentViewAsset).html("Loading...");
    $(modalViewAsset).modal('show')
        .find(modalContentViewAsset).load(url);
		document.getElementById('assetModalTitle').innerHTML = '<h4>' + mapGrid + ' - Assets</h4>';
}

