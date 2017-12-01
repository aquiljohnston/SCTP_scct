$(function(){
    var jqTimeCardFilter = $('#timecard_filter');
    var jqTCDropDowns = $('#timeCardDropdownContainer');
    var jqWeekSelection = jqTimeCardFilter.find('#timeCardDateRange');
    var jqTCPageSize = jqTCDropDowns.find('#timeCardPageSize');

    jqWeekSelection.on('change', function (event) {
        event.preventDefault();
        var selected = $(this).find(":selected").val();
        if(selected == "other") {
            $('#datePickerContainer').css("display", "block");
        }else {
            $('#datePickerContainer').css("display", "none");
            reloadTimeCardGridView();
        }
    });

    jqTCPageSize.on('change', function (event) {
        $('#timeCardPageNumber').val(1);
        reloadTimeCardGridView();
        event.preventDefault();
        return false;
    });

    $(document).off('click', "#TCPagination ul li a").on('click', "#TCPagination ul li a", function (event) {
        var page = $(this).data('page') + 1; // Shift by one to 1-index instead of 0-index.
        $('#timeCardPageNumber').val(page);
        reloadTimeCardGridView();
        event.preventDefault();
        return false;
    });
	
	// timecard filter listener
    $(document).off('keypress', '#timeCardFilter').on('keypress', '#timeCardFilter', function (e) {
        if (e.keyCode === 13 || e.keyCode === 10) {
            e.preventDefault();
			$('#timeCardPageNumber').val(1);
            reloadTimeCardGridView();
        }
    });

    $(document).off('click', '#timeCardSearchCleanFilterButton').on('click', '#timeCardSearchCleanFilterButton', function (){
        $('#timeCardFilter').val("");
        reloadTimeCardGridView();
    });
    
    function reloadTimeCardGridView() {
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
