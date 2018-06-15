/**
 * Created by tzhang on 11/16/2017.
 */
$(function () {
	
	$(document).off('click', "#clientIndexPagination ul li a").on('click', "#clientIndexPagination ul li a", function (event) {
        var page = $(this).data('page') + 1; // Shift by one to 1-index instead of 0-index.
        $('#clientIndexPageNumber').val(page);
        clientGridViewReload();
        event.preventDefault();
        return false;
    });
	
    $(document).off('keypress', '#clientSearchField').on('keypress', '#clientSearchField', function (e) {
        if (e.keyCode === 13 || e.keyCode === 10) {
            e.preventDefault();
			$('#clientIndexPageNumber').val(1);
            clientGridViewReload();
        }
    });

    $(document).off('click', '#clientSearchCleanFilterButton').on('click', '#clientSearchCleanFilterButton', function (){
        $('#clientSearchField').val("");
		$('#clientIndexPageNumber').val(1);
        clientGridViewReload();
    });
});

function clientGridViewReload() {
    var form = $("#ClientForm");
    $('#loading').show();
    $.pjax.reload({
        container: "#clientIndexPjaxContainer",
        timeout: 99999,
        url: form.attr("action"),
        type: "GET",
        data: form.serialize()
    }).done(function () {
    });
    $('#clientIndexPjaxContainer').on('pjax:success', function () {
        $('#loading').hide();
    });
    $('#clientIndexPjaxContainer').on('pjax:error', function () {
        console.log("Error");
    });
}