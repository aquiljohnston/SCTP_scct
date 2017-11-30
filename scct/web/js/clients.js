/**
 * Created by tzhang on 11/16/2017.
 */
$(function () {

    $(document).off('keypress', '#clientSearchField').on('keypress', '#clientSearchField', function (e) {
        if (e.keyCode === 13 || e.keyCode === 10) {
            e.preventDefault();
            clientGridViewReload();
        }
    });

    $(document).off('click', '#clientSearchCleanFilterButton').on('click', '#clientSearchCleanFilterButton', function (){
        $('#clientSearchField').val("");
        clientGridViewReload();
    });
});

function clientGridViewReload() {
    var form = $("#ClientForm");
    $('#loading').show();
    $.pjax.reload({
        container: "#clientGridview",
        timeout: 99999,
        url: form.attr("action"),
        type: "GET",
        data: form.serialize()
    }).done(function () {
    });
    $('#clientGridview').on('pjax:success', function (event, data, status, xhr, options) {
        $('#loading').hide();
    });
    $('#clientGridview').on('pjax:error', function (event, data, status, xhr, options) {
        console.log("Error");
    });
}