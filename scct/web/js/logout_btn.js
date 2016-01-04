$(document).ready(function(){
    
    $("#logout_btn").click(function(){
		url = "http://localhost:8000/index.php?r=login%2Flogout";
		window.location.href = url;
	});  
});
