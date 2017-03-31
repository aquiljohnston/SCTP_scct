$(function(){
    var jqMileageCardFilter = $('#mileage_card_filter');
    var jqMCDropDowns = $('#mileageCardDropdownContainer');
    var jqWeekSelection = jqMileageCardFilter.find('#mileageCardWeekSelection');
    var jqMCPageSize = jqMCDropDowns.find('#mileageCardPageSize');


    jqWeekSelection.on('change', function (event) {
        event.preventDefault();
        reloadGridView();
        return false;
    });

    jqMCPageSize.on('change', function (event) {
        reloadGridView();
        event.preventDefault();
        return false;
    });

    $(document).off('click', "#MCPagination ul li a").on('click', "#MCPagination ul li a", function (event) {
        var page = $(this).data('page') + 1; // Shift by one to 1-index instead of 0-index.
        $('#mileageCardPageNumber').val(page);
        reloadGridView();
        event.preventDefault();
        return false;
    });
    // Take this out of onclick so it doesn't get stacked up
    $('#mileageCardGridview').on('pjax:success', function () {
        $('#loading').hide();
        applyOnClickListeners();
    });

    
    function reloadGridView() {
        var form = jqMCDropDowns.find("#MileageCardForm");
        if (form.find(".has-error").length){
            return false;
        }
        $('#loading').show();
        $.pjax.reload({
            type: 'GET',
            url: form.attr("action"),
            container: '#mileageCardGridview', // id to update content
            data: form.serialize(),
            timeout: 99999
        }).done(function () {
            $('#loading').hide();
            applyOnClickListeners();
        });
    }
});
