// BULK DELETE
$(function () {

    // disable single approve button once user clicked it
    $('#enable_single_approve_btn_id_timecard').click(function (e) {
        $(this).addClass('disabled');
    });

    $('#multiple_approve_btn_id').prop('disabled', true); //TO DISABLED

    $("#timeCardGV input[type=checkbox]").click(function () {
        var disable;
        if ($("#timeCardGV .kv-row-select input:checked").length != 0) {
            disable = false;
            $("#timeCardGV .kv-row-select input:checked").each(function () {
                if ($(this).attr("approved").toUpperCase() == "YES" || $(this).attr("totalworkhours") == 0) {
                    disable = true;
                }
            });
        } else {
            disable = true;
        }
        if (disable) {
            $('#multiple_approve_btn_id').prop('disabled', true);
            $('#export_timecard_btn').prop('disabled', true);
        } else {
            $('#multiple_approve_btn_id').prop('disabled', false); //TO ENABLE
            $('#export_timecard_btn').prop('disabled', false); //TO ENABLE
        }
    });

    applyTimeCardOnClickListeners();

});

function applyTimeCardOnClickListeners() {
    $('#multiple_approve_btn_id').click(function () {
        var primaryKeys = $('#GridViewForTimeCard').yiiGridView('getSelectedRows');
        var quantifier = "";
        if(primaryKeys.length <= 1 ) { // We don't expect 0 or negative but we need to handle it
            quantifier = "this item?";
        } else {
            quantifier = "these items?"
        }
        var confirmBox = confirm('Are you sure you want to approve ' + quantifier);
        if (confirmBox) {
            $.ajax({
                type: 'POST',
                url: '/time-card/approve-multiple',
                data: {
                    timecardid: primaryKeys
                },
                success: function (data) {
                    $.pjax.reload({container: '#w0'});
                }
            });
        } else {
            e.stopImmediatePropagation();
            e.preventDefault();
        }
    });
}


/*Converts javascript array to CSV format */
function ConvertToCSV(headerArray, dataArray) {
    var header = typeof headerArray != 'object' ? JSON.parse(headerArray) : headerArray;
    var array = typeof dataArray != 'object' ? JSON.parse(dataArray) : dataArray;

    /* Get table headers */
    var indexes = [];
    for (var i = 0; i < header.length; i++) {
        str += '<th scope="col">' + header[i]['title'] + '</th>';
        indexes.push(header[i]['title']);
    }

    /* Data */
    var str = '';
    var strIndexes = '';

    /* Write headers */
    for (var j = 0; j < indexes.length; j++) {
        if (j != indexes.length - 1 && isNaN(indexes[j])) {
            strIndexes += indexes[j] + ',';
        }
        else
            strIndexes += indexes[j];
    }
    str += strIndexes + '\r\n';

    /* write data */
    for (var i = 0; i < array.length; i++) {
        var line = '';
        for (var index in array[i]) {
            if (line != '') line += ','

            line += array[i][index];
        }

        str += line + '\r\n';
    }

    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth() + 1; //January is 0!
    var yyyy = today.getFullYear();

    if (dd < 10) {
        dd = '0' + dd
    }

    if (mm < 10) {
        mm = '0' + mm
    }
    today = mm + '/' + dd + '/' + yyyy;

    if (navigator.userAgent.indexOf('Firefox') != -1 && parseFloat(navigator.userAgent.substring(navigator.userAgent.indexOf('Firefox') + 8)) >= 3.6) {//Firefox

        var csvContent = "data:text/csv;charset=utf-8," + str;
        var encodedUri = encodeURI(csvContent);
        window.open(encodedUri);

    } else if (navigator.userAgent.indexOf('Chrome') != -1 && parseFloat(navigator.userAgent.substring(navigator.userAgent.indexOf('Chrome') + 7).split(' ')[0]) >= 15) {//Chrome

        var csvContent = "data:text/csv;charset=utf-8," + str;
        var encodedUri = encodeURI(csvContent);
        var link = document.createElement("a");
        link.setAttribute("href", encodedUri);


        link.setAttribute("download", "Report_" + today + ".csv");

        link.click(); // This will download the data file named "my_data.csv".
    } else if (navigator.userAgent.indexOf('Safari') != -1 && navigator.userAgent.indexOf('Version') != -1 && parseFloat(navigator.userAgent.substring(navigator.userAgent.indexOf('Version') + 8).split(' ')[0]) >= 5) {//Safari

    } else {
        str = str.replace(/[^\x00-\x7F]/g, "");
        var csvContent = str; //here we load our csv data
        var blob = new Blob([csvContent], {
            type: "text/csv;charset=utf-8;"
        });

        var date = new Date();
        navigator.msSaveBlob(blob, "Report_" + today + ".csv")
    }
}