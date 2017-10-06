/**
 * Created by tzhang on 5/31/2017.
 */
$(function () {

    var assignedGV = $("#assignedGV");
    assignedSection_SectionNumber = [];
    assignedSection_MapGrid = [];
    assignedMap_MapGrid = [];
    assignedAssets_WorkOrderID = [];


    $('#UnassignedButton').prop('disabled', true); //TO DISABLED

    //assigned Seachfilter listener
    $(document).off('keypress', '#assignedFilter').on('keypress', '#assignedFilter', function (e) {
        if (e.keyCode === 13 || e.keyCode === 10) {
            e.preventDefault();
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
      //assignedGV.on('kvexprow.toggle.kvExpandRowColumn', function (event, ind, key, extra, state) {
      //assignedGV.on('kvexprow:toggle', function (event, ind, key, extra, state) {
        console.log('Toggled expand row');
        var isCheckDisabled = $(this).find("[data-key='"+key+"']").find('input[type=checkbox]').is(':disabled');
        if (isCheckDisabled){
          $(this).find("[data-key='"+key+"']").find('.unassignCheckbox input[type=checkbox]').prop('disabled', false);
        }else{
          $(this).find("[data-key='"+key+"']").find('.unassignCheckbox input[type=checkbox]').prop('checked', false).prop('disabled', true);
            //assignedMap_MapGrid =$("#assignedGridview #assignedGV").yiiGridView('getSelectedRows');
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

    // set constrains:
    $(document).off('click', '.unassignCheckbox input[type=checkbox]').on('click', '.unassignCheckbox input[type=checkbox]', function () {
        assignedMap_MapGrid = $("#assignedGridview #assignedGV").yiiGridView('getSelectedRows');
        if (assignedMap_MapGrid.length > 0) {
            $("#UnassignedButton").prop('disabled', false);
        } else
            $("#UnassignedButton").prop('disabled', true);
        console.log(assignedMap_MapGrid);
    });

    //checkbox listener on section table
    $(document).off('click', '.assignedSectionCheckbox input[type=checkbox]').on('click', '.assignedSectionCheckbox input[type=checkbox]', function () {
        assignedSection_SectionNumber =$("#assignedGridview #assignedSectionGV").yiiGridView('getSelectedRows');
        var mapGridSelected = $(this).attr('MapGrid');
        if ($(this).is(':checked')){
            assignedSection_MapGrid.push(mapGridSelected);
        }else{
            assignedSection_MapGrid = jQuery.grep(assignedSection_MapGrid, function(value) {
                                        return value != mapGridSelected;
                                    });
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
        if (assignedAssets_WorkOrderID.length > 0) {
            $("#UnassignedAssetsButton").prop('disabled', false);
        } else
            $("#UnassignedAssetsButton").prop('disabled', true);
    });

    if (assignedMap_MapGrid.length > 0 || assignedSection_SectionNumber.length > 0)
        unassignCheckboxListener();

    $(document).off('click', '#UnassignedButton').on('click', '#UnassignedButton', function () {
        $("#unassigned-message").find('span').html(getSelectedUserName(getUniqueMapGridKey(assignedSection_SectionNumber, assignedMap_MapGrid), assignedSection_SectionNumber, assignedSection_MapGrid, assignedAssets_WorkOrderID));
        $("#unassigned-message").css("display", "block");
        // When the user clicks buttons, close the modal
        $('#unassignedCancelBtn').click(function () {
            $("#unassigned-message").css("display", "none");
        });
        $('#unassignedConfirmBtn').click(function () {
            $("#unassigned-message").css("display", "none");
            unassignButtonListener(assignedMap_MapGrid, assignedSection_SectionNumber);
        });
    });
    $(document).off('click', '#UnassignedAssetsButton').on('click', '#UnassignedAssetsButton', function () {
        $("#unassigned-message").find('span').html(getSelectedUserName("","", "",assignedAssets_WorkOrderID));
        $("#unassigned-message").css("display", "block");
        $('#modalViewAssetAssigned').modal('hide');
        // When the user clicks buttons, close the modal
        $('#unassignedCancelBtn').click(function () {
            $("#unassigned-message").css("display", "none");
        });
        $('#unassignedConfirmBtn').click(function () {
            $("#unassigned-message").css("display", "none");
            unassignAssetsButtonListener();
        });
    });
});

function unassignButtonListener(assignedMap_MapGrid, assignedSection_SectionNumber) {
    var pks = $("#assignedGridview #assignedGV").yiiGridView('getSelectedRows');
    var assignedGV = "assignedGV";
    var assignedSectionGV = "assignedSectionGV";
    unassignMapData = getAssignedMapArray(assignedMap_MapGrid);
    unassignSectionData = getAssignedSectionArray(assignedSection_SectionNumber);
    console.log("Checked Rows " + pks);
    var form = $("#AssignForm");
    $('#loading').show();
    $.ajax({
        url: '/dispatch/assigned/unassign',
        data: {unassignMap: unassignMapData, unassignSection: unassignSectionData},
        type: 'POST',
        beforeSend: function () {
            $('#loading').show();
        }
    }).done(function () {
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
            unassignCheckboxListener();
        });
        $('#assignedGridview').on('pjax:error', function (e) {
            resetValue();
            e.preventDefault();
        });
    });

    // disable remove surveyor button again
    $('#UnassignedButton').prop('disabled', true); //TO DISABLED
}

function unassignCheckboxListener() {
    $(".unassignCheckbox input[type=checkbox]").click(function () {
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

function getAssignedUserIDs() {
    var pks = $('#assignedGV').yiiGridView('getSelectedRows');
    //alert("pks length is ：　"+pks.length);
    var AssignedToIDs = [];
    for(var i=0; i < pks.length; i++){
        // get assignedUserID associate with current MapGrid
        AssignedToIDs[i] = $(".kv-row-select input[MapGrid="+pks[i]+"]").attr("AssignedToID");
    }
    return AssignedToIDs;
}

// Asset modal view (not in use; using one in dispatch.js, sharing the same function)

// Generate Assigned Map Array;
function getAssignedMapArray(assignedMap_MapGrid) {
    var mapGridArray = [];
    if (assignedMap_MapGrid.length > 0){
        /*for (var i = 0; i < assignedMap_MapGrid.length; i++){
            mapGridArray.push({
                MapGrid: assignedMap_MapGrid[i],
                AssignedUserID: assignedUserID
            })
        }*/
        $('#assignedGV-container input:checked').each(function() {
            console.log("SELECTED MAP: "+$(this).attr('MapGrid'));
            mapGridArray.push({
                MapGrid: $(this).attr('MapGrid'),
                AssignedUserID: $(this).attr('AssignedToID')
            })
        });
        return mapGridArray;
    }else{
        return mapGridArray;
    }
}

// Generate Assigned Section Array;
function getAssignedSectionArray(assignedSection_SectionNumber) {
    var assignedSectionArray = [];
    if (assignedSection_SectionNumber.length > 0) {

        $('#assignedSectionGV-container input:checked').each(function() {
            console.log("SELECTED MAP - SECTION: "+$(this).attr('SectionNumber'));
            var userIDsCount = $(this).attr('AssignedToID').split(',');
            for (var i = 0; i < userIDsCount.length; i++) {
                assignedSectionArray.push({
                    MapGrid: $(this).attr('MapGrid'),
                    SectionNumber: $(this).attr('SectionNumber'),
                    AssignedUserID: userIDsCount[i]
                })
            }
        });
        return assignedSectionArray;
    }else{
        return assignedSectionArray;
    }
}

// Generate unAssign Data Array; combine mapGrid and section level
function getSelectedUserName(assignedMap_MapGrid, assignedSection_SectionNumber, assignedSection_MapGrid, assignedAssets_WorkOrderID) {
    var selectedMapGridUser = "";
    var selectedSectionUser = "";
    var selectedAssetsUser = "";
    if (assignedAssets_WorkOrderID != "" || assignedAssets_WorkOrderID.length > 0){
        for (var i = 0; i < assignedAssets_WorkOrderID.length; i++){
            var userName_Assets = $("#assetGV input[workorderid=" + assignedAssets_WorkOrderID[i] + "]").attr("AssignedTo");
            var ClientWorkOrderID = $("#assetGV input[workorderid=" + assignedAssets_WorkOrderID[i] + "]").attr("ClientWorkOrderID");
            selectedAssetsUser += "<li>" + ClientWorkOrderID + " : " + userName_Assets + "</li>";
        }
    }

    if (assignedMap_MapGrid != "" && assignedMap_MapGrid.length > 0 ) {
        for (var i = 0; i < assignedMap_MapGrid.length; i++) {
            var userName_MapGrid = $("#assignedGridview #assignedGV input[MapGrid=" + assignedMap_MapGrid[i] + "]").attr("UserName");
            selectedMapGridUser += "<li>" + assignedMap_MapGrid[i] + " : " + userName_MapGrid + "</li>"
        }
    }
    if (assignedSection_SectionNumber == "" || assignedSection_SectionNumber.length > 0) {
        for (var j = 0; j < assignedSection_SectionNumber.length; j++) {
            var userName_Section = $("#assignedGridview #assignedSectionGV input[SectionNumber=" + assignedSection_SectionNumber[j] + "][MapGrid$=" + assignedSection_MapGrid[j] + "]").attr("username");
            var mapGrid_Section = $("#assignedGridview #assignedSectionGV input[SectionNumber=" + assignedSection_SectionNumber[j] + "][MapGrid$=" + assignedSection_MapGrid[j] + "]").attr("mapgrid");
            selectedSectionUser += "<li>" + mapGrid_Section + " : " + userName_Section + "</li>"
        }
    }
    var selectedUserNameList = "<ul>"+selectedMapGridUser+selectedSectionUser+selectedAssetsUser+"</ul>";
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
function unassignAssetsButtonListener() {

    unassignAssetsData = getAssignedAssetsArray();
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
                AssignedUserID: $(this).attr('AssignedToID'),
                WorkOrderID: $(this).attr('WorkOrderID')
            })
        });
        return assetsArray;
    }else{
        return assetsArray;
    }
}