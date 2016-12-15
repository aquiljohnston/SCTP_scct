$(document).ready(function(){
    
	$('#loading').hide();
	
    var head = $("<a href='/home/index'><img src='/logo/sc_logo.png' alt='' height='50' width='300' ></a>");
    $(".logo").prepend(head);

    var toggleButton =    "<div class='navbar-default navbar-header'>"
        +"<button type='button' class='navbar-toggle collapsed' data-toggle='collapse' data-target='#navbar' aria-expanded='false' aria-controls='navbar'>"
        +"<span class='sr-only'>Navigation</span>"
        + "<span class='icon-bar'></span>"
        +"<span class='icon-bar'></span>"
        +"<span class='icon-bar'></span>"
        +"<?php ?>"
        +"</button>"
        +"</div>";

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

	if(adminID != null || middlePrivilegeID != null || defaultID != null){

		var AdminDropdown, DispatchDropdown, HomeDropdown;
		var PreFixUrl = window.location.hostname;
		
		// get prefix of current project
		PreFixUrl = PreFixUrl.replace(".southerncrossinc.com", "");

		if(adminID != null){
			userRoleID = adminID;
		} else if(middlePrivilegeID != null) {
			userRoleID = middlePrivilegeID;
		} else {
            userRoleID = defaultID;
        }

		//setup ajax call to get all project associate with the user
		/*$.ajax({
			type:"POST",
			url:"index.php?r=project%2Fget-all-projects",
			dataType:"json",
			data: {userID: userRoleID},
			beforeSend: function () {
                        //alert("before send");
                    },
			success: function(data){
				//alert("success to get projects! "+data.projects);
				var Data = $.parseJSON(data.projects);
				$('#projects_dropdown').empty();
				$('#projects_dropdown').append('<li><a data-description="All Projects" href="index.php?r=project-landing%2Findex">My Projects</a></li><hr id="seperator_line">');

				$.each(Data, function(i, item){
					//alert("project name are "+Data[i].ProjectName);
					//append projec name to the dropdown-menu
					$('#projects_dropdown').append('<li><a data-description="SubProject" href="index.php?r=project-landing%2Fview&id='+Data[i].ProjectID+'">'+Data[i].ProjectName+'</a></li>');
				});
			},
			failure: function () {
				alert("Failure getting project list!");
			}
		});*/

		//Build Table-Driven Navigation Menu
		$.ajax({
			type: "GET",
			url: "/home/get-nav-menu",
			dataType: "json",
			data: {id: PreFixUrl},
			beforeSend: function() {
				 $('#loading').show();
			  },
			  complete: function(){
				 $('#loading').hide();
			  },
			success: function(data){
			$('#loading').hide();
				data = $.parseJSON(data.navMenu);
				//console.log(JSON.stringify(data, null, 2));
				NavBar(data);
			}
		});

		function NavBar(data){
				var str="";
				var SubNavigationStr = "";
				var DispatchDropdown = "";
				var HomeDropdown = "";
				var AdminDropdown = "";
				var dropdownFlag = 0;


				if (jQuery.isEmptyObject(data)){
					str="Json array is empty";
				}else{
						// check which module is enabled
						if (data.Modules[0].CometTracker.enabled.toString() !=0){
							CometTrackerArray = data.Modules[0].CometTracker.NavigationMenu[0];

							// clean SubNavigationStr
							SubNavigationStr = "";

							// get SubNavigationArray  and length of the SubNavigation menu
							CometTrackerSubNavigationLength = CometTrackerArray.SubNavigation.length;
							CometTrackerSubNavigationArray = CometTrackerArray.SubNavigation;

							AdminDropdown = "<li class='dropdown'>"
													+"<a href='' class='dropdown-toggle' data-toggle='dropdown' role='button' aria-expanded='false'>"
													+ CometTrackerArray.NavigationName.toString()
													+"<b class='caret'></b></a>"
													+ "<ul class='dropdown-menu' role='menu'>";

							for(i = 0; i < CometTrackerSubNavigationLength; i++){
								if(CometTrackerSubNavigationArray[i].enabled.toString() != 0){
									SubNavigationStr += "<li><a data-description='Adminstration Option' href='/"+CometTrackerSubNavigationArray[i].Url.toString()+"/index'>"+CometTrackerSubNavigationArray[i].SubNavigationName.toString()+"</a></li>";
								}else{
									continue; //unnecessary
								}
							}

							AdminDropdown = AdminDropdown + SubNavigationStr + "</ul></li>";

						}
						if (data.Modules[0].Dispatch.enabled.toString() !=0){
							// get the length of the NavigationMenu
							DispatchNavigationMenuLength = data.Modules[0].Dispatch.NavigationMenu.length;

							for( var j =0; j < DispatchNavigationMenuLength; j++){

								DispatchArray = data.Modules[0].Dispatch.NavigationMenu[j];

								// clean SubNavigationStr
								SubNavigationStr = "";

								// if NavigationName is not report
								if(DispatchArray.NavigationName.toString() != "Reports"){

									// get SubNavigationArray  and length of the SubNavigation menu
									DispatchSubNavigationLength = DispatchArray.SubNavigation.length;
									DispatchSubNavigationArray = DispatchArray.SubNavigation;
									
									if(DispatchSubNavigationLength > 0){
										DispatchDropdown += "<li class='dropdown'>"
															+"<a href='#' class='dropdown-toggle' data-toggle='dropdown' role='button' aria-expanded='false'>"
															+ DispatchArray.NavigationName.toString()
															+"<b class='caret'></b></a>"
															//+ "<ul class='dropdown-menu' role='menu'>";
									
										for(var i = 0; i < DispatchSubNavigationLength; i++){
											if(DispatchSubNavigationArray[i].enabled.toString() != 0){
												dropdownFlag = 1;
												SubNavigationStr += "<li><a data-description='Dispatch Option' href='/"+DispatchSubNavigationArray[i].Url.toString()+"/index'>"+DispatchSubNavigationArray[i].SubNavigationName.toString()+"</a></li>";
											}else{
												continue;
											}
										}
										if(dropdownFlag == 1){
											DispatchDropdown = DispatchDropdown + "<ul class='dropdown-menu' role='menu'>" + SubNavigationStr + "</ul></li>";
										}else{
											DispatchDropdown = DispatchDropdown + SubNavigationStr + "</li>";
										}
									}	
									
								}else{
									DispatchDropdown = DispatchDropdown + "<li><a class='dropdown' href='/"+DispatchArray.Url.toString()+"/index'>"+DispatchArray.NavigationName.toString()+"</a></li>";
								}
							}
						}
						if (data.Modules[0].Home.enabled.toString() !=0){
							HomeArray = data.Modules[0].Home.NavigationMenu[0];

							HomeDropdown = 	$("<li><a id='home_btn' href='/'>"+HomeArray.NavigationName.toString()+"</a></li>");

						}
						if(data.Modules[0].Home.enabled.toString() == data.Modules[0].Dispatch.enabled.toString() == data.Modules[0].CometTracker.enabled.toString() == 0){
							$("#nav").addClass("blankNavBar");
							
						}else{
								$("#nav").prepend(HomeDropdown, DispatchDropdown, AdminDropdown);	
						}
					}
			}

			// assign class to current active link
			var url = $(location).attr('href').substring($(location).attr('href').lastIndexOf('/') + 1);


			var listItems = $(".menu .adminMenu .middlePrivilegeMenu li a");
			listItems.each(function(idx, li) {
				var product = String($(li)[0]).substring(String($(li)[0]).lastIndexOf('/') + 1);

				if(url === product) {
					if(product.substring(0, product.indexOf('#')).length > 0 )
						var url2 = "a[href$='"+product.substring(0, product.indexOf('#'))+"']";
					else
						var url2 = "a[href$='"+url+"']";
					$(url2).css({"color": "#FF9E19"});
				}
			});
	}
});
//         $('#nav > ul').not('ul li ul').not('li ul li').children().addClass('current');
//         $(this).closest('li').addClass('current');
 //No newline at end of file
