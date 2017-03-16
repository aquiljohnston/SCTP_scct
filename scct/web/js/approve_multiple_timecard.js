// BULK DELETE
$(function () {
    var checkApprovedArray = [];
    var counter = 0;
    var flag = "Yes";
    var totalworkhours = -1;

    // disable single approve button once user clicked it
    $('#enable_single_approve_btn_id_timecard').click(function (e) {
        $(this).addClass('disabled');
    });

    $('#multiple_approve_btn_id').prop('disabled', true); //TO DISABLED

    // TODO: Merge these two click listeners
    $("#timeCardGV input[type=checkbox]").click(function () {
        var checkApproved = $(this).attr("approved");
        checkApprovedArray[counter++] = checkApproved;

        //alert("checkApproved is :　"+checkApproved);
        //var model = $(this).attr('model');
        var pks = $('#GridViewForTimeCard').yiiGridView('getSelectedRows');
        //alert("pks length is ：　"+pks.length);
        for (var i = 0; i < pks.length; i++) {
            // get approved value for this timecard
            flag = $("#timeCardGV input[timecardid=" + pks[i] + "]").attr("approved");

            // get totalworkhours for this timecard
            totalworkhours = $("#timeCardGV input[timecardid=" + pks[i] + "]").attr("totalworkhours");

            //alert("loop flag is "+flag+" i is : "+i);
            if (flag.toUpperCase() == "YES" || totalworkhours == .0) {
                flag = "Yes";
                break;
            } else {
                continue;
            }
        }

        if (!pks || pks.length != 0 && flag != "Yes") {
            $('#multiple_approve_btn_id').prop('disabled', false); //TO ENABLE

            // triggered when checkbox selected
            $('#multiple_approve_btn_id').click(function () {
                var confirmBox = confirm('Are you sure you want to approve this item?');
                if (confirmBox) {

                    $.ajax({
                        type: 'POST',
                        url: '/time-card/approve-multiple',
                        data: {timecardid: pks},
                        success: function (data) {
                            $.pjax.reload({container: '#GridViewForTimeCard'});
                        }
                    });
                } else {
                    e.stopImmediatePropagation();
                    e.preventDefault();
                }
            });
        } else {
            $('#multiple_approve_btn_id').prop('disabled', true);
        }
    });

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
});
	 