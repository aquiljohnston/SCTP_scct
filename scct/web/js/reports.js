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
            if(selectedReport.ParmInspectorFlag == 1) {
                // reuse and rename the inspector dropdown
                if(selectedReport.ReportSPName.includes("TimeCard")) {
                    // show datepicker
                    inspectorsListHeader.text("Project List: ");
                } 
                toggleVisible([inspectorsListHeader[0], inspectorsDropdown[0]], "inline");
            } if(selectedReport.ParmBetweenDateFlag == 1) {
                // mapgrids need data query
                if(selectedReport.ParmDropDownFlag == 1) {
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
                if(selectedReport.ParmDropDownFlag == 1)
                    dataSync("mapgrid",reportStartDate,reportEndDate, null, null); 
            }  else
                // show erro message            
            submitButton.css('display', 'inline');
        } else {
            endDatePicker.datepicker('setDate', null);
            // todo: show error message
        }
    });
    // Submit Report Action
    submitButton.on("click", function(){
        $("#reportTable_wrapper").empty();
        // init variables
        var reportName, reportType, reportStartDate=null, reportEndDate=null, reportProjectID=null, mapgrid=null;
        reportName = selectedReport.ReportSPName;
        reportType = selectedReport.ReportType.trim();
        // get dates
        if(selectedReport.ParmBetweenDateFlag == 1) {
            reportStartDate = startDatePicker.datepicker("getDate");
            reportEndDate = endDatePicker.datepicker("getDate");
            if(reportStartDate !== null && reportEndDate !== null) {
                // format dates
                reportStartDate = reportStartDate.getUTCMonth()+1+"/"+reportStartDate.getUTCDate()+"/"+reportStartDate.getUTCFullYear();
                reportEndDate = reportEndDate.getUTCMonth()+1+"/"+reportEndDate.getUTCDate()+"/"+reportEndDate.getUTCFullYear();
            } 
        }
        if(selectedReport.ParmInspectorFlag == 1) {
            if(selectedReport.ReportSPName.includes("TimeCard")) {
                if(!inspectorsDropdown.val().includes("< All >"))
                    reportProjectID = "["+inspectorsDropdown.val()+"]";
            } else {
                console.log("else not timecard");
            }
        }    
        if(selectedReport.ParmDropDownFlag == 1) {
            // get mapgrid
            mapgrid = mapGridDropdown.val();
        }
        // execute server call   
        dataSync("report",reportStartDate,reportEndDate, reportProjectID, mapgrid);
    });
    // Export Report Data Action
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
        mapGridListHeader[0], mapGridDropdown[0], submitButton[0]], "none");
    
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
                "iDisplayLength": 250
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
function dataSync(ajaxCallType, reportStartDate, reportEndDate, reportProjectID, mapgrid){
    $.ajax({
        type: "POST",
        url: "reports/get-report",
        data: {
            ReportName: selectedReport.ReportSPName,
            ReportType: selectedReport.ReportType.trim(),
            StartDate: reportStartDate,
            EndDate: reportEndDate,
            Project: reportProjectID,
            Mapgrid: mapgrid
        },
        beforeSend: function () {
            $('#loading').show();
            console.log("Starting query for " + ajaxCallType);
            console.log("reportProjectID: " + reportProjectID + ", mapgrid: " + mapgrid + ", reportName: " + selectedReport.ReportSPName+ ", reportType: " + selectedReport.ReportType.trim() + ", reportStartDate: " + reportStartDate + ", reportEndDate: " + reportEndDate);  
        },
        success: function (data) {
            if(ajaxCallType === "mapgrid")
                loadMapgrids(JSON.parse(data));
            else if(ajaxCallType === "report")
                loadTable(JSON.parse(data));
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
function loadMapgrids(data) {
    try {
        mapGridDropdown.empty();
        if (data.data.length > 0) {
            // load the projects dropdowns
            $.each(data.data, function(key, val) {
                mapGridDropdown.append($('<option>', {
                    value: key,
                    text : val 
                }));
            });
        } else {
            // add < All > anyway
            mapGridDropdown.append($('<option>', {
                value: null,
                text : "< ALL >"
            }));
        }
    } catch(err) {
        console.log("Ajax error, " + err);
        $("#dataMessage").text("An error occurred, please refresh your page and try again.");
    }
    toggleVisible([mapGridListHeader[0], mapGridDropdown[0], submitButton[0]], "inline");
}