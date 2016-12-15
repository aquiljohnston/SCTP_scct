$(document).ready(function(){
    
    $("#logout_btn").click(function(){
		//url = "/index.php?r=login%2Fuser-logout";
        url = "/login/user-logout";
		window.location.href = url;
	});  
});
