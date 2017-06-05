$(document).ready(function () {

    $('#loading').hide();
    var head = $("<a href='/home/index'><img src='/logo/sc_logo.png' alt='' height='50' width='300' ></a>");
    $(".logo").prepend(head);

    var toggleButton = "<div class='navbar-default navbar-header'>"
        + "<button type='button' class='navbar-toggle collapsed' data-toggle='collapse' data-target='#navbar' aria-expanded='false' aria-controls='navbar'>"
        + "<span class='sr-only'>Navigation</span>"
        + "<span class='icon-bar'></span>"
        + "<span class='icon-bar'></span>"
        + "<span class='icon-bar'></span>"
        + "<?php ?>"
        + "</button>"
        + "</div>";

    // default head setting
    var head = $(toggleButton + "<div id='navbar' class='navbar-collapse collapse'>"
        + "<ul class='nav navbar-nav' id='nav'></ul>"
        + "</div><div class='clear'></div>");
    $(".menu").prepend(head);


    // middle privilege (less than admin more than technician) head setting
    var MiddlePrivilegeHead = $(toggleButton + "<div id='navbar' class='navbar-collapse collapse'>"
        + "<ul class='nav navbar-nav' id='middlePrivilegeNav'></ul>"
        + "</div><div class='clear'></div>");
    $(".middlePrivilegeMenu").prepend(MiddlePrivilegeHead);

    // admin header setting
    var adminHead = $(toggleButton + "<div id='navbar' class='navbar-collapse collapse'>"
        + "<ul class='nav navbar-nav' id='adminNav'></ul>"
        + "</div><div class='clear'></div>");
    $(".adminMenu").prepend(adminHead);

    //set login logo link
    var sc_logout_logo = $("<a href='/'><img src='/logo/sc_logo.png' alt='' height='50' width='300' ></a>");
    $(".sc_logout_logo").prepend(sc_logout_logo);

    //login header setting
    var login_head = $(toggleButton + "<div id='navbar' class='navbar-collapse collapse'>"
        + "<ul class='nav navbar-nav' id='nonav'></ul>");
    $(".loginMenu").prepend(login_head);

//Ajax call to retrieve all the projects for the project drop-down selection on the main menu
    /*var nav7 = $("<li class='dropdown'><a href='' class='dropdown-toggle' data-toggle='dropdown' role='button' aria-expanded='false'>"
     + "PROJECTS<b class='caret'></b></a>"
     + "    <ul href='#' id='projects_dropdown' class='dropdown-menu' role='menu'>"
     + "</ul></li>");*/

    // gather userID based on user role type
    var adminID = $(".adminMenu").attr("id");
    var middlePrivilegeID = $(".middlePrivilegeMenu").attr("id");
    var defaultID = $(".menu").attr("id");
    var userRoleID = -1;

    if (adminID != null || middlePrivilegeID != null || defaultID != null) {

        var AdminDropdown, DispatchDropdown, HomeDropdown;
        var PreFixUrl = window.location.hostname;

        // get prefix of current project
        PreFixUrl = PreFixUrl.split(".");
        PreFixUrl = PreFixUrl[0];
        if (PreFixUrl === "localhost") {
            PreFixUrl = "scct"; // for localhost
        }

        if (adminID != null) {
            userRoleID = adminID;
        } else if (middlePrivilegeID != null) {
            userRoleID = middlePrivilegeID;
        } else {
            userRoleID = defaultID;
        }

        //Build Table-Driven Navigation Menu
        if (localStorage.getItem('scct-navbar-saved') != "true") {
            $("#nav").addClass("blankNavBar");
            $.ajax({
                type: "GET",
                url: "/base/get-nav-menu",
                dataType: "json",
                data: {id: PreFixUrl},
                beforeSend: function () {
                    $('#loading').show();
                },
                complete: function () {
                    $('#loading').hide();
                },
                success: function (data) {
                    $('#loading').hide();
                    //data = $.parseJSON(data.navMenu);
                    //console.log(JSON.stringify(data, null, 2));
                    NavBar(data);
                }
            });
        } else {
            if (localStorage.getItem('navbar-blank') == "true") {
                $("#nav").addClass("blankNavBar");
            } else {
                $("#nav").prepend(localStorage.getItem('scct-navbar-data'));
            }

        }

        function NavBar(data) {
            var str = "";
            var SubNavigationStr = "";
            var DispatchDropdown = "";
            var HomeDropdown = "";
            var AdminDropdown = "";
            var dropdownFlag = 0;
            var baseUrl = "/";
            var HomeDropdownStr = "";
            if (jQuery.isEmptyObject(data)) {
                str = "Json array is empty";
            } else {
                // check which module is enabled
                if (data.Modules[0].CometTracker.enabled.toString() != 0) {
                    CometTrackerArray = data.Modules[0].CometTracker.NavigationMenu[0];

                    // clean SubNavigationStr
                    SubNavigationStr = "";

                    // get SubNavigationArray  and length of the SubNavigation menu
                    CometTrackerSubNavigationLength = CometTrackerArray.SubNavigation.length;
                    CometTrackerSubNavigationArray = CometTrackerArray.SubNavigation;

                    AdminDropdown = "<li class='dropdown'>"
                        + "<a href='' class='dropdown-toggle' data-toggle='dropdown' role='button' aria-expanded='false'>"
                        + CometTrackerArray.NavigationName.toString()
                        + "<b class='caret'></b></a>"
                        + "<ul class='dropdown-menu' role='menu'>";

                    for (i = 0; i < CometTrackerSubNavigationLength; i++) {
                        if (CometTrackerSubNavigationArray[i].enabled.toString() != 0) {
                            SubNavigationStr += "<li><a data-description='Adminstration Option' href='" + baseUrl + CometTrackerSubNavigationArray[i].Url.toString() + "'>" + CometTrackerSubNavigationArray[i].SubNavigationName.toString() + "</a></li>";
                        } else {
                            continue;
                        }
                    }

                    AdminDropdown = AdminDropdown + SubNavigationStr + "</ul></li>";

                }

                if (data.Modules[0].Dispatch.enabled.toString() != 0) {
                    // get the length of the NavigationMenu
                    DispatchNavigationMenuLength = data.Modules[0].Dispatch.NavigationMenu.length;

                    for (var j = 0; j < DispatchNavigationMenuLength; j++) {

                        DispatchArray = data.Modules[0].Dispatch.NavigationMenu[j];

                        // clean SubNavigationStr
                        SubNavigationStr = "";

                        // if NavigationName is not report
                        if (DispatchArray.NavigationName.toString() != "Reports") {

                            // get SubNavigationArray  and length of the SubNavigation menu
                            DispatchSubNavigationLength = DispatchArray.SubNavigation.length;
                            DispatchSubNavigationArray = DispatchArray.SubNavigation;

                            // get tab name, if tab is disable not showing
                            //var tabName = DispatchArray.enabled.toString() != 0 ? DispatchArray.NavigationName.toString() : "";
                            if (DispatchSubNavigationLength > 0) {
                                var navigationName;
                                if (DispatchArray.enabled.toString() == 0)
                                    continue;
                                if (DispatchArray.NavigationName.toString() == "Dispatch")
                                    navigationName = "Work Orders";
                                else
                                    navigationName = DispatchArray.NavigationName.toString();

                                DispatchDropdown += "<li class='dropdown'>"
                                    + "<a href='' class='dropdown-toggle' data-toggle='dropdown' role='button' aria-expanded='false'>"
                                    + navigationName
                                    + "<b class='caret'></b></a>"
                                //+ "<ul class='dropdown-menu' role='menu'>";

                                if (DispatchArray.NavigationName.toString() == "Dashboard" && DispatchArray.enabled.toString() != 0) {
                                    for (var i = 0; i < DispatchSubNavigationLength; i++) {
                                        if (DispatchSubNavigationArray[i].enabled.toString() != 0) {
                                            dropdownFlag = 1;
                                            SubNavigationStr += "<li><a data-description='Dispatch Option' href='" + baseUrl + DispatchSubNavigationArray[i].Url.toString() + "'>" + DispatchSubNavigationArray[i].SubNavigationName.toString() + "</a></li>";
                                        } else {
                                            continue;
                                        }
                                    }
                                }
                                if (DispatchArray.NavigationName.toString() == "Dispatch" && DispatchArray.enabled.toString() != 0) {
                                    for (var i = 0; i < DispatchSubNavigationLength; i++) {
                                        if (DispatchSubNavigationArray[i].enabled.toString() != 0) {
                                            dropdownFlag = 1;
                                            var dispatchModule = DispatchSubNavigationArray[i].SubNavigationName.toString().toLowerCase();
                                            SubNavigationStr += "<li><a data-description='Dispatch Option' href='" + baseUrl + DispatchSubNavigationArray[0].Url.toString() + "/" + dispatchModule + "'>" + DispatchSubNavigationArray[i].SubNavigationName.toString() + "</a></li>";
                                        } else {
                                            continue;
                                        }
                                    }
                                }
                                if (dropdownFlag == 1) {
                                    DispatchDropdown = DispatchDropdown + "<ul class='dropdown-menu' role='menu'>" + SubNavigationStr + "</ul></li>";
                                } else {
                                    DispatchDropdown = DispatchDropdown + SubNavigationStr + "</li>";
                                }
                            }

                        } else {
                            if (DispatchArray.enabled.toString() != 0) {
                                DispatchDropdown = DispatchDropdown + "<li><a class='dropdown' href='" + baseUrl + DispatchArray.Url.toString() + "'>" + DispatchArray.NavigationName.toString() + "</a></li>";
                            }
                        }
                    }
                }

                if (data.Modules[0].Home.enabled.toString() != 0) {
                    HomeArray = data.Modules[0].Home.NavigationMenu[0];
                    if (HomeArray.enabled.toString() != 0) {
                        HomeDropdown = $("<li><a id='home_btn' href='" + baseUrl + "home'>" + HomeArray.NavigationName.toString() + "</a></li>");
                        HomeDropdownStr = "<li><a id='home_btn' href='" + baseUrl + "home'>" + HomeArray.NavigationName.toString() + "</a></li>";
                    } //end of home enabled flag check
                }
                if ((data.Modules[0].Home.enabled.toString() == 0)
                    && (data.Modules[0].Dispatch.enabled.toString() == 0)
                    && (data.Modules[0].CometTracker.enabled.toString() == 0)) {

                    localStorage.setItem('scct-navbar-blank', 'true');
                } else {
                    var nav = $("#nav");
                    nav.removeClass("blankNavBar");
                    if (AdminDropdown.length !== 0) {
                        nav.prepend(AdminDropdown);
                    }
                    if (DispatchDropdown.length !== 0) {
                        nav.prepend(DispatchDropdown);
                    }
                    if (HomeDropdownStr.length !== 0) {
                        nav.prepend(HomeDropdownStr);
                    }
                    localStorage.setItem('scct-navbar-data', HomeDropdownStr + DispatchDropdown + AdminDropdown);

                    /*if (data.Modules[0].Dispatch.enabled.toString() != 0 && data.Modules[0].CometTracker.enabled.toString() != 0) {
                     nav.prepend(DispatchDropdown, AdminDropdown);

                     localStorage.setItem('scct-navbar-data', DispatchDropdown + AdminDropdown);
                     }else if (data.Modules[0].Home.enabled.toString() != 0 && data.Modules[0].CometTracker.enabled.toString() != 0) {
                     nav.prepend(HomeDropdown, AdminDropdown);

                     localStorage.setItem('scct-navbar-data', HomeDropdownStr + AdminDropdown);
                     }else if (data.Modules[0].Home.enabled.toString() != 0 && data.Modules[0].Dispatch.enabled.toString() != 0){
                     nav.prepend(HomeDropdown, DispatchDropdown);

                     localStorage.setItem('scct-navbar-data', HomeDropdownStr + DispatchDropdown);
                     }else {
                     nav.prepend(HomeDropdown, DispatchDropdown, AdminDropdown);

                     localStorage.setItem('scct-navbar-data', HomeDropdownStr + DispatchDropdown + AdminDropdown);
                     }*/
                }
            }
            localStorage.setItem('scct-navbar-saved', 'true');
        }

        // assign class to current active link
        var url = $(location).attr('href').substring($(location).attr('href').lastIndexOf('/') + 1);


        var listItems = $(".menu .adminMenu .middlePrivilegeMenu li a");
        listItems.each(function (idx, li) {
            var product = String($(li)[0]).substring(String($(li)[0]).lastIndexOf('/') + 1);

            if (url === product) {
                if (product.substring(0, product.indexOf('#')).length > 0)
                    var url2 = "a[href$='" + product.substring(0, product.indexOf('#')) + "']";
                else
                    var url2 = "a[href$='" + url + "']";
                $(url2).css({"color": "#FF9E19"});
            }
        });
    }
});
//         $('#nav > ul').not('ul li ul').not('li ul li').children().addClass('current');
//         $(this).closest('li').addClass('current');
//No newline at end of file
