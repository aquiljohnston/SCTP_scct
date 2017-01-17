$(function(){
    var jqMCGridViewContainer = $('#mileageCardGridViewContainer');
    var jqMCDropDowns = $('#mileageCardDropdownContainer');
    var jqWeekSelection = jqMCDropDowns.find('#mileageCardWeekSelection');
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

    $(document).off('click', "#MCPagination ul li a").on('click', "#MCPagination ul li a", function () {
        $('#loading').show();
        $('#mileageCardGridview').on('pjax:success', function () {
            $('#loading').hide();
        });
    });
    
    function reloadGridView() {
        var form = jqMCDropDowns.find("#MileageCardForm");
        if (form.find(".has-error").length){
            return false;
        }
        $('#loading').show();
        $.pjax.reload({
            type: 'POST',
            url: form.attr("action"),
            container: '#mileageCardGridview', // id to update content
            data: form.serialize(),
            timeout: 99999
        }).done(function () {
            $('#loading').hide();
        });
    }
});
