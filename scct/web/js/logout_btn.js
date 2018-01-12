$(document).ready(function(){
    $("#logout_btn").click(function (event) {
        event.preventDefault();
        localStorage.clear(); // Clear the menus
        $.ajax({
            url: '/login/user-logout',
            beforeSend: function () {
                $('#loading').show();
            },
            success: function(data) {
                window.location.href = "/login/index";
                $('#loading').hide();
            }
        })/*.done(function () {
            window.location.href = "/login/index";
            $('#loading').hide();
        });*/
    });
});
