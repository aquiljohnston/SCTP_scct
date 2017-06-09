/**
 * Created by tzhang on 05/30/2016.
 */

$(function () {
    var currentPath = window.location.pathname;
    var reports = currentPath.replace(/\/+$/, "");//.substr(0, currentPath.length - 1);//.replace(/\/$/, "")
    console.log(reports);
    var oTable; //datatable variable
    var reportsToSP = {}; //map of report names -> stored procedures
    var reportsToParms = {}; //map of report names -> parm objects
    var reportsToExports = {}; //map of report names -> export values

    if (reports == "/reports") {

        var displayedResults;
        var reportsDropdown = document.getElementById("reportsDropdown");
        var inspectorsDropdown = document.getElementById("inspectorsDropdown");
        var parmDropdown = document.getElementById("parmDropdown");

        var beginDate = document.getElementById('beginDate'),
            endDate = document.getElementById('endDate'),
            beginDateView = document.getElementById('beginDateView'),
            endDateView = document.getElementById('endDateView'),
            selectDate = document.getElementById('selectDate'),
            exportButton = document.getElementById('export'),
            goButton = document.getElementById('go'),
            noSelectionError = document.getElementById('noSelectionError'),
            noDateError = document.getElementById('noDateError'),
            selectDateFirstError = document.getElementById('selectDateFirstError');

        $('#datePickerBeginDate').datepicker();
        $('#datePickerEndDate').datepicker();
        $('#datePickerSelectDate').datepicker();
        $('#datePickerBeginDateView').datepicker();
        $('#datePickerEndDateView').datepicker();

        //helper functions
        function toggleVisible(arr, display) {
            var i = 0;
            for (; i < arr.length; i++) {
                arr[i].style.display = display;
            }
        }

        //return true if visible, false if display:none
        function isVisible(element) {
            return element.style.display !== "none";
        }

        function buildParmDropdown(sp, parm, exports) {
            //bookmark

            $.ajax({
                type: "POST",
                url: "reports/get-parm-drop-down",
                data: {
                    //type: "parmDropdown",
                    ReportName: sp
                },
                beforeSend: function () {
                    $('#ajax-busy').show();
                },
                success: function (data) {
                    while (parmDropdown.lastChild && parmDropdown.lastChild.innerHTML !== "Please make a selection") {
                        parmDropdown.removeChild(parmDropdown.lastChild);
                    }
                    $('#ajax-busy').hide();
                    toggleVisible([parmDropdown], "block");
                    var results = JSON.parse(data);
                    $.each(results.options, function (i, obj) {
                        var option = document.createElement("option");
                        option.value = option.innerHTML = obj;
                        parmDropdown.appendChild(option);
                    });
                    $('#parmDropdown').on('change', function () {
                        if (oTable != null) {
                            oTable.fnDestroy(); //have to be destory first, then rebuild
                            $("#reportTable").empty(); //need to remove its dom elements, otherwise there will be problems rebuilding the table
                        }
                        toggleVisible([goButton, exportButton, selectDate, beginDate, endDate, beginDateView, endDateView], "none");
                        if ($('#parmDropdown').val().split(" - ")[0] === "99" && parm["ParmDateOverrideFlag"] === "1") { //dateoverride
                            /*if (parm["ParmDate"] === "1") {
                             toggleVisible([selectDate], "block");
                             $('#datePickerSelectDate').on('change', function () {
                             if ($('#datePickerSelectDate').val() !== "") {
                             toggleVisible([goButton], "inline");
                             if (exports) { toggleVisible([exportButton], "inline"); }
                             }
                             });
                             }*/
                            //else if (parm["ParmBetweenDate"] === "1") {
                            toggleVisible([beginDate, endDate], "block");
                            $('#datePickerBeginDate').on('change', function () {
                                if (oTable != null) {
                                    oTable.fnDestroy(); //have to be destory first, then rebuild
                                    $("#reportTable").empty(); //need to remove its dom elements, otherwise there will be problems rebuilding the table
                                }
                                if ($('#datePickerBeginDate').val() !== "" && $('#datePickerEndDate').val() !== "") {
                                    if ($('#datePickerBeginDate').datepicker("getDate") > $('#datePickerEndDate').datepicker("getDate")) {
                                        alert('Begin date cannot be greater than end date');
                                        toggleVisible([goButton, exportButton], "none");
                                    }
                                    else {
                                        toggleVisible([goButton], "inline");
                                        if (exports) {
                                            toggleVisible([exportButton], "inline");
                                        }
                                    }
                                }
                            });
                            $('#datePickerEndDate').on('change', function () {
                                if (oTable != null) {
                                    oTable.fnDestroy(); //have to be destory first, then rebuild
                                    $("#reportTable").empty(); //need to remove its dom elements, otherwise there will be problems rebuilding the table
                                }
                                if ($('#datePickerBeginDate').val() !== "" && $('#datePickerEndDate').val() !== "") {
                                    if ($('#datePickerBeginDate').datepicker("getDate") > $('#datePickerEndDate').datepicker("getDate")) {
                                        alert('Begin date cannot be greater than end date');
                                        toggleVisible([goButton, exportButton], "none");
                                    }
                                    else {
                                        toggleVisible([goButton], "inline");
                                        if (exports) {
                                            toggleVisible([exportButton], "inline");
                                        }
                                    }
                                }
                            });
                        }
                        else if ($('#parmDropdown').val().split(" - ")[0] === "99" && parm["ParmDateOverrideFlag"] !== "1") {
                            alert('Cannot override dates for this selection');
                        }
                        else { //user selects something other than 99 date override
                            toggleVisible([goButton], "inline");
                            if (exports) {
                                toggleVisible([exportButton], "inline");
                            }
                        }
                    });
                }
            });
        }

        function buildInspectorDropdown(beginDate, endDate, sp, parm, exports) {

            $.ajax({
                type: "POST",
                url: "reports/get-inspector-drop-down",
                data: {
                    ReportName: sp,
                    //type: "inspectors",
                    BeginDate: beginDate || null,
                    EndDate: endDate || null,
                    Parm: parm || null
                },
                beforeSend: function () {
                    $('#ajax-busy').show();
                },
                success: function (data) {
                    $('#ajax-busy').hide();
                    var results = JSON.parse(data);
                    toggleVisible([inspectorsDropdown], "block");
                    var inspectors = []; //userid lastname firstname

                    //clear existing dropdown
                    while (inspectorsDropdown.lastChild && inspectorsDropdown.lastChild.innerHTML !== "Please select an inspector") {
                        inspectorsDropdown.removeChild(inspectorsDropdown.lastChild);
                    }

                    //build dropdown
                    //added default option to inspector dropdown
                    var firstOption = document.createElement("option");
                    firstOption.innerHTML = "Please make a selection";
                    inspectorsDropdown.appendChild(firstOption);

                    $.each(results.inspectors, function (i, obj) {
                        //console.log(obj);
                        var option = document.createElement("option");
                        option.innerHTML = option.value = obj;
                        inspectorsDropdown.appendChild(option);
                    });

                    $('#inspectorsDropdown').on('change', function () {
                        if (oTable != null) {
                            oTable.fnDestroy(); //have to be destory first, then rebuild
                            $("#reportTable").empty(); //need to remove its dom elements, otherwise there will be problems rebuilding the table
                        }
                        if ($('#inspectorsDropdown').val() !== "Please select an inspector") {
                            toggleVisible([goButton], "inline");
                            if (exports) {
                                toggleVisible([exportButton], "inline");
                            }
                        }
                        else {
                            toggleVisible([goButton, exportButton], "none");
                        }
                    });
                }
            });
        }

        function buildTable() {//
            var starVal = null, endVal = null;
            var parameters = $('#reportsDropdown').find(":selected").attr('id').split('-');
            if (isVisible(beginDate) && isVisible(endDate)) {
                starVal = $('#datePickerBeginDate').val();
                endVal = $('#datePickerEndDate').val();
            } else {
                starVal = $('#datePickerBeginDateView').val();
                endVal = $('#datePickerEndDateView').val();
            }
            var parmDateOverride = isVisible(parmDropdown) ? $('#parmDropdown').val() : null;
            var userLogin = isVisible(inspectorsDropdown) ? $('#inspectorsDropdown').val() : null;

            var parmDateOverrideCheck = parmDateOverride != null ? 1 : 0;
            var userLoginCheck = userLoginCheck != null ? 1 : 0;

            var parmVar = parmDateOverrideCheck > userLoginCheck ? parmDateOverride : userLogin;

            $.ajax({
                type: "POST",
                url: "reports/get-reports",
                //url: "script/report/get_reports_new.php",
                data: {
                    ReportName: parameters[0],
                    type: "reports",
                    ParmVar: parmVar,
                    ParmDateOverride: isVisible(parmDropdown) ? $('#parmDropdown').val() : null,
                    ParmDateOverrideFlag: parameters[6],
                    UserLogin: isVisible(inspectorsDropdown) ? $('#inspectorsDropdown').val() : null,
                    BeginDate: starVal || null,
                    EndDate: endVal || $('#datePickerSelectDate').val() || null,
                    Parm: parameters[1] || null,
                    ParmBetweenDate: parameters[2],
                    ParmDate: parameters[3],
                    ParmInspector: parameters[4],
                    ReportType: parameters[7],
                },
                beforeSend: function () {
                    $('#ajax-busy').show();
                },
                success: function (data) {
                    $('#ajax-busy').hide();
                    var results = JSON.parse(data);
                    console.log(results.data);
                    //console.log(parameters);
                    //console.log(results);
                    if (oTable != null) {
                        oTable.fnDestroy(); //have to be destory first, then rebuild
                        $("#reportTable").empty(); //need to remove its dom elements, otherwise there will be problems rebuilding the table
                    }

                    oTable = $('#reportTable').dataTable({
                        "pagingType": "full_numbers",
                        "scrollX": true,
                        "data": results.data,
                        "columns": results.columns,
                        "columnDefs": [
                            {
                                /*"targets": [1],
                                 "visible": false,
                                 "searchable": false*/
                            }
                        ],
                        "lengthMenu": [10, 25, 50, 100, 250, 500],
                        "iDisplayLength": 250
                    });

                    //calculate height
                    $(".dataTable thead").on("click", "th.sorting_asc", function (event) {
                        $('.dataTables_scrollBody').css('height', window.innerHeight - 410 + "px");
                    });

                    //calcualate height
                    $(".dataTable thead").on("click", "th.sorting_desc", function (event) {
                        $('.dataTables_scrollBody').css('height', window.innerHeight - 410 + "px");
                    });

                    //windows resize height
                    $(window).resize(function () {
                        $('#reportTable').dataTable().fnAdjustColumnSizing(); //fix header resize doesn't align up problem
                        $(".dataTables_scrollBody").height(window.innerHeight - 410 + "px");
                    });

                    displayedResults = results;
                    //console.log(results);
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

                str = str.replace(/[^\x00-\x7F]/g, "");
                sep = '",';
                str = 'sep=;\r\n' + str;
                var csvData = new Blob([str], {type: 'data:text/csv;charset=utf-8'});
                var csvUrl = URL.createObjectURL(csvData);

                var link = document.createElement("a");
                link.setAttribute("href", csvUrl);
                link.setAttribute("download", "Report_" + today + ".csv");

                link.click(); // This will download the data file named "my_data.csv".
            } else if (navigator.userAgent.indexOf('Safari') != -1 && navigator.userAgent.indexOf('Version') != -1 && parseFloat(navigator.userAgent.substring(navigator.userAgent.indexOf('Version') + 8).split(' ')[0]) >= 5) {//Safari

            } else {
                str = str.replace(/[^\x00-\x7F]/g, "");
                sep = '";';
                str = 'sep=;\r\n' + str;

                var csvContent = str; //here we load our csv data
                var blob = new Blob([csvContent], {
                    type: "text/csv;charset=utf-8;"
                });

                var date = new Date();
                navigator.msSaveBlob(blob, "Report_" + today + ".csv")
            }
        }

        $.ajax({
            type: "GET",
            url: "reports/build-drop-down",
            beforeSend: function () {
                $('#loading').show();
            },
            success: function (data) {
                $('#loading').hide();
                //console.log(JSON.stringify(data));
                var results = JSON.parse(data);
                $.each(results.reports, function (i, obj) {
                    var option = document.createElement("option");
                    option.innerHTML = obj["ReportDisplayName"];
                    option.value = obj["ReportDisplayName"];
                    option.id = obj["ReportSPName"].trim();
                    option.id += "-" + obj["Parm"] + "-" + obj["ParmBetweenDateFlag"] + "-" + obj["ParmDateFlag"] + "-" + obj["ParmInspectorFlag"]
                        + "-" + obj["ParmDropDownFlag"] + "-" + obj["ParmDateOverrideFlag"] + "-" + obj["ReportType"];
                    reportsDropdown.appendChild(option);
                    var parms = {};
                    parms["ParmDateFlag"] = obj["ParmDateFlag"];
                    parms["ParmBetweenDateFlag"] = obj["ParmBetweenDateFlag"];
                    parms["ParmInspectorFlag"] = obj["ParmInspectorFlag"];
                    parms["Parm"] = obj["Parm"];
                    parms["ParmDropDownFlag"] = obj["ParmDropDownFlag"];
                    parms["ParmDateOverrideFlag"] = obj["ParmDateOverrideFlag"];
                    parms["ReportType"] = obj["ReportType"];
                    reportsToParms[obj["ReportDisplayName"]] = parms;
                    if (obj["ExportFlag"] === "1") {
                        reportsToExports[obj["ReportDisplayName"]] = obj["ExportFlag"];
                    }
                    reportsToSP[obj["ReportDisplayName"]] = obj["ReportSPName"];
                });
            }
        });

        $('#reportsDropdown').on('change', function () {
            var selectedReport = $(this).val();
            var parms = reportsToParms[selectedReport];
            var exp = reportsToExports[selectedReport];
            var sp = reportsToSP[selectedReport];
            var dateSelected = false;
            var inspectorSelected = false;
            var parmType;
            console.log(parms);
            $('#inspectorsDropdown').val("Please select an inspector");
            $('#datePickerSelectDate').val("");
            $('#datePickerBeginDate').val("");
            $('#datePickerEndDate').val("");

            if (oTable != null) {
                oTable.fnDestroy(); //have to be destory first, then rebuild
                $("#reportTable").empty(); //need to remove its dom elements, otherwise there will be problems rebuilding the table
            }

            if ($(this).val() == "Please make a selection") {
                parms = undefined;
                exp = undefined;
                sp = undefined;
                toggleVisible([goButton, exportButton], "none");
            }

            toggleVisible([beginDate, endDate, selectDate, goButton, exportButton, noSelectionError, noDateError, inspectorsDropdown, selectDateFirstError, parmDropdown], "none");

            if (parms) { //parm == 1
                console.log("parms has value!");
                console.log(parms);

                if (parms["ParmInspectorFlag"] === "1") { //parminspector == 1
                    if (parms["ParmDateFlag"] === "1") { //parm == 1, parminspector == 1, parmdate == 1
                        toggleVisible([selectDate], "block");
                        $('#datePickerSelectDate').on('change', function () {
                            if (oTable != null) {
                                oTable.fnDestroy(); //have to be destory first, then rebuild
                                $("#reportTable").empty(); //need to remove its dom elements, otherwise there will be problems rebuilding the table
                            }
                            if ($('#datePickerSelectDate').val() !== "") {
                                buildInspectorDropdown(null, $('#datePickerSelectDate').val(), sp, null, exp);
                            }
                            else {
                                $('#inspectorsDropdown').val("Please select an inspector");
                                toggleVisible([goButton, exportButton, inspectorsDropdown], "none");
                                while (inspectorsDropdown.lastChild && inspectorsDropdown.lastChild.innerHTML !== "Please select an inspector") {
                                    inspectorsDropdown.removeChild(inspectorsDropdown.lastChild);
                                }
                            }
                        });
                    }
                    else if (parms["ParmBetweenDateFlag"] === "1") { //parminspector == 1 and parmbetweendate == 1
                        console.log("Line453");
                        toggleVisible([beginDate, endDate], "block");
                        toggleVisible([beginDateView, endDateView], "none");
                        document.getElementById('inspectorsDropdown').style.display = "none";

                        $('#datePickerBeginDate').on('change', function () {
                            if (oTable != null) {
                                oTable.fnDestroy(); //have to be destory first, then rebuild
                                $("#reportTable").empty(); //need to remove its dom elements, otherwise there will be problems rebuilding the table
                            }
                            if ($('#datePickerBeginDate').val() !== "" && $('#datePickerEndDate').val() !== "") {
                                if ($('#datePickerBeginDate').datepicker("getDate") > $('#datePickerEndDate').datepicker("getDate")) {
                                    toggleVisible([goButton, exportButton, inspectorsDropdown], "none");
                                    $('#datePickerBeginDate').val("");
                                    $('#datePickerEndDate').val("");
                                    alert('Begin date cannot be greater than end date');
                                }
                                else {
                                    dateSelected = true;
                                    if (parms["ParmInspectorFlag"] === "1") {
                                        console.log("Line 471");
                                        buildInspectorDropdown($('#datePickerBeginDate').val(), $('#datePickerEndDate').val(), sp, parms["Parm"], exp);
                                    }
                                }
                            }
                            else {
                                $('#inspectorsDropdown').val("Please select an inspector");
                                toggleVisible([goButton, exportButton, inspectorsDropdown], "none");
                                while (inspectorsDropdown.lastChild && inspectorsDropdown.lastChild.innerHTML !== "Please select an inspector") {
                                    inspectorsDropdown.removeChild(inspectorsDropdown.lastChild);
                                }
                            }
                        });
                        $('#datePickerEndDate').on('change', function () {
                            if (oTable != null) {
                                oTable.fnDestroy(); //have to be destory first, then rebuild
                                $("#reportTable").empty(); //need to remove its dom elements, otherwise there will be problems rebuilding the table
                            }
                            if ($('#datePickerBeginDate').val() !== "" && $('#datePickerEndDate').val() !== "") {
                                if ($('#datePickerBeginDate').datepicker("getDate") > $('#datePickerEndDate').datepicker("getDate")) {
                                    toggleVisible([goButton, exportButton, inspectorsDropdown], "none");
                                    $('#datePickerBeginDate').val("");
                                    $('#datePickerEndDate').val("");
                                    alert('Begin date cannot be greater than end date');
                                }
                                else {
                                    dateSelected = true;
                                    if (parms["ParmInspectorFlag"] === "1") {
                                        buildInspectorDropdown($('#datePickerBeginDate').val(), $('#datePickerEndDate').val(), sp, parms["Parm"], exp);
                                    }
                                }
                            }
                            else {
                                $('#inspectorsDropdown').val("Please select an inspector");
                                toggleVisible([goButton, exportButton, inspectorsDropdown], "none");
                                while (inspectorsDropdown.lastChild && inspectorsDropdown.lastChild.innerHTML !== "Please select an inspector") {
                                    inspectorsDropdown.removeChild(inspectorsDropdown.lastChild);
                                }
                            }
                        });
                        $('#inspectorsDropdown').on('change', function () {
                            if (oTable != null) {
                                oTable.fnDestroy(); //have to be destory first, then rebuild
                                $("#reportTable").empty(); //need to remove its dom elements, otherwise there will be problems rebuilding the table
                            }
                            if (dateSelected) {
                                toggleVisible([selectDateFirstError], "none");
                                if ($('#inspectorsDropdown').val() !== "Please select an inspector") {
                                    inspectorSelected = true;
                                }
                                else {
                                    inspectorSelected = false;
                                }

                                if (inspectorSelected) {
                                    toggleVisible([goButton], "inline");
                                    if (exp !== undefined) {
                                        toggleVisible([exportButton], "inline");
                                    }
                                    parmType = "ParmBetweenDateFlag";
                                }
                                else {
                                    toggleVisible([goButton, exportButton], "none");
                                }
                            }
                            else {
                                toggleVisible([goButton, exportButton], "none");
                                toggleVisible([selectDateFirstError], "inline");
                                $('#inspectorsDropdown').val("Please select an inspector");
                            }
                        });
                    }
                    else { //parmdate != 1 and parmbetweendate != 1, parminspector == 1
                        toggleVisible([goButton], "inline");
                        if (exp !== undefined) {
                            toggleVisible([exportButton], "inline");
                        }
                    }
                } //end if parmsinspector == 1
                else if (parms["ParmDropDownFlag"] === "1") {
                    buildParmDropdown(sp, parms, exp);
                }
                else { //parminspector == NULL
                    if (parms["ParmDateFlag"] === "1") {
                        toggleVisible([selectDate], "block");
                        toggleVisible([beginDate, endDate], "none");
                        toggleVisible([beginDateView, endDateView], "none");
                        $('#datePickerSelectDate').on('change', function () {
                            if (oTable != null) {
                                oTable.fnDestroy(); //have to be destory first, then rebuild
                                $("#reportTable").empty(); //need to remove its dom elements, otherwise there will be problems rebuilding the table
                            }
                            if ($('#datePickerSelectDate').val() !== "") {
                                dateSelected = true;
                                toggleVisible([goButton], "inline");
                                if (exp !== undefined) {
                                    toggleVisible([exportButton], "inline");
                                }
                            }
                            else {
                                dateSelected = false;
                                toggleVisible([goButton, exportButton], "none");
                            }
                        });
                    }
                    else if (parms["ParmBetweenDateFlag"] === "1") {
                        console.log("call Viwe");
                        toggleVisible([beginDateView, endDateView], "block");

                        $('#datePickerBeginDateView').on('change', function () {
                            if (oTable != null) {
                                oTable.fnDestroy(); //have to be destory first, then rebuild
                                $("#reportTable").empty(); //need to remove its dom elements, otherwise there will be problems rebuilding the table
                            }
                            if ($('#datePickerBeginDateView').val() !== "" && $('#datePickerEndDateView').val() !== "") {
                                dateSelected = true;
                                toggleVisible([goButton], "inline");
                                if (exp !== undefined) {
                                    toggleVisible([exportButton], "inline");
                                }
                            }
                            else {
                                dateSelected = false;
                                toggleVisible([goButton, exportButton], "none");
                            }
                        });
                        $('#datePickerEndDateView').on('change', function () {
                            if (oTable != null) {
                                oTable.fnDestroy(); //have to be destory first, then rebuild
                                $("#reportTable").empty(); //need to remove its dom elements, otherwise there will be problems rebuilding the table
                            }
                            if ($('#datePickerBeginDateView').val() !== "" && $('#datePickerEndDateView').val() !== "") {
                                dateSelected = true;
                                toggleVisible([goButton], "inline");
                                if (exp !== undefined) {
                                    //toggleVisible([exportButton], "inline");
                                }
                            }
                            else {
                                dateSelected = false;
                                toggleVisible([goButton, exportButton], "none");
                            }
                        });
                    }
                    else { //parmdate != 1, parmbetweendate != 1, parminspector != 1
                        toggleVisible([goButton], "inline");
                        toggleVisible([beginDateView, endDateView], "none");
                        if (exp !== undefined) {
                            toggleVisible([exportButton], "inline");
                        }
                    }
                }
            }
            else { //parm != 1
                toggleVisible([goButton], "inline");
                if (exp !== undefined) {
                    toggleVisible([exportButton], "inline");
                }
            }
            //end reportsdropdown change

            //go button

            if ($(this).val() == "Please make a selection") {
                toggleVisible([goButton, exportButton], "none");
            }

        });
        $('#go').on('click', function () {
            toggleVisible([exportButton], "");
            if (isVisible(beginDate)) {
                if ($('#datePickerBeginDate').val() !== "" && $('#datePickerEndDate').val() !== "") {
                    dateSelected = true;
                }
                else {
                    dateSelected = false;
                }
            }
            else if (isVisible(selectDate)) {
                if ($('#datePickerSelectDate').val() !== "") {
                    dateSelected = true;
                }
                else {
                    dateSelected = false;
                }
            }
            else { //Parm != 1
                toggleVisible([noDateError, selectDateFirstError], "none");
                dateSelected = true;
            }

            if (dateSelected) {
                buildTable();
            }
        });

        /*export to data to file with user specified name*/
        $("#export").click(function (e) {
            ConvertToCSV(displayedResults.columns, displayedResults.data);
        });

        $('#viewReportButton').click(function () {
            $('#loading').show();
            $('#reportDisplay').load('/reports/view', function () {
                $('#reportDisplay').show();
                $('#loading').hide();
            });
        });
    }
});
