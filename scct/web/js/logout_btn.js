$(document).ready(function(){
    $("#logout_btn").click(function () {
        $("#loading").show();
        localStorage.clear(); // Clear the menus
        window.location.href = "/login/user-logout"; //Link type redirect (instead of replace)
    });
});
