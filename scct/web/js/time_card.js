$(function(){
    var jqTimeCardFilter = $('#timecard_filter');
    var jqTCDropDowns = $('#timeCardDropdownContainer');
    var jqWeekSelection = jqTimeCardFilter.find('#weekSelection');
    var jqTCPageSize = jqTCDropDowns.find('#timeCardPageSize');


    jqWeekSelection.on('change', function (event) {
        event.preventDefault();
        reloadGridView();
        return false;
    });

    jqTCPageSize.on('change', function (event) {
        $('#timeCardPageNumber').val(1);
        reloadGridView();
        event.preventDefault();
        return false;
    });

    $(document).off('click', "#TCPagination ul li a").on('click', "#TCPagination ul li a", function (event) {
        var page = $(this).data('page') + 1; // Shift by one to 1-index instead of 0-index.
        $('#timeCardPageNumber').val(page);
        reloadGridView();
        event.preventDefault();
        return false;
    });
	
	// timecard filter listener
    $(document).off('keypress', '#timeCardFilter').on('keypress', '#timeCardFilter', function (e) {
        if (e.keyCode === 13 || e.keyCode === 10) {
            e.preventDefault();
			$('#timeCardPageNumber').val(1);
            reloadGridView();
        }
    });

    $(document).off('click', '#timeCardSearchCleanFilterButton').on('click', '#timeCardSearchCleanFilterButton', function (){
        $('#timeCardFilter').val("");
        reloadGridView();
    });
    
    function reloadGridView() {
        var form = jqTCDropDowns.find("#TimeCardForm");
        if (form.find(".has-error").length){
            return false;
        }
        $('#loading').show();
        $.pjax.reload({
            type: 'GET',
            url: form.attr("action"),
            container: '#timeCardGridview', // id to update content
            data: form.serialize(),
            timeout: 99999
        });
        $('#timeCardGridview').on('pjax:success', function () {
            $('#loading').hide();
            applyOnClickListeners();
        });
        $('#timeCardGridview').on('pjax:error', function () {
            $('#loading').hide();
            location.reload();
        });
    }
});
