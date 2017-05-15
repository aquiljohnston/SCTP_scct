$(function () {
    var jqUserDropDowns = $('#userDropdownContainer');
    var jqUserPageSize = jqUserDropDowns.find('#userPageSize');

    jqUserPageSize.on('change', function (event) {
        reloadGridView();
        event.preventDefault();
        return false;
    });

    $(document).off('click', "#UserPagination ul li a").on('click', "#UserPagination ul li a", function () {
        $('#loading').show();
        $('#userGridview').on('pjax:success', function () {
            $('#loading').hide();
        });
    });

    $('#userFilter #dynamicmodel-filter').keypress(function(e) {
        if(e.which == 13) {
            reloadGridView();
            e.preventDefault();
        }
    });

    function reloadGridView() {
        var form = jqUserDropDowns.find("#UserForm");
        if (form.find(".has-error").length) {
            return false;
        }
        $('#loading').show();
        var data = form.serialize();
        $.pjax.reload({
            type: 'GET',
            url: form.attr("action"),
            container: '#userGridview', // id to update content
            data: data,
            timeout: 99999
        }).done(function () {
            $('#loading').hide();
        });
    }
});
