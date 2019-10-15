/**
 * Created by Jose Pinott on 4/24/2018.
 */

 // init variables
 function initVars() {
    reportsArray = new Array();
    reportsDropdown = $("#reportsDropdown"), 
    mapGridListHeader = $("#mapGridListHeader"),
    mapGridDropdown = $("#parmDropdown"),
    inspectorsListHeader = $("#inspectorListHeader"),
    inspectorsDropdown = $("#inspectorsDropdown"),  
    parmDropdown = $("#parmDropdown"),
    beginDate = $("#beginDate"),
    endDate = $("#endDate"),
    exportButton = $("#export"),
    submitButton = $("#go"),
    noSelectionError = $("#noSelectionError"),
    noDateError = $("#noDateError"),
    selectDateFirstError = $("#selectDateFirstError"),
    startDatePicker = $("#datePickerBeginDate").datepicker({minDate: "1/1/"+(new Date()).getFullYear(), maxDate : 'now'}),
    endDatePicker = $('#datePickerEndDate').datepicker({minDate: "1/31/"+(new Date()).getFullYear(), maxDate : 'now', maxDate : 'now'});
    oTable = null;
    selectedReport = new Object();
    queryResultsAray = null;
 }

$(function () {
    var reports = window.location.pathname.replace(/\/+$/, "");
    if (reports == "/reports") {
        initVars();
        // tmp fix: need to consolidate reports and reports3 unto 1 file
        initListeners();
        $.ajax({
            type: "GET",
            url: "reports/get-dropdowns-data",
            beforeSend: function () {
                $('#loading').show();
            },
            success: function (data) {
                var dataResultArray = JSON.parse(data);
                // console.log("success called: " + JSON.stringify(dataResultArray));
                // load the reports dropdowns
                $.each(dataResultArray.dropdowns.reports, function (i, item) {
                    // copy report to object
                    reportsArray.push(item);
                    reportsDropdown.append($('<option>', { 
                        value: item.ReportSPName,
                        text : item.ReportDisplayName 
                    }));
                });
                // load the projects dropdowns
                $.each(dataResultArray.projects, function(key, val) {
                    inspectorsDropdown.append($('<option>', {
                        value: key,
                        text : val 
                    }));
                });
                $('#loading').hide();
            }
        });
    }
});

//--------------- Listeners ---------------//
function initListeners() {
    // report selected
    reportsDropdown.on("change", function(){
        resetDropdowns();
        $("#reportTable_wrapper").empty();
        if(reportsDropdown.val() == "Please make a selection") {
            // reset values
            toggleVisible([beginDate[0],endDate[0], inspectorsListHeader[0], inspectorsDropdown[0], 
                    mapGridListHeader[0], mapGridDropdown[0], submitButton[0]], "none");
            $('#dataMessage').empty();
        } else {
            getReport(reportsDropdown.val());
            if(selectedReport.ParmBetweenDateFlag == 1) {
                // mapgrids need data query
                if(selectedReport.ParmDropDownFlag == 1 || selectedReport.Parm == 1) {
                    toggleVisible([beginDate[0],endDate[0]], "inline");
                } else
                    toggleVisible([beginDate[0],endDate[0], submitButton[0]], "inline");
            } else {
                // only show submit and export button
                toggleVisible([submitButton[0]], "inline");
            } 
        }
    });
    // datepickers
    startDatePicker.change(function(){
        endDatePicker.datepicker('setDate', null);
        if(startDatePicker.datepicker("getDate") != null)
            endDatePicker.datepicker("option", "minDate", startDatePicker.datepicker("getDate"));
        else 
            submitButton.css('display', 'none');
        if(selectedReport.ParmDropDownFlag == 1) {
            toggleVisible([mapGridListHeader[0], mapGridDropdown[0], submitButton[0]], "none");
            $('#dataMessage').empty();
        }
    });
    endDatePicker.change(function(){
        if(startDatePicker.datepicker("getDate") != null) {
            reportStartDate = startDatePicker.datepicker("getDate");
            reportEndDate = endDatePicker.datepicker("getDate");
            if(reportStartDate !== null && reportEndDate !== null) {
                // format dates
                reportStartDate = reportStartDate.getUTCMonth()+1+"/"+reportStartDate.getUTCDate()+"/"+reportStartDate.getUTCFullYear();
                reportEndDate = reportEndDate.getUTCMonth()+1+"/"+reportEndDate.getUTCDate()+"/"+reportEndDate.getUTCFullYear();
                // get mapgrids
                if(selectedReport.ParmDropDownFlag == 1) {
                    dataSync("mapgrid",reportStartDate,reportEndDate, null, null); 
                    toggleVisible([mapGridListHeader[0], mapGridDropdown[0], submitButton[0]], "inline");
                } 
                // get inspectors
                if(selectedReport.ParmInspectorFlag == 1) {
                    dataSync("inspector",reportStartDate,reportEndDate, null, null);
					//show inspector dropdown
                    inspectorsListHeader.text("Inspector List: ");
                    toggleVisible([inspectorsListHeader[0], inspectorsDropdown[0], submitButton[0]], "inline");
                }
				console.log(selectedReport);
                // reuse and rename the inspector dropdown for project
                if(selectedReport.ParmProjectFlag == 1) {
                    dataSync("timeCard", reportStartDate, reportEndDate, null, null);
                    // show project dropdown
                    inspectorsListHeader.text("Project List: ");
                    toggleVisible([inspectorsListHeader[0], inspectorsDropdown[0]], "inline");
                }
				// custom logic for taskout report
                if(selectedReport.ReportSPName == 'spRptEmployeeTaskOutDetails') {
                    dataSync("taskOut", reportStartDate, reportEndDate, null, null);
                    // show project dropdown
                    inspectorsListHeader.text("Task Types: ");
                    toggleVisible([inspectorsListHeader[0], inspectorsDropdown[0]], "inline");
                }
                submitButton.css('display', 'inline');
            }
        } else {
            endDatePicker.datepicker('setDate', null);
            // todo: show error message
        }
    });
    // Submit Report Action
    submitButton.on("click", function(){
        $("#reportTable_wrapper").empty();
        $('#dataMessage').empty();
        // init variables
        var reportName, reportType, reportStartDate=null, reportEndDate=null, dropdownParam=null, mapgrid=null;
        reportName = selectedReport.ReportSPName;
        reportType = selectedReport.ReportType.trim();
        // set dates
        if(selectedReport.ParmBetweenDateFlag == 1) {
            reportStartDate = startDatePicker.datepicker("getDate");
            reportEndDate = endDatePicker.datepicker("getDate");
            //  error check in OR condition: user did not select end date
            if(reportStartDate !== null && reportEndDate !== null || reportStartDate !== null && reportEndDate == null) {
                // format dates
                reportStartDate = reportStartDate.getUTCMonth()+1+"/"+reportStartDate.getUTCDate()+"/"+reportStartDate.getUTCFullYear(); 
                if(reportEndDate == null){
                    reportEndDate = new Date();
                }
                reportEndDate = reportEndDate.getUTCMonth()+1+"/"+reportEndDate.getUTCDate()+"/"+reportEndDate.getUTCFullYear();
            } 
        }
        // set inspectors
        if(selectedReport.ParmInspectorFlag == 1)
            dropdownParam = inspectorsDropdown.val(); 
        // use inspector dropdown as project 
        if(selectedReport.ParmProjectFlag == 1) {
            if(!inspectorsDropdown.val().toLocaleLowerCase().includes("<All>".toLocaleLowerCase())) {
                console.log("selected report: " + inspectorsDropdown.val());
                dropdownParam = "["+inspectorsDropdown.val()+"]";
            } else
                dropdownParam = inspectorsDropdown.val();
        } 
        // set mapgrid
        if(selectedReport.ParmDropDownFlag == 1)
            mapgrid = mapGridDropdown.val();
        // execute server call   
        dataSync("report",reportStartDate,reportEndDate, dropdownParam, mapgrid);
        if(selectedReport.ExportFlag == 1)
            toggleVisible([exportButton[0]], "inline");
    });
    // Export Report Data Action
    $("#export").click(function (e) {
        console.log("Export clicked!");
        convertToCSV();
    });
}
//--------------- helper functions ---------------//
function resetDropdowns() {
    inspectorsDropdown.val("");
    startDatePicker.val("");
    endDatePicker.val("");
    noSelectionError.val("");
    noDateError.val("");
    selectDateFirstError.val("");
    $('#dataMessage').empty();
    if (oTable != null)
        oTable.fnDestroy();
    $('#reportTable').empty();
    selectedReport = null;
    toggleVisible([beginDate[0],endDate[0], inspectorsListHeader[0], inspectorsDropdown[0], 
        mapGridListHeader[0], mapGridDropdown[0], submitButton[0], exportButton[0]], "none");
    
}
function toggleVisible(arr, display) {
    for (var i = 0; i < arr.length; i++) {
        arr[i].style.display = display;
    }
}
//return true if visible, false if display:none
function isVisible(element) {
    return element.style.display !== "none";
}
// load json data
function loadTable(data) {
    try {
        if (oTable != null) {
            oTable.fnDestroy();
            $('#reportTable').empty();
        }
        if (data.data.length > 0) {
            oTable = $('#reportTable').dataTable({
                "pagingType": "full_numbers",
                "scrollX": true,
                "data": data.data,
                "columns": data.columns,
                "lengthMenu": [10, 25, 50, 100, 250, 500],
                "iDisplayLength": 250,
                "order": []
            });
        } else {
            $("#dataMessage").text("No data available for the specified data range.");
        }
    }
    catch(err) {
        console.log("Ajax error: " + err);
        $("#dataMessage").text("An error occurred, please refresh your page and try again.");
    }
}
// finds/returns report in reports array
function getReport(name) {
    reportsArray.forEach(element => {
        if(element.ReportSPName === name)
            selectedReport = element;
    });
}
// get map grids call
function dataSync(ajaxCallType, reportStartDate, reportEndDate, dropdownParam, mapgrid){
    $.ajax({
        type: "POST",
        url: "reports/get-report",
        data: {
            ReportName: selectedReport.ReportSPName,
            ReportType: selectedReport.ReportType.trim(),
            StartDate: reportStartDate,
            EndDate: reportEndDate,
            Project: dropdownParam,
            Mapgrid: mapgrid
        },
        beforeSend: function () {
            $('#loading').show();
        },
        success: function (data) {
            if(ajaxCallType === "mapgrid")
                loadDropdowns(JSON.parse(data), mapGridDropdown);
            else if(ajaxCallType === "report") {
                queryResultsAray = JSON.parse(data);
                loadTable(queryResultsAray);
            } else
                loadDropdowns(JSON.parse(data), inspectorsDropdown);
            $('#loading').hide();
        },
        error: function(xhr, textStatus, error) {
            //Here the status code can be retrieved like;
            console.log("Ajax error: " + error + ", report: " + ajaxCallType +", code: " + xhr.status);
            $("#dataMessage").text("An error occurred, please refresh your page and try again.");
            $('#loading').hide();
        }
    });
}
function loadDropdowns(data, dropdown){
    try {
        dropdown.empty();
        if (data.data.length > 0) {
            // load the projects dropdowns
            $.each(data.data, function(key, val) {
                dropdown.append($('<option>', {
                    value: val[0],
                    text : val[1] 
                }));
            });
        } else {
            // add < All > anyway
            dropdown.append($('<option>', {
                value: "<All>",
                text : "ALL"
            }));
        }
    } catch(err) {
        console.log("Ajax error, " + err);
        $("#dataMessage").text("An error occurred, please refresh your page and try again.");
    }
}

/*Converts javascript array to CSV format */
function convertToCSV() {
    console.log("queryResultsAray obj: " + JSON.stringify(queryResultsAray));
    console.log("columns: " + JSON.stringify(queryResultsAray.columns));
    console.log("data: " + JSON.stringify(queryResultsAray.data));
    var header = queryResultsAray.columns;
    var array = queryResultsAray.data;

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
            strIndexes += indexes[j] + ';';
        }
        else
            strIndexes += indexes[j];
    }
    str += strIndexes + '\r\n';

    /* write data */
    for (var i = 0; i < array.length; i++) {
        var line = '';
        for (var index in array[i]) {
            if (line != '') line += ';';

            line += (array[i][index] !== null) ? array[i][index] : ''; // Append the cell data
			line = line.replace(/(\r\n|\n|\r)/gm," ");
            // The ternary operator changes nulls to ''
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

    str = str.replace(/[^\x00-\x7F]/g, "");
    str = 'sep=;\r\n' + str;
    //using FileSaver.min.js
    var blob = new Blob([str], {type: "text/csv;charset=utf-8"});
	//consider adding report type to file name?
    saveAs(blob, "Report_" + today + ".csv");
}