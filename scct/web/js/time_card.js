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
        reloadGridView();
        event.preventDefault();
        return false;
    });

    $(document).off('click', "#TCPagination ul li a").on('click', "#TCPagination ul li a", function () {
        $('#loading').show();
        $('#timeCardGridview').on('pjax:success', function () {
            $('#loading').hide();
        });
    });
    
    function reloadGridView() {
        var form = jqTCDropDowns.find("#TimeCardForm");
        if (form.find(".has-error").length){
            return false;
        }
        $('#loading').show();
        $.pjax.reload({
            type: 'POST',
            url: form.attr("action"),
            container: '#timeCardGridview', // id to update content
            data: form.serialize(),
            timeout: 99999
        }).done(function () {
            $('#loading').hide();
        });
    }
});
