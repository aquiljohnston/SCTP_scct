$(document).ready(function(){
    
    $("#logout_btn").click(function(){
		url = "http://scct.southerncrossinc.com/index.php?r=login%2Fuser-logout";
		window.location.href = url;
	});  
});
