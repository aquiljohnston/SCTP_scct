$(document).ready(function(){
    
    var head = $("<a href='index.php?r=home'><img src='logo/sc_logo.png' alt='' height='50' width='300' ></a>");
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
	var sc_logout_logo = $("<a href='index.php?'><img src='logo/sc_logo.png' alt='' height='50' width='300' ></a>");
    $(".sc_logout_logo").prepend(sc_logout_logo);
	
	//login header setting
	var login_head = $(toggleButton + "<div id='navbar' class='navbar-collapse collapse'>"
        + "<ul class='nav navbar-nav' id='nonav'></ul>"
        + "</div><div class='clear'></div>");    
    $(".loginMenu").prepend(login_head);
		
	/*var nav4 = $("<li class='dropdown'><a href='' class='dropdown-toggle' data-toggle='dropdown' role='button' aria-expanded='false'>"
        + "ADMINISTRATION<b class='caret'></b></a>"
        + "    <ul class='dropdown-menu' role='menu'>"  
            + "<li><a data-description='Image Animation' href='index.php?r=user%2Findex'>User Management</a></li>"
            + "<li><a data-description='Image Animation' href='index.php?r=equipment%2Findex'>Equipment Management</a></li>"
            + "<li><a data-description='Image Animation' href='index.php?r=time-card%2Findex'>Time Cards</a></li>"
            + "<li><a data-description='Instrument Repair' href='index.php?r=mileage-card%2Findex'>Mileage Cards</a></li>"
        + "</ul></li>");
		
      var nav5 = $("<li class='dropdown'><a href='' class='dropdown-toggle' data-toggle='dropdown' role='button' aria-expanded='false'>"
          + "ADMINISTRATION<b class='caret'></b></a>"
          + "    <ul class='dropdown-menu' role='menu'>"  
			  + "<li><a data-description='Instrument Repair' href='index.php?r=client%2Findex'>Clients</a></li>"
			  + "<li><a data-description='Instrument Repair' href='index.php?r=project%2Findex'>Projects</a></li>"			
              + "<li><a data-description='Image Animation' href='index.php?r=user%2Findex'>User Management</a></li>"
              + "<li><a data-description='Image Animation' href='index.php?r=equipment%2Findex'>Equipment Management</a></li>"
              + "<li><a data-description='Image Animation' href='index.php?r=time-card%2Findex'>Time Cards</a></li>"
              + "<li><a data-description='Instrument Repair' href='index.php?r=mileage-card%2Findex'>Mileage Cards</a></li>"
          + "</ul></li>");

    var nav6 = $("<li><a id='home_btn' href='index.php?'>HOME</a></li>");*/

//Ajax call to retrieve all the projects for the project drop-down selection on the main menu
    var nav7 = $("<li class='dropdown'><a href='' class='dropdown-toggle' data-toggle='dropdown' role='button' aria-expanded='false'>"
        + "PROJECTS<b class='caret'></b></a>"
        + "    <ul href='#' id='projects_dropdown' class='dropdown-menu' role='menu'>"
		+ "</ul></li>");
	
	// gather userID based on user role type
	var adminID = $(".adminMenu").attr("id");
	var middlePrivilegeID = $(".middlePrivilegeMenu").attr("id");
	var defaultID = $(".menu").attr("id");
	var userRoleID = -1;
	
	if(adminID != null || middlePrivilegeID != null || defaultID != null){
	
		var AdminDropdown, DispatchDropdown, HomeDropdown;
	
		if(adminID != null){
			userRoleID = adminID;
		} else if(middlePrivilegeID != null) {
			userRoleID = middlePrivilegeID;
		} else {
            userRoleID = defaultID;
        }
		
		// $("#nav").prepend(nav1, nav2, nav3, nav4);
		// $("#adminNav").prepend(nav1, nav2, nav3, nav5);
		/*$("#middlePrivilegeNav").prepend(nav6, nav7, nav4);
		$("#adminNav").prepend(nav6, nav7, nav5);
		$("#nav").prepend(nav6, nav7);*/
		
		//setup ajax call to get all project associate with the user
		$.ajax({
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
		});
		
		//Build Table-Driven Navigation Menu
		$.ajax({
			type: "GET",
			url: "index.php?r=home%2Fget-nav-menu",
			dataType: "json",
			//data: {id: 3},
			success: function(data){
				data = $.parseJSON(data.navMenu);
				//console.log(JSON.stringify(data, null, 2));
				NavBar(data);
			}
		});
		
		function NavBar(data){
				var str="";
				var SubNavigationStr = "";
				var i;
				
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
									SubNavigationStr += "<li><a data-description='Adminstration Option' href='index.php?r="+CometTrackerSubNavigationArray[i].Url.toString()+"%2Findex'>"+CometTrackerSubNavigationArray[i].SubNavigationName.toString()+"</a></li>";
								}else{
									continue;
								}
							}
							
							AdminDropdown = AdminDropdown + SubNavigationStr + "</ul></li>";
	
						} 
						if (data.Modules[0].Dispatch.enabled.toString() !=0){
							DispatchArray = data.Modules[0].Dispatch.NavigationMenu[0];
							
							// clean SubNavigationStr
							SubNavigationStr = "";
							
							// get SubNavigationArray  and length of the SubNavigation menu 
							DispatchSubNavigationLength = DispatchArray.SubNavigation.length;
							DispatchSubNavigationArray = DispatchArray.SubNavigation;
							
							DispatchDropdown = "<li class='dropdown'>"
													+"<a href='' class='dropdown-toggle' data-toggle='dropdown' role='button' aria-expanded='false'>"
													+ DispatchArray.NavigationName.toString()
													+"<b class='caret'></b></a>"
													+ "<ul class='dropdown-menu' role='menu'>"; 
							
							for(i = 0; i < DispatchSubNavigationLength; i++){
								if(DispatchSubNavigationArray[i].enabled.toString() != 0){
									SubNavigationStr += "<li><a data-description='Dispatch Option' href='index.php?r="+DispatchSubNavigationArray[i].Url.toString()+"%2Findex'>"+DispatchSubNavigationArray[i].SubNavigationName.toString()+"</a></li>";
								}else{
									continue;
								}
							}
							
							DispatchDropdown = DispatchDropdown + SubNavigationStr + "</ul></li>";
							
						}				
						if (data.Modules[0].Home.enabled.toString() !=0){
							HomeArray = data.Modules[0].Home.NavigationMenu[0];
							
							HomeDropdown = 	$("<li><a id='home_btn' href='index.php?'>"+HomeArray.NavigationName.toString()+"</a></li>");		
													
						}														
					}
					
					$("#middlePrivilegeNav").prepend(HomeDropdown, nav7, AdminDropdown);
					$("#adminNav").prepend(HomeDropdown, nav7, AdminDropdown);
					$("#nav").prepend(HomeDropdown, nav7);
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
