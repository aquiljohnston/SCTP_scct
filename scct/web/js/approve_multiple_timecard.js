// BULK DELETE
$(function () {
    $('#multiple_approve_btn_id').prop('disabled', true); //TO DISABLED

    $(document).off('change', "#timeCardGV input[type=checkbox]").on('change', "#timeCardGV input[type=checkbox]", function (e) {
        if ($("#GridViewForTimeCard").yiiGridView('getSelectedRows') != 0) {
            $('#multiple_approve_btn_id').prop('disabled', false); //TO ENABLE
        } else {
            $('#multiple_approve_btn_id').prop('disabled', true);
        }
    });

    applyTimeCardOnClickListeners();
    applyTimeCardSubmitButtonListener();


    $.ctGrowl.init( { position: 'absolute', bottom: '70px', left: '8px' });

    
});

function applyTimeCardOnClickListeners() {
    $('#multiple_approve_btn_id').off('click').click(function (event) {
        var primaryKeys = $('#GridViewForTimeCard').yiiGridView('getSelectedRows');
        var quantifier = "";


        if(primaryKeys.length <= 1 ) { // We don't expect 0 or negative but we need to handle it
            quantifier = "this item?";
        } else {
            quantifier = "these items?"
        }
       // var confirmBox = confirm('Are you sure you want to approve ' + quantifier);

        krajeeDialog.defaults.confirm.title = 'Approve';
        krajeeDialog.confirm('Are you sure you want to approve ' + quantifier, function (resp) {
        
        if (resp) {
            $.ajax({
                type: 'POST',
                url: '/time-card/approve-multiple',
                data: {
                    timecardid: primaryKeys
                }
            });
        } else {
            event.stopImmediatePropagation();
            event.preventDefault();
        }
      })
    });
}

function applyTimeCardSubmitButtonListener() {
    $('#multiple_submit_btn_id').off('click').click(function (event) {

         //apply css class that gives the tooltip gives
         //the appearance of being disabled via css
         //add returns false to prevent submission in
         //this state.
         if($(this).hasClass('off-btn')){
            return false;
        }

        // 

       // return false;


        var quantifier = "";
        var name = 'timecard_history_';
        var payroll = 'payroll_history_'
        var thIndex = $('th:contains("Project Name")').index();
        var projectName = $('#projectFilterDD').val();
       // var projectName = $('table td').eq(thIndex).text();
        var d = new Date();
        var minutes = (d.getMinutes() < 10 ? "0" : "") + d.getMinutes();
        var hours = ((d.getHours() + 11) % 12 + 1);
        dates = $.datepicker.formatDate('yy-mm-dd', new Date());

        timeCardName    = name+dates+"_"+hours+"_"+minutes+"_"+d.getSeconds();
        payRollFileName = payroll+dates+"_"+hours+"_"+minutes+"_"+d.getSeconds();
        dateRange = $('[name="DynamicModel[dateRangeValue]"]').val();
        dateRange = dateRange.split(",");
        timeCardComplete = false;
        payrollComplete = false;
        weekStart       = dateRange[0];
        weekEnd =       $.trim(dateRange[1]);


        //return false;
        var primaryKeys = $('#GridViewForTimeCard').yiiGridView('getSelectedRows');
        if(primaryKeys.length <= 1 ) { // We don't expect 0 or negative but we need to handle it
            quantifier = "this item?";
        } else {
            quantifier = "these items?"
        }
       // var confirmBox = confirm('Are you sure you want to submit ' + quantifier);

        krajeeDialog.defaults.confirm.title = 'Submit';
        krajeeDialog.confirm('Are you sure you want to submit ' + quantifier, function (resp) {
        
        if (resp) {
            var primaryKeys = $('#GridViewForTimeCard').yiiGridView('getSelectedRows');

            
            //usage $.ctGrow(msg,title,boostrap text class)
            $.ctGrowl.msg('Initiating the Submission.','Success','bg-success');
                   
            $.ajax({
                type: 'POST',
                url: '/time-card/ajax-process-comet-tracker-files?timeCardName='+timeCardName+
                '&payrollFileName=' + payRollFileName+
                '&projectName=' + projectName+
                '&weekStart=' + weekStart+
                '&weekEnd=' + weekEnd,
                success: function(data) {
                    console.log(data)
                    data = JSON.parse(data);
                    if(data.success){
           
                        $.ctGrowl.msg(data.message,'Success','bg-success');
                        //calls time_card.js reload function
                        reloadTimeCardGridView();
                        
                      //  $.ctGrowl.msg('Intiating File Download......','Success','bg-info');
                       
                       /* setTimeout(function() {

                         timeCard = window.open('/time-card/download-time-card-data?timeCardName='+timeCardName+
                            '&projectName=' + projectName+
                            '&weekStart=' + weekStart+
                            '&weekEnd=' + weekEnd, '_blank');

                         payroll =  window.open('/time-card/download-payroll-data?cardName='+payRollFileName+
                            '&projectName=' + projectName+
                            '&weekStart=' + weekStart+
                            '&weekEnd=' +  weekEnd, '_blank');               
                                    }, 3000);*/

                      //   $.ctGrowl.msg('File Download Success...','Success','bg-success');

                    } else {

                         $.ctGrowl.msg(data.message,'Error','bg-danger');
                    }
                    

            }
            });
           

        } else {
            event.stopImmediatePropagation();
            event.preventDefault();
        }
       }) 
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
