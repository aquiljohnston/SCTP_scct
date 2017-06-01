/**
 * Created by tzhang on 5/31/2017.
 */
$(function () {

    // Unassign Table Pagination default listener
    /*function AssignedPaginationListener() {
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
                type: "post",
                data: form.serialize()
            }).done(function () {
                //resetDispatchButton();
            });
            $('#dispatchUnassignedGridview').on('pjax:success', function (event, data, status, xhr, options) {
                $('#loading').hide();
            });
            $('#dispatchUnassignedGridview').on('pjax:error', function (event, data, status, xhr, options) {
                //window.location.reload();
                console.log("Error");
            });
        });
    }*/

    $('#UnassignedButton').prop('disabled', false); //TO DISABLED
    /*$('#dialog-unassign').dialog({autoOpen: false, modal: true, show: "blind", hide: "blind"});
     $('#dialog-add-surveyor').dialog({autoOpen: false, modal: true, show: "blind", hide: "blind"});*/

    //dispatchUnassignedContainer Seachfilter listener
    $(document).off('keypress', '#assignedFilter').on('keypress', '#assignedFilter', function (e) {
        if (e.keyCode === 13 || e.keyCode === 10) {
            e.preventDefault();
            reloadAssignedGridView();
        }
    });
    $(document).off('change', '#assignPageSize').on('change', '#assignPageSize', function () {
        reloadAssignedGridView();
    });
    unassignCheckboxListener();
});

$(document).off('click', '#UnassignedButton').on('click', '#UnassignedButton', function () {
    $("#unassigned-message").css("display", "block");
    // When the user clicks buttons, close the modal
    $('#unassignedCancelBtn').click(function () {
        $("#unassigned-message").css("display", "none");
    });
    $('#unassignedConfirmBtn').click(function () {
        $("#unassigned-message").css("display", "none");
        unassignButtonListener();
    });
});

function unassignButtonListener() {
    var pks = $("#assignedGridview #assignedGV").yiiGridView('getSelectedRows');
    console.log("Checked Rows " + pks);
    var form = $("#AssignForm");
    $('#loading').show();
    $.ajax({
        url: '/dispatch/assigned/unassign',
        data: {MapGrid: pks, AssignedToIDs: getAssignedUserIDs()},
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
            unassignCheckboxListener();
        });
        $('#assignedGridview').on('pjax:error', function (e) {
            e.preventDefault();
        });
    });

    // disable dispatch button again
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