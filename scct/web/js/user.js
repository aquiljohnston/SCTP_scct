$(function(){
    var jqTCGridViewContainer = $('#userGridview');
    var jqTCDropDowns = $('#UserDropdownContainer');
    var jqTCPageSize = jqTCDropDowns.find('#userPageSize');


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

    $(document).off('click', "#userPagination ul li a").on('click', "#userPagination ul li a", function () {
        $('#loading').show();
        $('#userGridview').on('pjax:success', function () {
            $('#loading').hide();
        });
    });
    
    function reloadGridView() {
        var form = jqTCDropDowns.find("#userForm");
        if (form.find(".has-error").length){
            return false;
        }
        $('#loading').show();
        $.pjax.reload({
            type: 'POST',
            url: form.attr("action"),
            container: '#userGridview', // id to update content
            data: form.serialize(),
            timeout: 99999
        }).done(function () {
            $('#loading').hide();
        });
    }
});
