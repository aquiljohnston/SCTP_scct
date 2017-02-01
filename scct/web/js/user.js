$(function () {
    var jqUserDropDowns = $('#equipmentDropdownContainer');
    var jqUserPageSize = jqUserDropDowns.find('#userPageSize');

    jqUserPageSize.on('change', function (event) {
        reloadGridView();
        event.preventDefault();
        return false;
    });

    $(document).off('click', "#UserPagination ul li a").on('click', "#UserPagination ul li a", function () {
        $('#loading').show();
        $('#equipmentGridview').on('pjax:success', function () {
            $('#loading').hide();
        });
    });

    function reloadGridView() {
        var form = jqUserDropDowns.find("#UserForm");
        if (form.find(".has-error").length) {
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
