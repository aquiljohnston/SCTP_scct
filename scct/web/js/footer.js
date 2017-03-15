$(function(){
    var date = new Date();
    var currentYear = date.getFullYear(); 
        
    var copyright = $("<div class='row'>"
        + "<div class='col-md-12' id='copyright-bar'>"
        + "<div>Southern Cross &copy; " + currentYear + " | All Rights Reserved</div>"
        + "</div>"
        + "</div>");
    
    $(".copyright-section").append(copyright);
    
});
