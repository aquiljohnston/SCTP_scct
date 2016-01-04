$(document).ready(function(){
    
    $("#logout_btn").click(function(){
		url = "http://localhost:8000/index.php?r=login%2Fuser-logout";
		window.location.href = url;
	});  
});
