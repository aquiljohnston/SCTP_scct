<?php
/**
 * Created by PhpStorm.
 * User: tzhang
 * Date: 7/13/2017
 * Time: 2:38 PM
 */
$this->title = 'Map View';
?>
<!--
*todo: need to switch key depends on the environment
    Google Map Key For Server -> AIzaSyASmV1lt9mVXyX3lA4J74GNyoO2u3IiGvI
    Google Map Key For Local  -> AIzaSyBtnxA5IiHSgZ1REfHI_3Hb3zau4p0jnZ4
-->
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyASmV1lt9mVXyX3lA4J74GNyoO2u3IiGvI&libraries=places"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

    <!--Begin page authentication block. Prevents users from accessing unauthorized page through URL-->

    <script type="text/javascript">
        <?php error_reporting(E_ERROR | E_WARNING | E_PARSE); ?>
        var importedGrid = "<?php echo $_GET['mapgrid']; ?>";
    </script>
    <script type="text/javascript">
        var globalSearchBounds;
        var map, pb; //global map variable
        var grid;
        var gxml;
        var prevLat, prevLng, prevSrcDTLT;
        var bounds = new google.maps.LatLngBounds();
        var markers = []; //Holds all markers currently loaded
        var startYTD = '01/01/2016';

        //Breadcrumbs
        var breadcrumbMarkers = []; //Holds only markers for breadcrumbs
        var breadcrumbSlowMarkers = []; //Holds only markers for slow breadcrumbs
        var breadcrumbFastMarkers = []; //Holds only markers for fast breadcrumbs
        var playbackMarkers = [];

        //Assets
        var assetMarkers = []; //Holds only markers for assets
        var assetActiveMarkers = []; //Holds only markers for assets
        var assetCGIMarkers = []; //Holds only markers for assets
        var assetCompletedMarkers = []; //Holds only markers for assets

        //Pipeline
        var pipelineMarkers = [];
        var pipelineVerifiedMarkers = [];
        var pipelineNonVerifiedMarkers = [];

        var mapgridPolygons = [];
        var polyLines = [];

        //Leaks
        var leakMarkers = []; //Holds only markers for leaks
        var leakGrade1Markers = []; //Holds only markers for leaks
        var leakGrade2Markers = []; //Holds only markers for leaks
        var leakGrade3Markers = []; //Holds only markers for leaks

        var lastKnownLocationMarkers = []; //Holds only markers for user's last known locations
        var markerCluster; //Clusters for all markers currently displayed
        var assetsArray, breadcrumbsArray, leaksArray;
        var pipelineArray;
        var mapgridsArray;
        var loaded = false;
        var userColors = []; //Array to store who each color represents
        var infowindow = new google.maps.InfoWindow(); //Variable to store infowindow
        var mapCanvas;

        $(function () {
            // need to hide nav bar and light-blue bar in tracker map view
            $('.menu').css('display', 'none');
            $('.footerabove').css('display', 'none');

            // hide user info and logout button
            $('.logout').css('display', 'none');
            $('#UserInfo').css('display', 'none');

            var grid_pipeline = "";

            // Initialize map
            google.maps.event.addDomListener(window, 'load', initialize);

            //Initialize GoogleMap
            function initialize() {
                var mapCanvasData = document.getElementById('map-canvas');
                if (mapCanvasData != null) {
                    mapCanvas = mapCanvasData;
                }
                else {
                    mapCanvas = null;
                }
                var myLatLong = new google.maps.LatLng(34, -81);
                var mapOptions = {
                    center: myLatLong,
                    zoom: 7,
                    zoomControl: true,
                    zoomControlOptions: {
                        style: google.maps.ZoomControlStyle.LARGE,
                        position: google.maps.ControlPosition.LEFT_TOP
                    },
                    scaleControl: true,
                    streetViewControl: true,
                    streetViewControlOptions: {
                        position: google.maps.ControlPosition.LEFT_TOP
                    }
                }

                //Initialize progressBar
                pb = new progressBar();
                //map.controls[google.maps.ControlPosition.RIGHT].push(pb.getDiv()); //Progress bar

                //Initialize map
                map = new google.maps.Map(mapCanvas, mapOptions);

                google.maps.event.addDomListener(window, "resize", function () {
                    var center = map.getCenter();
                    google.maps.event.trigger(map, "resize");
                    map.setCenter(center);
                });


                // Create the search box and link it to the UI element.
                var input = (document.getElementById('pac-input'));
                map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

                var searchBox = new google.maps.places.SearchBox((input));

                // [START region_getplaces]
                // Listen for the event fired when the user selects an item from the
                // pick list. Retrieve the matching places for that item.
                google.maps.event.addListener(searchBox, 'places_changed', function () {
                    var places = searchBox.getPlaces();

                    if (places.length == 0)
                        return;

                    // For each place, get the icon, place name, and location.
                    var searchBounds = new google.maps.LatLngBounds();
                    for (var i = 0, place; place = places[i]; i++)
                        searchBounds.extend(place.geometry.location);

                    map.fitBounds(searchBounds);
                    globalSearchBounds = searchBounds;
                });
                // [END region_getplaces]

                // Bias the SearchBox results towards places that are within the bounds of the
                // current map's viewport.
                google.maps.event.addListener(map, 'bounds_changed', function () {
                    var searchBounds = map.getBounds();
                    searchBox.setBounds(searchBounds);
                });

                // If jumped from Dispatch...
                if (importedGrid) {
                    grid = importedGrid;
                    loadAllMapData();
                    console.log("LOAD ALL MAP CALLED");
                    //$('#mapGrid').text("Grid: " + grid);
                }
            }

            // Load all map data
            function loadAllMapData() {
                loaded = false;

                //Clear all markers
                for (var i = 0; i < breadcrumbMarkers.length; i++) {
                    breadcrumbMarkers[i].setMap(null);
                }
                for (var i = 0; i < assetMarkers.length; i++) {
                    assetMarkers[i].setMap(null);
                }
                for (var i = 0; i < leakMarkers.length; i++) {
                    leakMarkers[i].setMap(null);
                }
                for (var i = 0; i < lastKnownLocationMarkers.length; i++) {
                    lastKnownLocationMarkers[i].setMap(null);
                }
                for (var i = 0; i < pipelineMarkers.length; i++) {
                    pipelineMarkers[i].setMap(null);
                }
                for (var i = 0; i < mapgridPolygons.length; i++) {
                    mapgridPolygons[i].setMap(null);
                }
                for (var i = 0; i < markers.length; i++) {
                    markers[i].setMap(null);
                }
                for (var i = 0; i < polyLines.length; i++) {
                    polyLines[i].setMap(null);
                }

                //Empty all arrays
                markers = [];
                breadcrumbMarkers = [];
                assetMarkers = [];
                leakMarkers = [];
                pipelineMarkers = [];
                mapgridPolygons = [];
                lastKnownLocationMarkers = [];
                polyLines = [];

                //Clear markers
                if (markerCluster != null)
                    markerCluster.clearMarkers();

                //Hide progressBar
                //pb.hide();

                //Reload map
                google.maps.event.trigger(window, 'resize', {});

                //Reload data
                fetchData();

                //Check all checkboxes
                $('input[type=checkbox]').attr('checked', 'checked');
            }

            // Load all data into the map
            function loadData(dataArray, type) {
                // Chunk data & load
                var i, j, temparray, chunk = 100;
                for (i = 0, j = dataArray.length; i < j; i += chunk) {
                    temparray = dataArray.slice(i, i + chunk); //Create chunk of array
                    setTimeout(loadDataChunk(temparray, type, function () {
                        //pb.updateBar(100);
                        //if (pb.getCurrent() == pb.getTotal()) {

                            // Automatically center the map fitting all markers on the screen
                            // map.fitBounds(bounds);

                            //markers = breadcrumbMarkers.concat(assetMarkers, leakMarkers);
                            markers = assetMarkers;

                            //Create clusters
                            if (markerCluster == null)
                                markerCluster = new MarkerClusterer(map, assetMarkers, /*{ ignoreHidden: true }*/{gridSize: 50, maxZoom: 15, imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m'});
                            else
                                markerCluster.addMarkers(assetMarkers);

                            //hideDefaults();
                        //}
                    }), 5); //Load chunk of data, update progressBar
                }
            }

            //Loads a chunk of data into the map
            function loadDataChunk(data, type, callback) {
                setTimeout(function () {
                    switch (type) {
                        case 'breadcrumbs':
                            $.each(data, function (i, obj) {
                                createBreadcrumbMarker(i, obj);
                            });
                            break;
                        case 'assets':
                            $.each(data, function (i, obj) {
                                createAssetMarker(i, obj);
                            });
                            break;
                        case 'leaks':
                            $.each(data, function (i, obj) {
                                createLeakMarker(i, obj);
                            });
                            break;
                        case 'pipeline':
                            $.each(data, function (i, obj) {
                                createPipelineMarker(i, obj);
                            });
                            break;
                    }
                    callback();
                }, 0);
            }


            //Create last known location marker on the map
            function createLastKnownLocationMarker(i, obj) {

                //Lat-Lng of breadcrumb point
                var latlong = new google.maps.LatLng(obj.Latitude, obj.Longitude);

                //Create image to use as marker
                var pinImage = new google.maps.MarkerImage("http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=L|FFFFFF|000000",
                    new google.maps.Size(21, 34),
                    new google.maps.Point(0, 0),
                    new google.maps.Point(10, 34));

                //Create marker
                var marker = new google.maps.Marker({
                    icon: pinImage,
                    position: latlong,
                    map: map
                });

                //Add to markers array
                lastKnownLocationMarkers.push(marker);

                //HTML for window popup
                var contentString = '<div id="lastKnownLocation">' +
                    '<p><b>BreadcrumbUID: </b>' + obj.BreadcrumbUID + '</p>' +
                    '<p></p>' +
                    '<p><b>SrcDTLT: </b>' + obj.SrcDTLT + '</p>' +

                    '<p><b>CreatedUserID: </b>' + obj.CreatedUserID + '</p>' +
                    '<p><b>Latitude: </b>' + obj.Latitude + '</p>' +
                    '<p><b>Longitude: </b>' + obj.Longitude + '</p>' +
                    '</div>';

                //Add click listener to marker
                google.maps.event.addListener(marker, 'click', function () {
                    infowindow.setContent(contentString);
                    infowindow.open(map, marker);
                });
            }

            //Create pipeline marker on the map
            function createPipelineMarker(i, obj, callback) {

                //Lat-Lng of breadcrumb point
                var latlong = new google.maps.LatLng(obj.Latitude, obj.Longitude);

                //Create image to use as marker
                if (obj.Verified === "0")
                    var pinImage = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAkAAAAJCAYAAADgkQYQAAAAiklEQVR42mNgQIAoIF4NxGegdCCSHAMzEC+NUlH5v9rF5f+ZoCAwHaig8B8oPhOmKC1NU/P//7Q0DByrqgpSGAtSdOCAry9WRXt9fECK9oIUPXwYFYVV0e2ICJCi20SbFAuyG5uiECUlkKIQmOPng3y30d0d7Lt1bm4w301jQAOgcNoIDad1yOEEAFm9fSv/VqtJAAAAAElFTkSuQmCC";
                else
                    var pinImage = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAkAAAAJCAYAAADgkQYQAAAAiElEQVR42mNgQIAoIF4NxGegdCCSHAMzEC81izL7n746/X/VmSowbRho+B8oPhOmKM02zfb/TCzQItYCpDAWpOhA8YFirIoK9xaCFO0FKXrY/rAdq6Lm280gRbeJNikWZDc2RUYhRiBFITDHzwf5LmtjFth3GesyYL6bxoAGQOG0ERpO65DDCQDX7ovT++K9KQAAAABJRU5ErkJggg==";

                //Create marker
                var marker = new google.maps.Marker({
                    icon: pinImage,
                    position: latlong,
                    customInfo: obj.CreatedUserID,
                    map: map
                });

                // Add markers to appropriate array for filtering
                if (obj.Verified === "0")
                    pipelineNonVerifiedMarkers.push(marker);
                else
                    pipelineVerifiedMarkers.push(marker);


                //Add to markers array
                pipelineMarkers.push(marker);

                //HTML for window popup
                var contentString = '<div id="pipeline">' +
                    '<p><b>Latitude: </b>' + obj.Latitude + '</p>' +
                    '<p><b>Longitude: </b>' + obj.Longitude + '</p>' +
                    '<p><b>Verifying BC ID: </b>' + obj.BC_ID + '</p>' +
                    '<p><b>Verifying BC Distance: </b>' + obj.BC_Distance + '</p>' +
                    '</div>';

                //Add click listener to marker
                google.maps.event.addListener(marker, 'click', function () {
                    infowindow.setContent(contentString);
                    infowindow.open(map, marker);
                });
            }

            //Create asset marker on the map
            function createBreadcrumbMarker(i, obj) {
                //alert("inside createBreadcrumbMarker function");
                //Lat-Lng of breadcrumb point
                var latlong = new google.maps.LatLng(obj.Latitude, obj.Longitude);

                //Extend the initial bounds to include this point
                bounds.extend(latlong);

                //Create image to use as marker
                var icon = "";
                if (Number(obj.CalcSpeed) <= 4)
                    icon = "https://maps.gstatic.com/intl/en_us/mapfiles/markers2/measle_blue.png";
                else
                    icon = "https://storage.googleapis.com/support-kms-prod/SNP_2752264_en_v0";

                var pinImage = new google.maps.MarkerImage(icon,
                    new google.maps.Size(21, 34),
                    new google.maps.Point(0, 0),
                    new google.maps.Point(10, 34));

                //Create marker
                var marker = new google.maps.Marker({
                    icon: pinImage,
                    position: latlong,
                    map: map
                });

                //Create image to use as marker
                if (Number(obj.CalcSpeed) <= 4)
                    breadcrumbSlowMarkers.push(marker);
                else
                    breadcrumbFastMarkers.push(marker);

                //Add to markers array
                breadcrumbMarkers.push(marker);

                var contentString = '<div id="breadcrumb">' +
                    '<p><b>ID: </b>' + obj.tBreadcrumbsWithGarminDataID + '</p>' +
                    '<p><b>SrcDTLT: </b>' + obj.SrcDTLT + '</p>' +
                    '<p><b>CreatedUserID: </b>' + obj.CreatedUserID + '</p>' +
                    '<p><b>Source: </b>' + obj.SourceName + '</p>' +
                    '<p><b>Latitude: </b>' + obj.Latitude + '</p>' +
                    '<p><b>Longitude: </b>' + obj.Longitude + '</p>' +
                    '<p><b>Calculated Speed: </b>' + obj.CalcSpeed + '</p>' +
                    '<p><b>Activity Type: </b>' + obj.ActivityType + '</p>' +
                    '</div>';

                //Add click listener to marker
                google.maps.event.addListener(marker, 'click', function () {
                    infowindow.setContent(contentString);
                    infowindow.open(map, marker);
                });


                //////////////////  PLAYBACK? ////////////
                // Define a symbol using SVG path notation, with an opacity of 1.
                var lineSymbol = {
                    path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW
                };

                var lineCoordinates = [
                    new google.maps.LatLng(obj.Latitude, obj.Longitude),
                    new google.maps.LatLng(prevLat, prevLng)
                ];

                prevLat = obj.Latitude;
                prevLng = obj.Longitude;

            }

            //Create asset marker on the map
            function createAssetMarker(i, obj) {

                //Lat-Lng of breadcrumb point
                var latlong = new google.maps.LatLng(obj.Latitude, obj.Longitude);

                //Grab color to use for marker
                var pinColor = userColors[obj.ModifiedUserID]; //Grab color for user

                //Create image to use as marker
                var icon = "";
                switch (obj.AssetType) {
                    case 'Service Location':
                        icon = "http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=|FF0000|000000"
                        break;
                    case 'CGE/CNL':
                        icon = "http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=|FFFF00|000000"
                        break;
                    case 'Completed':
                        icon = "http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=|33FF00|000000"
                        break;
                    default:
                        icon = "http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=|FF0000|000000"
                        //icon = "http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=|FFFFFF|000000"
                        break;
                }

                var pinImage = new google.maps.MarkerImage(icon,
                    new google.maps.Size(21, 34),
                    new google.maps.Point(0, 0),
                    new google.maps.Point(10, 34));

                //Create marker
                var marker = new google.maps.Marker({
                    icon: pinImage,
                    position: latlong,
                    map: map
                });

                //Create image to use as marker
                switch (obj.AssetType) {
                    case 'Active':
                        assetActiveMarkers.push(marker);
                        break;
                    case 'CGE/CNL':
                        assetCGIMarkers.push(marker);
                        break;
                    case 'Completed':
                        assetCompletedMarkers.push(marker);
                        break;
                    default:
                        break;
                }

                //Add to markers array
                //assetMarkers = [];
                //assetMarkers.push(marker);

                //HTML for window popup
                var contentString = '<div id="asset">' +
                    /*'<p><b>AssetUID: </b>' + obj.AssetUID + '</p>' +
                    '<p></p>' +
                    '<p><b>ClientID: </b>' + obj.ClientID + '</p>' +
                    '<p><b>HouseNumber: </b>' + obj.HouseNumber + '</p>' +
                    '<p><b>Street: </b>' + obj.Street1 + '</p>' +
                    '<p><b>City: </b>' + obj.City + '</p>' +
                    '<p><b>State: </b>' + obj.State + '</p>' +
                    '<p><b>ZIP: </b>' + obj.ZIP + '</p>' +
                    '<p><b>StatusType: </b>' + obj.StatusType + '</p>' +
                    '<p><b>Latitude: </b>' + obj.Latitude + '</p>' +
                    '<p><b>Longitude: </b>' + obj.Longitude + '</p>' +
                    '</div>';*/
                    '<p><b>ID: </b>' + obj.ID + '</p>' +
                    '<p></p>' +
                    '<p><b>ClientWorkOrderID: </b>' + obj.ClientWorkOrderID + '</p>' +
                    '<p><b>AssetType: </b>' + obj.AssetType + '</p>' +
                    '<p><b>Address: </b>' + obj.Address + '</p>' +
                    '<p><b>MapGrid: </b>' + obj.MapGrid + '</p>' +
                    '<p><b>Distance: </b>' + obj.Distance + '</p>' +
                    //'<p><b>Verified: </b>' + obj.Verified + '</p>' +
                    '</div>';

                //Add click listener to marker
                /*google.maps.event.addListener(marker, 'click', function () {
                    infowindow.setContent(contentString);
                    infowindow.open(map, marker);
                });*/
                google.maps.event.addListener(marker, 'click', (function(marker, i) {
                    return function() {
                        infowindow.setContent(contentString);
                        infowindow.open(map, marker);
                    }
                })(marker, i));
                assetMarkers.push(marker);
            }

            //Create asset marker on the map
            function createLeakMarker(i, obj) {

                //Lat-Lng of breadcrumb point
                var latlong = new google.maps.LatLng(obj.Latitude, obj.Longitude);

                //Extend the initial bounds to include this point
                //bounds.extend(latlong);

                //Create image to use as marker
                var icon = "";
                switch (obj.LeakGradeType) {
                    case 'Grade 1':
                        icon = "http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=1|0000FF|FFFFFF"
                        break;
                    case 'Grade 2':
                        icon = "http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=2|0000FF|FFFFFF"
                        break;
                    case 'Grade 3':
                        icon = "http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=3|0000FF|FFFFFF"
                        break;
                    default:
                        icon = "http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=|0000FF|FFFFFF"
                        break;
                }

                var pinImage = new google.maps.MarkerImage(icon,
                    new google.maps.Size(21, 34),
                    new google.maps.Point(0, 0),
                    new google.maps.Point(10, 34));

                //Create marker
                var marker = new google.maps.Marker({
                    icon: pinImage,
                    position: latlong,
                    map: map
                });

                switch (obj.LeakGradeType) {
                    case 'Grade 1':
                        leakGrade1Markers.push(marker);
                    case 'Grade 2':
                        leakGrade2Markers.push(marker);
                        break;
                    case 'Grade 3':
                        leakGrade3Markers.push(marker);
                        break;
                    default:
                        break;
                }


                //Add to markers array
                leakMarkers.push(marker);

                //HTML for window popup
                var contentString = '<div id="leak">' +
                    '<p><b>AssetUID: </b>' + obj.AssetUID + '</p>' +
                    '<p></p>' +
                    '<p><b>ClientID: </b>' + obj.ClientID + '</p>' +
                    '<p><b>LeakLocationType: </b>' + obj.LeakLocationType + '</p>' +
                    '<p><b>LeakFoundType: </b>' + obj.LeakGradeType + '</p>' +
                    '<p><b>Latitude: </b>' + obj.Latitude + '</p>' +
                    '<p><b>Longitude: </b>' + obj.Longitude + '</p>' +
                    '</div>';

                //Add click listener to marker
                google.maps.event.addListener(marker, 'click', function () {
                    infowindow.setContent(contentString);
                    infowindow.open(map, marker);
                });
            }

            function fetchData() {

                //Show busy screen
                $('#ajax-busy').show();

                //Get all data
                getData();

                // Executes when all ajax are finished
                $(document).ajaxStop(function () {

                    if (!loaded) {

                        //Set loaded flag to true
                        loaded = true;

                        //Hide busy screen
                        $('#ajax-busy').hide();

                        // Start progressBar
                        //pb.start(pipelineArray.length);

                        // Load breadcrumb data onto map
                        //loadData(breadcrumbsArray, 'breadcrumbs');

                        // Load assets data onto map
                        loadData(assetsArray, 'assets');

                        // Load leaks data onto map
                        //loadData(leaksArray, 'leaks');

                        // Load pipeline data onto map
                        //loadData(pipelineArray, 'pipeline');

                        // Load map grid onto map
                        //loadMapGrid(mapgridsArray);

                    }

                });

                // Get last known locations from database
                function getLastKnownLocations() {
                    $.ajax({
                        type: "POST",
                        datatype: "JSON",
                        url: "script/explorer/get_lastKnownLocations.php",
                        success: function (results) {
                            var data = JSON.parse(results);

                            // Load data
                            $.each(data.LastKnownLocations, function (i, obj) {
                                createLastKnownLocationMarker(i, obj);
                            });
                        }
                    });
                }


                // Get leaks from database
                function loadMapGrid(points) {

                    // Define the LatLng coordinates for the polygon's path.
                    var arr = new google.maps.MVCArray();
                    $.each(points, function (i, obj) {
                        console.log("lat is :　"+obj.Latitude+" lon is : "+obj.Longitude);
                        arr.push(new google.maps.LatLng(Number(obj.Latitude), Number(obj.Longitude)));
                    });

                    // Construct the polygon.
                    var mapGrid = new google.maps.Polygon({
                        paths: arr,
                        strokeColor: '#000000',
                        strokeOpacity: 0.8,
                        strokeWeight: 2,
                        //fillColor: '#FF0000',
                        fillOpacity: 0
                    });
                    mapgridPolygons.push(mapGrid);
                    mapGrid.setMap(map);

                    // Zoom to map grid
                    var latlngbounds = new google.maps.LatLngBounds();
                    for (var i = 0; i < arr.length; i++) {
                        latlngbounds.extend(arr.getAt(i));
                    }

                    if (arr.length > 0)
                        map.fitBounds(latlngbounds);
                    else
                        map.fitBounds(bounds);
                }

                // Get pipeline from database
                function getData() {
                    $.ajax({
                        type: "GET",
                        //datatype: "JSON",
                        data: ({
                            mapGrid: grid,
                            /*start: (importedGrid) ? startYTD : $("#start").val(),
                            end: (importedGrid) ? getTodaysDate() : $("#end").val(),
                            activityType: $("#activityTypeDropdown").val(),
                            surveyType: $("#surveyTypeDropdown").val()*/
                        }),
                        url: '/tracker/get-map-data',
                        //url: "script/explorer/get_data.php",
                        success: function (results) {
                            console.log("MAP DATA: "+results);
                            var data = JSON.parse(results);
                            //pipelineArray = data.pipeline;
                            assetsArray = data.assets;
                            /*leaksArray = data.leaks;
                            breadcrumbsArray = data.breadcrumbs;
                            mapgridsArray = data.mapgrids;*/
                        }
                    });
                }
            }

            function getTodaysDate() {
                var today = new Date();
                var dd = today.getDate();
                var mm = today.getMonth() + 1;
                var yyyy = today.getFullYear();

                if (dd < 10) {
                    dd = '0' + dd;
                }
                if (mm < 10) {
                    mm = '0' + mm
                }
                today = mm + '/' + dd + '/' + yyyy;
                return today;
            }
        });
    </script>

        <div id="map-container">
            <div id="map-canvas" ></div>
        </div>
