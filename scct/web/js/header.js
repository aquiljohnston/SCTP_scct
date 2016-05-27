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

    var nav1 = $("<li class='dropdown'><a href='' class='dropdown-toggle' data-toggle='dropdown' role='button' aria-expanded='false'>"
        + "DASHBOARD<b class='caret'></b></a>"
        + "    <ul class='dropdown-menu' role='menu'>"                                
            + "<li><a data-description='Image Animation' href=''>dashboard 1</a></li>"
            + "<li><a data-description='Image Animation' href=''>dashboard 2</a></li>"
            + "<li><a data-description='Image Animation' href=''>dashboard 3</a></li>"
            + "<li><a data-description='Instrument Repair' href=''>dashboard 4</a></li>"
        + "</ul></li>");
    var nav2 = $("<li><a href='dispatch.html'>DISPATCH</a></li>");
    var nav3 = $("<li class='dropdown'><a href='' class='dropdown-toggle' data-toggle='dropdown' role='button' aria-expanded='false'>"
        + "REPORTS<b class='caret'></b></a>"
        + "    <ul class='dropdown-menu' role='menu'>"                                
            + "<li><a data-description='Image Animation' href=''>report 1</a></li>"
            + "<li><a data-description='Image Animation' href=''>report 2</a></li>"
            + "<li><a data-description='Image Animation' href=''>report 3</a></li>"
            + "<li><a data-description='Instrument Repair' href=''>report 4</a></li>"
        + "</ul></li>");

		
	var nav4 = $("<li class='dropdown'><a href='' class='dropdown-toggle' data-toggle='dropdown' role='button' aria-expanded='false'>"
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

    var nav6 = $("<li><a id='home_btn' href='index.php?'>HOME</a></li>");

//Ajax call to retrieve all the projects for the project drop-down selection on the main menu
    var nav7 = $("<li class='dropdown'><a href='' class='dropdown-toggle' data-toggle='dropdown' role='button' aria-expanded='false'>"
        + "PROJECTS<b class='caret'></b></a>"
        + "    <ul href='#' id='projects_dropdown' class='dropdown-menu' role='menu'>"
		+ "</ul></li>");

    // $("#nav").prepend(nav1, nav2, nav3, nav4);
		// $("#adminNav").prepend(nav1, nav2, nav3, nav5);
		$("#middlePrivilegeNav").prepend(nav6, nav7, nav4);
		$("#adminNav").prepend(nav6, nav7, nav5);
        $("#nav").prepend(nav6, nav7);
    
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
	
	// gather userID based on user role type
	var adminID = $(".adminMenu").attr("id");
	var middlePrivilegeID = $(".middlePrivilegeMenu").attr("id");
	var defaultID = $(".menu").attr("id");
	var userRoleID = -1;
	
	if(adminID != null || middlePrivilegeID != null || defaultID != null){
	
		if(adminID != null){
			userRoleID = adminID;
		} else if(middlePrivilegeID != null) {
			userRoleID = middlePrivilegeID;
		} else {
            userRoleID = defaultID;
        }
		
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
	}
});
//         $('#nav > ul').not('ul li ul').not('li ul li').children().addClass('current');
//         $(this).closest('li').addClass('current');
 //No newline at end of file
