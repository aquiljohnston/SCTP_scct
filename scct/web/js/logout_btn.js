$(document).ready(function(){
    $("#logout_btn").click(function (event) {
        event.preventDefault();
        localStorage.clear(); // Clear the menus
        $.ajax({
            url: '/login/user-logout',
            beforeSend: function () {
                $('#loading').show();
            }
        }).done(function () {
            window.location.href = "/login/index";
            $('#loading').hide();
        });
    });
});
