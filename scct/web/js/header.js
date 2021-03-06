navBarLoaded = false;
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

    //set login logo link
    var sc_logout_logo = $("<a href='/'><img src='/logo/sc_logo.png' alt='' height='50' width='300' ></a>");
    $(".sc_logout_logo").prepend(sc_logout_logo);

    //login header setting
    var login_head = $(toggleButton + "<div id='navbar' class='navbar-collapse collapse'>"
        + "<ul class='nav navbar-nav' id='nonav'></ul>");
    $(".loginMenu").prepend(login_head);

    // gather userID based on user role type
    var defaultID = $(".menu").attr("id");
    var userRoleID = -1;

	//check if user is logged in before trying to get menus
    if (defaultID != null) {	
		//should not be usign these hard coded values
        var AdminDropdown, DispatchDropdown, HomeDropdown;
		
        function ajaxLoadNavBar() {
            $.ajax({
                type: "GET",
                url: "/base/get-nav-menu",
                dataType: "json",
                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (data) {
					try{
						data = $.parseJSON(data.navMenu);
					} catch(error){
						$('#loading').hide();
						$('#nav').html("<div class='alert alert-warning'>We were unable to load the menu. Please logout and try again or call support.</div>");
						console.error("Menu not loaded. Inspect the request for more info.");
					}
                    NavBar(data);
                    navBarLoaded = true;
                    checkNavBarLoading();
                },
                error: function () {
					$('#loading').hide();
					$('#nav').html("<div class='alert alert-warning'>We were unable to load the menu. Please logout and try again or call support.</div>");
                    console.error("Menu not loaded. Inspect the request for more info.");
                }
            });
        }

        function isLocalStorageNameSupported() {
            var testKey = 'testOfLocalStorageSupport', storage = window.localStorage;
            try {
                storage.setItem(testKey, '1');
                storage.removeItem(testKey);
                return true;
            } catch (error) {
                return false;
            }
        }
        if(isLocalStorageNameSupported()) {
            //Build Table-Driven Navigation Menu
            if (localStorage.getItem('scct-navbar-saved') == "true") {
				console.log('loading nav bar from storage');
                $("#nav").prepend(localStorage.getItem('scct-navbar-data'));
                navBarLoaded = true;
                checkNavBarLoading();
            } else {
				console.log('getting nav bar from api');
				$("#nav").addClass("blankNavBar");
                ajaxLoadNavBar();
            }
        } else {
            ajaxLoadNavBar();
        }

        function NavBar(data) {
			var modules = data.Modules[0];
            var SubNavigationStr = "";
            var dropdownFlag = 0;
            var baseUrl = "/";
			var Dropdown = "";
			var LocalStorageString = "";
			var menuItems = [];
            if (!jQuery.isEmptyObject(data)) {
				//loop all modules
				for (var module in modules){
					if (modules.hasOwnProperty(module)){
						//check if module is enable
						if(modules[module].enabled.toString() == 1)
						{				
							//console.log(modules[module]);
							navigationMenusLength = modules[module].NavigationMenu.length;
							//loop all nav menu items withing a module
							for (var i = 0; i < navigationMenusLength; i++)
							{
								//reset dropdown variables
								dropdownFlag = 0;
								Dropdown = "";
								
								//set navigation menu values
								navigationMenuArray = modules[module].NavigationMenu[i];
								var navigationName = "";
								var navigationURL = "";
								if (navigationMenuArray.enabled.toString() == 0)
									continue;
								//check to change dispatch to work orders for non pge
								//TODO do this better
								if (navigationMenuArray.NavigationName.toString() == "Dispatch")
									navigationName = "Work Orders";
								else
									navigationName = navigationMenuArray.NavigationName.toString();
								if(navigationMenuArray.Url !== null)
									navigationURL = navigationMenuArray.Url.toString();
								
								// clean SubNavigationStr
								SubNavigationStr = "";
								subNavigationLength = 0;
								
								// get SubNavigationArray  and length of the SubNavigation menu
								if("SubNavigation" in navigationMenuArray)
								{
									subNavigationArray = navigationMenuArray.SubNavigation;
									subNavigationLength = subNavigationArray.length;
								}
								
								//check if sub navs exist
								if (subNavigationLength > 0) {
									
									//create dropdown base
									Dropdown += "<li class='dropdown'>"
										+ "<a href='' class='dropdown-toggle' data-toggle='dropdown' role='button' aria-expanded='false'>"
										+ navigationName
										+ "<b class='caret'></b></a>"
									
									//loop sub navigation items
									for (var j = 0; j < subNavigationLength; j++) {
										if (subNavigationArray[j].enabled.toString() != 0) {
											dropdownFlag = 1;
											subNavBaseUrl = baseUrl;
											//if sub nav is a full url remove leading "/" to redirect away from main site
											if(subNavigationArray[j].Url.toString().includes(".com"))
												subNavBaseUrl = "";
											SubNavigationStr += "<li><a data-description='Dropdown" + j + "Option' href='" + subNavBaseUrl + subNavigationArray[j].Url.toString() + "'>" + subNavigationArray[j].SubNavigationName.toString() + "</a></li>";
										} else {
											continue;
										}
									}
									if (dropdownFlag == 1) {
										Dropdown += "<ul class='dropdown-menu' role='menu'>" + SubNavigationStr + "</ul></li>";
									} else {
										Dropdown += SubNavigationStr + "</li>";
									}
									//console.log("Dropdown Value " + module + ": " + Dropdown);
								}
								else
								{
									Dropdown += "<li><a id='" + navigationName + "_btn' href='" + baseUrl + navigationURL + "'>" + navigationName + "</a></li>";
									//console.log("Dropdown Value " + module + ": " + Dropdown);
								}
								//create object of dropdowns to organize before appending.
								menuItems[navigationMenuArray.SortSequence.toString()] = Dropdown;
							}	
						}
					}
				}
				//order and loop dropdown items and add them to nav bar object
				Object.keys(menuItems).sort();
				for (var item in menuItems)
				{
					//console.log("Dropdown Before Prepend - Key: " + item + " Value: " + menuItems[item]);
					//append dropdowns to nav bar
					var nav = $("#nav");
					nav.removeClass("blankNavBar");
					if (menuItems[item].length !== 0) {
						nav.append(menuItems[item]);
					}
					//add dropdown to local storage
					LocalStorageString += menuItems[item];
				}
				if(isLocalStorageNameSupported()) {
					localStorage.setItem('scct-navbar-data', LocalStorageString);
					localStorage.setItem('scct-navbar-saved', 'true');
				}
            }
        }

        // assign class to current active link
        var url = $(location).attr('href').substring($(location).attr('href').lastIndexOf('/') + 1);


        var listItems = $(".menu li a");
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

function checkNavBarLoading() {
	if(navBarLoaded) $("#loading").hide();
}