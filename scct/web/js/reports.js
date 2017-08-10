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
            selectDate = document.getElementById('selectDate'),
            exportButton = document.getElementById('export'),
            goButton = document.getElementById('go'),
            noSelectionError = document.getElementById('noSelectionError'),
            noDateError = document.getElementById('noDateError'),
            selectDateFirstError = document.getElementById('selectDateFirstError');

        $('#datePickerEndDate').datepicker();
        $("#datePickerBeginDate").datepicker({
            changeMonth: true,
            onSelect: function(date){
                var selectedDate = new Date(date);
                var msecsInADay = 86400000;
                var endDate = new Date(selectedDate.getTime() + msecsInADay);
                var maxDate = new Date(selectedDate.getTime() + 13*msecsInADay);
                var currentDate = new Date();
                maxDate = maxDate >= currentDate ? currentDate: maxDate;
                endDate = selectedDate.toDateString() == currentDate.toDateString() ? date: endDate;
                var currentEndDate = $('#datePickerEndDate').datepicker("getDate");
                if (selectedDate > currentEndDate && $('#datePickerEndDate').datepicker("getDate") != null){
                    toggleVisible([goButton, exportButton, parmDropdown], "none");
                    $('#datePickerBeginDate').val("");
                    $('#datePickerEndDate').val("");
                    $('#inspectorListHeader').css('display', 'none');
                    $('#mapGridListHeader').css('display', 'none');
                    alert('Begin date cannot be greater than end date');
                }

                $("#datePickerEndDate").datepicker( "option", { minDate: new Date(endDate), maxDate: new Date(maxDate), beforeShowDay: $.datepicker.noWeekends, setDate: new Date(endDate)} );
            }
        });
        $('#datePickerSelectDate').datepicker();

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
                    ViewName: sp
                },
                beforeSend: function () {
                    $('#ajax-busy').show();
                },
                success: function (data) {
                    while (parmDropdown.lastChild && parmDropdown.lastChild.innerHTML !== "Please make a selection") {
                        parmDropdown.removeChild(parmDropdown.lastChild);
                    }
                    $('#ajax-busy').hide();
                    toggleVisible([goButton], "inline");
                    $('#mapGridListHeader').css('display', 'inline');
                    toggleVisible([parmDropdown], "inline");

                    //added default option to inspector dropdown
                    var firstOption = document.createElement("option");
                    firstOption.innerHTML = "All";
                    firstOption.value = null;
                    parmDropdown.appendChild(firstOption);

                    var results = JSON.parse(data);
                    $.each(results.options, function (i, obj) {
                        var option = document.createElement("option");
                        option.value = option.innerHTML = obj['mapgrid'];
                        parmDropdown.appendChild(option);
                    });
                    $('#parmDropdown').on('change', function () {
                        if (oTable != null) {
                            oTable.fnDestroy(); //have to be destory first, then rebuild
                            $("#reportTable").empty(); //need to remove its dom elements, otherwise there will be problems rebuilding the table
                        }
                        if ($('#parmDropdown').val() !== "Please select an inspector") {
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

        function buildInspectorDropdown(beginDate, endDate, sp, parm, exports) {

            $.ajax({
                type: "POST",
                url: "reports/get-inspector-drop-down",
                data: {
                    /*ReportName: sp,
                    BeginDate: beginDate || null,
                    EndDate: endDate || null,*/
                    Parm: parm || null
                },
                beforeSend: function () {
                    $('#ajax-busy').show();
                },
                success: function (data) {
                    $('#ajax-busy').hide();
                    var results = JSON.parse(data);
                    toggleVisible([inspectorsDropdown], "block");
                    toggleVisible([goButton], "inline");
                    $('#inspectorListHeader').css('display', 'inline');

                    var inspectors = []; //userid lastname firstname

                    //clear existing dropdown
                    while (inspectorsDropdown.lastChild && inspectorsDropdown.lastChild.innerHTML !== "Please select an inspector") {
                        inspectorsDropdown.removeChild(inspectorsDropdown.lastChild);
                    }

                    //build dropdown
                    //added default option to inspector dropdown
                    var firstOption = document.createElement("option");
                    firstOption.innerHTML = "All";
                    firstOption.value = null;
                    inspectorsDropdown.appendChild(firstOption);

                    $.each(results.inspectors, function (i, obj) {
                        //console.log(obj);
                        var option = document.createElement("option");
                        option.innerHTML = obj['displayNameData'];
                        option.value = obj['userNameData'];
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
                starVal = null;
                endVal = null;
            }
            var parmDateOverride = isVisible(parmDropdown) ? $('#parmDropdown').val() : null;
            var userLogin = isVisible(inspectorsDropdown) ? $('#inspectorsDropdown').val() : null;

            var parmDateOverrideCheck = parmDateOverride != null ? 1 : 0;
            var userLoginCheck = userLoginCheck != null ? 1 : 0;

            var parmVar = parmDateOverrideCheck > userLoginCheck ? parmDateOverride : userLogin;
            var ParmInspector = $('#inspectorsDropdown').val();

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
                    ParmInspector: ParmInspector,
                    ReportType: parameters[7],
                },
                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (data) {
                    $('#loading').hide();
                    $('#go').prop('disabled', false);
                    var results = JSON.parse(data);
                    //console.log(results.data);
                    //console.log(parameters);
                    //console.log(results.data.length);
                    if (oTable != null) {
                        oTable.fnDestroy(); //have to be destory first, then rebuild
                        $("#reportTable").empty(); //need to remove its dom elements, otherwise there will be problems rebuilding the table
                    }

                    if (results.data.length > 0) {
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
                    }

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

                    /*adjust the table size based on the selected report*/
                    if (parameters[2] == 1){
                        $('#reportTable_wrapper .dataTables_scrollBody').attr('style', 'max-height: 42vh !important; overflow-y: auto');
                    }else{
                        $('#reportTable_wrapper .dataTables_scrollBody').attr('style', 'max-height: 54vh !important; overflow-y: auto');
                    }
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

                    line += (array[i][index] !== null) ? array[i][index] : ''; // Append the cell data
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
            saveAs(blob, "Report_" + today + ".csv");
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
                    /*option.value = obj["ReportSPName"];*/
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
                    parms["isMapGridDropDownRequired"] = obj["ParmDropDownFlag"];
                    reportsToParms[obj["ReportDisplayName"]] = parms;
                    if (obj["ExportFlag"] === "1") {
                        reportsToExports[obj["ReportDisplayName"]] = obj["ExportFlag"];
                    }
                    reportsToSP[obj["ReportDisplayName"]] = obj["ReportSPName"];
                });
            }
        });

        $('#reportsDropdown').on('change', function () {
            $('#go').prop('disabled', false);
            var selectedReport = $(this).val();
            var parms = reportsToParms[selectedReport];
            var exp = reportsToExports[selectedReport];
            var sp = reportsToSP[selectedReport];
            var dateSelected = false;
            var inspectorSelected = false;
            var parmDropdownSelected = false;
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

            $('#inspectorListHeader').css('display', 'none');
            $('#mapGridListHeader').css('display', 'none');
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
                        document.getElementById('inspectorsDropdown').style.display = "none";

                        $(document).off('change', '#datePickerBeginDate').on('change', '#datePickerBeginDate', function () {
                            console.log("Begin Date changed under inspector drop down");
                            if (oTable != null) {
                                oTable.fnDestroy(); //have to be destory first, then rebuild
                                $("#reportTable").empty(); //need to remove its dom elements, otherwise there will be problems rebuilding the table
                            }
                            if ($('#datePickerBeginDate').val() !== "" && $('#datePickerEndDate').val() !== "") {
                                    dateSelected = true;
                                    if (parms["ParmInspectorFlag"] === "1") {
                                        console.log("Line 471");
                                        buildInspectorDropdown($('#datePickerBeginDate').val(), $('#datePickerEndDate').val(), sp, parms["Parm"], exp);
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
                        $(document).off('change', '#datePickerEndDate').on('change', '#datePickerEndDate', function () {
                            if (oTable != null) {
                                oTable.fnDestroy(); //have to be destory first, then rebuild
                                $("#reportTable").empty(); //need to remove its dom elements, otherwise there will be problems rebuilding the table
                            }
                            if ($('#datePickerBeginDate').val() !== "" && $('#datePickerEndDate').val() !== "") {
                                dateSelected = true;
                                toggleVisible([parmDropdown], "none");
                                $('#mapGridListHeader').css("display", "none");
                                if (parms["ParmInspectorFlag"] === "1") {
                                    buildInspectorDropdown($('#datePickerBeginDate').val(), $('#datePickerEndDate').val(), sp, parms["Parm"], exp);
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
                    if (parms["ParmBetweenDateFlag"] === "1") { //parminspector == 1 and parmbetweendate == 1
                        console.log("Line453");
                        toggleVisible([beginDate, endDate], "block");
                        document.getElementById('parmDropdown').style.display = "none";

                        $(document).off('change', '#datePickerBeginDate').on('change', '#datePickerBeginDate', function () {
                            console.log("Begin Date changed under parm drop down");
                            if (oTable != null) {
                                oTable.fnDestroy(); //have to be destory first, then rebuild
                                $("#reportTable").empty(); //need to remove its dom elements, otherwise there will be problems rebuilding the table
                            }
                            if ($('#datePickerBeginDate').val() !== "" && $('#datePickerEndDate').val() !== "") {
                                dateSelected = true;
                                toggleVisible([parmDropdown], "none");
                                $('#inspectorListHeader').css("display", "none");
                                if (parms["isMapGridDropDownRequired"] == 1) {
                                    var sp = "vMapGrid";
                                    buildParmDropdown(sp, parms, exp);
                                }
                            }
                            else {
                                toggleVisible([goButton, exportButton, parmDropdown], "none");
                                while (parmDropdown.lastChild && parmDropdown.lastChild.innerHTML !== "Please select a Map Grid") {
                                    parmDropdown.removeChild(parmDropdown.lastChild);
                                }
                            }
                        });
                        $(document).off('change', '#datePickerEndDate').on('change', '#datePickerEndDate', function () {
                            if (oTable != null) {
                                oTable.fnDestroy(); //have to be destory first, then rebuild
                                $("#reportTable").empty(); //need to remove its dom elements, otherwise there will be problems rebuilding the table
                            }
                            if ($('#datePickerBeginDate').val() !== "" && $('#datePickerEndDate').val() !== "") {
                                dateSelected = true;
                                if (parms["isMapGridDropDownRequired"] == 1) {
                                    var sp = "vMapGrid";
                                    buildParmDropdown(sp, parms, exp);
                                }
                            }
                            else {
                                toggleVisible([goButton, exportButton, parmDropdown], "none");
                                while (parmDropdown.lastChild && parmDropdown.lastChild.innerHTML !== "Please select a Map Grid") {
                                    parmDropdown.removeChild(parmDropdown.lastChild);
                                }
                            }
                        });
                        $('#parmDropdown').on('change', function () {
                            console.log("triggered parm drop down");
                            if (oTable != null) {
                                oTable.fnDestroy(); //have to be destory first, then rebuild
                                $("#reportTable").empty(); //need to remove its dom elements, otherwise there will be problems rebuilding the table
                            }
                            if (dateSelected) {
                                toggleVisible([selectDateFirstError], "none");
                                if ($('#parmDropdown').val() !== "Please select a Map Grid") {
                                    parmDropdownSelected = true;
                                }
                                else {
                                    parmDropdownSelected = false;
                                }

                                if (parmDropdownSelected) {
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
                                $('#parmDropdown').val("Please select a Map Grid");
                            }
                        });
                    }
                }
                else { //parminspector == NULL
                    if (parms["ParmDateFlag"] === "1") {
                        toggleVisible([selectDate], "block");
                        toggleVisible([beginDate, endDate], "none");
                        //toggleVisible([beginDateView, endDateView], "none");
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
                        toggleVisible([beginDate, endDate], "block");

                        $(document).off('change', '#datePickerBeginDate').on('change', '#datePickerBeginDate', function () {
                            if (oTable != null) {
                                oTable.fnDestroy(); //have to be destory first, then rebuild
                                $("#reportTable").empty(); //need to remove its dom elements, otherwise there will be problems rebuilding the table
                            }
                            if ($('#datePickerBeginDate').val() !== "" && $('#datePickerEndDate').val() !== "") {
                                dateSelected = true;
                                toggleVisible([goButton], "inline");
                                $('#go').prop('disabled', false);
                                if (exp !== undefined) {
                                    toggleVisible([exportButton], "inline");
                                }
                            }
                            else {
                                dateSelected = false;
                                toggleVisible([goButton, exportButton], "none");
                            }
                        });
                        $(document).off('change', '#datePickerEndDate').on('change', '#datePickerEndDate', function () {
                            if (oTable != null) {
                                oTable.fnDestroy(); //have to be destory first, then rebuild
                                $("#reportTable").empty(); //need to remove its dom elements, otherwise there will be problems rebuilding the table
                            }
                            if ($('#datePickerBeginDate').val() !== "" && $('#datePickerEndDate').val() !== "") {
                                dateSelected = true;
                                $('#go').prop('disabled', false);
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
                        toggleVisible([beginDate, endDate], "none");
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
            $(this).prop('disabled', true);
        });

        console.log("The export listener is called below this line");
        /*export to data to file with user specified name*/
        $("#export").click(function (e) {
            console.log("Export clicked!");
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
