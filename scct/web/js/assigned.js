/**
 * Created by tzhang on 5/31/2017.
 */
$(function () {

	//may be able to eliminate some of these 'global' variables that don't provide much value
    var assignedGV = $("#assignedGV");
    assignedSection_SectionNumber = [];
    assignedSection_MapGrid = [];
    assignedMap_MapGrid = [];
    assignedAssets_WorkOrderID = [];
    assignedAssets_AssignedUserId = [];


    $('#UnassignedButton').prop('disabled', true); //TO DISABLED

    //assigned Seachfilter listener
    $(document).off('keypress', '#assignedFilter').on('keypress', '#assignedFilter', function (e) {
        if (e.keyCode === 13 || e.keyCode === 10) {
            e.preventDefault();
            //reset page number to 1
            $('#assignedPageNumber').val(1);
            reloadAssignedGridView();
        }
    });
    $(document).off('change', '#assignPageSize').on('change', '#assignPageSize', function () {
        $('#assignedTableRecordsUpdate').val(true);
        reloadAssignedGridView();
    });

    //pagination listener on assigned page
    $(document).off('click', '#assignedPagination .pagination li a').on('click', '#assignedPagination .pagination li a', function (event) {
        event.preventDefault();
        var page = $(this).data('page') + 1; // Shift by one to 1-index instead of 0-index.

        $('#assignedPageNumber').val(page);
        var form = $("#AssignForm");
        $('#loading').show();
        $.pjax.reload({
            container: "#assignedGridview",
            timeout: 99999,
            url: form.attr("action"),
            type: "GET",
            data: form.serialize()
        }).done(function () {
        });
        $('#assignedGridview').on('pjax:success', function (event, data, status, xhr, options) {
            console.log("Success");
            $('#loading').hide();
        });
        $('#assignedGridview').on('pjax:error', function (event, data, status, xhr, options) {
            console.log("Error");
            //window.location.reload(); // Can't leave them stuck
        });
    });

    //expandable row column listener
    $(document).off('kvexprow:toggle', "#assignedTable #assignedGV").on('kvexprow:toggle', "#assignedTable #assignedGV", function (event, ind, key, extra, state) {
        console.log('Toggled expand row');
		keyStr = JSON.stringify(key);
        var isCheckDisabled = $(this).find("[data-key='"+keyStr+"']").find('input[type=checkbox]').is(':disabled');
        var inProgressFlag = $(this).find("[data-key='"+keyStr+"']").find('input[type=checkbox]').attr('InProgressFlag');
        if (isCheckDisabled){
            if (inProgressFlag != "1")
                $(this).find("[data-key='"+keyStr+"']").find('.assignedCheckbox input[type=checkbox]').prop('disabled', false);
        }else{
			$(this).find("[data-key='"+keyStr+"']").removeClass('danger');
			$(this).find("[data-key='"+keyStr+"']").find('.assignedCheckbox input[type=checkbox]').prop('checked', false).prop('disabled', true);
        }
        assignedMap_MapGrid =$("#assignedGridview #assignedGV").yiiGridView('getSelectedRows');
        console.log("assignedMap_MapGrid: " +assignedMap_MapGrid.length);

        //check to see if need to disable/enable add surveyor button
        if (assignedMap_MapGrid.length > 0){
          $("#UnassignedButton").prop('disabled', false);
        }else {
          $("#UnassignedButton").prop('disabled', true);
        }
    });
	
	//gets map data
    //checkbox listener on table mapgrids
    $(document).off('click', '.assignedCheckbox input[type=checkbox]').on('click', '.assignedCheckbox input[type=checkbox]', function () {
		//if checked un-check section items
        if($(this).is(':checked')){
            $(".kv-expanded-row[data-key='"+$(this).val()+"'] #assignedSectionGV-container>table>tbody>tr").removeClass('danger');
            $(".kv-expanded-row[data-key='"+$(this).val()+"']").find('input[type=checkbox]').prop('checked', false);
        }
        assignedMap_MapGrid = $("#assignedGridview #assignedGV").yiiGridView('getSelectedRows');
        if (assignedMap_MapGrid.length > 0) {
            $("#UnassignedButton").prop('disabled', false);
        } else {
            $("#UnassignedButton").prop('disabled', true);
		}
		
		//what is this?
        assignedMap_MapGrid = assignedMap_MapGrid.filter( function( el ) {
            return assignedSection_SectionNumber.indexOf( el ) < 0;
        } );
        console.log(assignedMap_MapGrid);
    });

	//get section data
    //checkbox listener on table sections
    $(document).off('click', '.assignedSectionCheckbox input[type=checkbox]').on('click', '.assignedSectionCheckbox input[type=checkbox]', function () {
        assignedSection_SectionNumber =$("#assignedGridview #assignedSectionGV").yiiGridView('getSelectedRows');
        var mapGridSelected = $(this).attr('MapGrid');
        if ($(this).is(':checked')){
                assignedSection_MapGrid.push(mapGridSelected);
            console.log("assignedSection_MapGrid: "+assignedSection_MapGrid);
        }else{
            var index = assignedSection_MapGrid.indexOf(mapGridSelected);
            if (index > -1) {
                assignedSection_MapGrid.splice(index, 1);
            }
        }
        // check to see if need to disable/enable add surveyor button
        if (assignedMap_MapGrid.length > 0 || assignedSection_SectionNumber.length > 0){
            $("#UnassignedButton").prop('disabled', false);
        }else{
            $("#UnassignedButton").prop('disabled', true);
        }
    });

    // Assets Modal checkbox Listener
    $(document).off('click', '.unassignAssetsCheckbox input[type=checkbox]').on('click', '.unassignAssetsCheckbox input[type=checkbox]', function () {
        assignedAssets_WorkOrderID = $("#assetGV").yiiGridView('getSelectedRows');
        if($(this).prop('checked') == true){
            //do something
            assignedAssets_AssignedUserId.push($(this).attr('assigneduserid'));
        }
        if (assignedAssets_WorkOrderID.length > 0) {
            $("#UnassignedAssetsButton").prop('disabled', false);
        } else
            $("#UnassignedAssetsButton").prop('disabled', true);
    });
	
    if (assignedMap_MapGrid.length > 0 || assignedSection_SectionNumber.length > 0)
        unassignCheckboxListener();
	
	//remove surveyor button on assigned index
    $(document).off('click', '#UnassignedButton').on('click', '#UnassignedButton', function () {
        $('#unassignConfirmationModal').modal('show')
			.find('#unassignConfirmationModalContent').html("Loading...");;
		//probably need to update these two function calls to return office name
		assignedMapData = getAssignedMapArray();
		assignedSectionData = getAssignedSectionArray();
		assignedDataArray = assignedMapData.concat(assignedSectionData);
		$('#unassignConfirmationModal').modal('show')
            .find('#unassignConfirmationModalContent')
            .load('/dispatch/assigned/view-unassign-confirmation', {assignedUserMaps: assignedDataArray});
    });	

    $(document).off('click', '#UnassignedAssetsButton').on('click', '#UnassignedAssetsButton', function () {
        $("#unassigned-message").find('span').html(getSelectedUserName(assignedAssets_WorkOrderID, assignedAssets_AssignedUserId));
        $("#unassigned-message").css("display", "block");
		//get asset data before it get reset
		unassignAssetsData = getAssignedAssetsArray();
		$('#modalViewAssetAssigned').modal('hide');
        // When the user clicks buttons, close the modal
        $('#unassignedCancelBtn').click(function () {
            $("#unassigned-message").css("display", "none");
        });
        $('#unassignedConfirmBtn').click(function () {
            $("#unassigned-message").css("display", "none");
            unassignAssetsButtonListener(unassignAssetsData);
        });
    });
	
	//reset checked assets on modal close
	$('#modalViewAssetAssigned').on('hidden.bs.modal', function () {
		assignedAssets_WorkOrderID = [];
	});

    $(document).off('click', '#assignedSearchCleanFilterButton').on('click', '#assignedSearchCleanFilterButton', function (){
        $('#assignedFilter').val("");
        reloadAssignedGridView();
    });
});

function unassignCheckboxListener() {
    $(".assignedCheckbox input[type=checkbox]").click(function () {
        var pks = $("#assignedGridview #assignedGV").yiiGridView('getSelectedRows');
        if (!pks || pks.length != 0) {
            $('#UnassignedButton').prop('disabled', false); //TO ENABLE
        } else {
            $('#UnassignedButton').prop('disabled', true);
        }
    });
}

function reloadAssignedGridView() {
    var jqAssignedDropDowns = $('#assignedDropdownContainer');
    var form = jqAssignedDropDowns.find("#AssignForm");
    if (form.find(".has-error").length) {
        return false;
    }
    $('#loading').show();
    $.pjax.reload({
        type: 'GET',
        url: form.attr("action"),
        container: '#assignedGridview', // id to update content
        data: form.serialize(),
        timeout: 99999
    }).done(function () {
        $('#loading').hide();
        $('#addSurveyor').prop('disabled', true);
        $('#UnassignedButton').prop('disabled', true);
        $('#assignedTableRecordsUpdate').val(false);
        unassignCheckboxListener();
    });
}

// Generate Assigned Map Array;
function getAssignedMapArray() {
    var mapGridArray = [];
	$('#assignedGV-container .assignedCheckbox input:checked').each(function() {
		mapGridArray.push({
			MapGrid: $(this).attr('MapGrid'),
			AssignedUserID: $(this).attr('AssignedToID'),
			BillingCode: $(this).attr('BillingCode'),
			InspectionType: $(this).attr('InspectionType'),
			OfficeName: $(this).attr('OfficeName'),
		})
	});
	return mapGridArray;
}

// Generate Assigned Section Array;
function getAssignedSectionArray() {
    var assignedSectionArray = [];
	$('#assignedSectionGV-container .assignedSectionCheckbox input:checked').each(function() {
		assignedSectionArray.push({
			MapGrid: $(this).attr('MapGrid'),
			SectionNumber: $(this).attr('SectionNumber'),
			AssignedUserID: $(this).attr('AssignedToID'),
			BillingCode: $(this).attr('BillingCode'),
			InspectionType: $(this).attr('InspectionType'),
			OfficeName: $(this).attr('OfficeName'),
		})
	});
	return assignedSectionArray;
}

// Generate unAssign Data Array for asset level unassign
function getSelectedUserName(assignedAssets_WorkOrderID, assignedAssets_AssignedUserId) {
    var selectedAssetsUser = "";
    if (assignedAssets_WorkOrderID != "" || assignedAssets_WorkOrderID.length > 0){
        for (var i = 0; i < assignedAssets_WorkOrderID.length; i++){
			var assetAddress = $("#assetGV input[workorderid=" + assignedAssets_WorkOrderID[i] + "][assigneduserid="+assignedAssets_AssignedUserId[i]+"]").attr("assetAddress");
            var userName_Assets = $("#assetGV input[workorderid=" + assignedAssets_WorkOrderID[i] + "][assigneduserid="+assignedAssets_AssignedUserId[i]+"]").attr("AssignedTo");
            selectedAssetsUser += "<li>" + assetAddress + " : " + userName_Assets + "</li>";
        }
    }
    var selectedUserNameList = "<ul>"+selectedAssetsUser+"</ul>";
    return selectedUserNameList;
}

// Generate unique key from Map Grid table
function getUniqueMapGridKey(assignedSection_SectionNumber, assignedMap_MapGrid) {

    var assignedMap_MapGrid = assignedMap_MapGrid.filter(function (val) {
        return assignedSection_SectionNumber.indexOf(val) == -1;
    });
    return assignedMap_MapGrid;
}

// Reset Value After Unassigning Work
function resetValue() {
    assignedSection_SectionNumber = [];
    assignedSection_MapGrid = [];
    assignedMap_MapGrid = [];
}

// Unassign Assets Button Listener
function unassignAssetsButtonListener(unassignAssetsData) {
    var form = $("#AssignForm");
    $('#loading').show();
    $.ajax({
        url: '/dispatch/assigned/unassign',
        data: {unassignMap: [], unassignSection: [], unassignAsset: unassignAssetsData},
        type: 'POST',
        beforeSend: function () {
            $('#loading').show();
        }
    }).done(function () {
        assignedAssets_WorkOrderID = [];
        assignedAssets_AssignedUserId = [];

        $.pjax.reload({
            container: '#assignedGridview',
            timeout: 99999,
            type: 'GET',
            url: form.attr("action"),
            data: form.serialize()
        });
        $('#assignedGridview').on('pjax:success', function () {
            $('#loading').hide();
            resetValue();
        });
        $('#assignedGridview').on('pjax:error', function (e) {
            resetValue();
            e.preventDefault();
        });
    });

    // disable remove surveyor button again
    $('#UnassignedAssetsButton').prop('disabled', true); //TO DISABLED
}

// get Selected Assets
function getAssignedAssetsArray() {
    var assetsArray = [];
    if (assignedAssets_WorkOrderID.length > 0){
        $('#assetGV-container input:checked').each(function() {
            assetsArray.push({
                WorkOrderID: $(this).attr('WorkOrderID'),
                AssignedUserID: $(this).attr("AssignedUserID")
            })
        });
        return assetsArray;
    }else{
        return assetsArray;
    }
}