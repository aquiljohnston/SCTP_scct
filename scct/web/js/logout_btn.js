$(document).ready(function(){
    $("#logout_btn").click(function () {
        $.ajax({
            url: "/login/user-logout",
            type: "POST",
            data: {}
        });
    });
});
