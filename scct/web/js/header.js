$(document).ready(function(){
    
    var head = $("<a href='http://scct.southerncrossinc.com/index.php?r=home'><img src='logo/sc_logo.png' alt='' height='50' width='300' ></a>");
    $(".logo").prepend(head);

    var toggleButton =    "<div class='navbar-default navbar-header'>"
        +"<button type='button' class='navbar-toggle collapsed' data-toggle='collapse' data-target='#navbar' aria-expanded='false' aria-controls='navbar'>"
        +"<span class='sr-only'>Navigation</span>"
        + "<span class='icon-bar'></span>"
        +"<span class='icon-bar'></span>"
        +"<span class='icon-bar'></span>"
        +"</button>"
        +"</div>";
	
	// default head setting
    var head = $(toggleButton + "<div id='navbar' class='navbar-collapse collapse'>"
        + "<ul class='nav navbar-nav' id='nav'></ul>"
        + "</div><div class='clear'></div>");    
    $(".menu").prepend(head);
	
	// admin header setting
	var adminHead = $(toggleButton + "<div id='navbar' class='navbar-collapse collapse'>"
        + "<ul class='nav navbar-nav' id='adminNav'></ul>"
        + "</div><div class='clear'></div>");    
	$(".adminMenu").prepend(adminHead);
	
	//set login logo link
	var sc_logout_logo = $("<a href='http://scct.southerncrossinc.com/index.php?'><img src='logo/sc_logo.png' alt='' height='50' width='300' ></a>");
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
            + "<li><a data-description='Image Animation' href='http://scct.southerncrossinc.com/index.php?r=user%2Findex'>User Management</a></li>"
            + "<li><a data-description='Image Animation' href='http://scct.southerncrossinc.com/index.php?r=equipment%2Findex'>Equipment Manager</a></li>"
            + "<li><a data-description='Image Animation' href='http://scct.southerncrossinc.com/index.php?r=time-card%2Findex'>Timecards</a></li>"
            + "<li><a data-description='Instrument Repair' href='http://scct.southerncrossinc.com/index.php?r=mileage-card%2Findex'>Mileagecards</a></li>"
        + "</ul></li>");
		
    var nav5 = $("<li class='dropdown'><a href='' class='dropdown-toggle' data-toggle='dropdown' role='button' aria-expanded='false'>"
        + "ADMINISTRATION<b class='caret'></b></a>"
        + "    <ul class='dropdown-menu' role='menu'>"  
			+ "<li><a data-description='Instrument Repair' href='http://scct.southerncrossinc.com/index.php?r=client%2Findex'>Clients</a></li>"
			+ "<li><a data-description='Instrument Repair' href='http://scct.southerncrossinc.com/index.php?r=project%2Findex'>Projects</a></li>"			
            + "<li><a data-description='Image Animation' href='http://scct.southerncrossinc.com/index.php?r=user%2Findex'>User Management</a></li>"
            + "<li><a data-description='Image Animation' href='http://scct.southerncrossinc.com/index.php?r=equipment%2Findex'>Equipment Manager</a></li>"
            + "<li><a data-description='Image Animation' href='http://scct.southerncrossinc.com/index.php?r=time-card%2Findex'>Timecards</a></li>"
            + "<li><a data-description='Instrument Repair' href='http://scct.southerncrossinc.com/index.php?r=mileage-card%2Findex'>Mileagecards</a></li>"
        + "</ul></li>");
    
    // var nav5 = $("<li><a href='equipmentrepairs.html'>equipment repairs</a></li>");
    // var nav6 = $("<li><a href='industry.html'>Our Industry</a></li>");
    // var nav7 = $("<li><a href='careers.html'>Careers</a></li>");
    // var nav8 = $("<li><a href='contact.html'>Contact</a></li>");
    

    
    // $("#nav").prepend(nav1, nav2, nav3, nav4, nav5, nav6, nav7, nav8);
		$("#nav").prepend(nav1, nav2, nav3, nav4);
		$("#adminNav").prepend(nav1, nav2, nav3, nav5);
		
    
    // assign class to current active link
    var url = $(location).attr('href').substring($(location).attr('href').lastIndexOf('/') + 1);
        
                                    
    var listItems = $(".menu li a");
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
});
//         $('#nav > ul').not('ul li ul').not('li ul li').children().addClass('current');
//         $(this).closest('li').addClass('current');
 //No newline at end of file
