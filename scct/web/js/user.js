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

    userManagementPaginationListener();
});
function userManagementPaginationListener() {
    $(document).off('click', '#UserPagination .pagination li a').on('click', '#UserPagination .pagination li a', function (event) {
        event.preventDefault();
        var page = $(this).data('page') + 1; // Shift by one to 1-index instead of 0-index.
        $('#UserManagementPageNumber').val(page);
        var form = $('#UserForm');
        $('#loading').show();
        $.pjax.reload({
            container: "#userGridview",
            timeout: 99999,
            url: form.attr("action"),
            type: "get",
            data: form.serialize()
        });
    });
    $('#userGridview').on('pjax:success', function (event, data, status, xhr, options) {
        $('#loading').hide();
    });
    $('#userGridview').on('pjax:error', function (event, data, status, xhr, options) {
        console.log("Error loading PJAX user management gridview");
        //TODO: Consider resending PJAX call
    });
}

