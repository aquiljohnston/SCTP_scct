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
function firePageChangeHandler(event, page) {
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
    event.preventDefault();
}

function userManagementPaginationListener() {
    $(document).off('click', '#UserPagination .pagination li a').on('click', '#UserPagination .pagination li a', function (event) {
        // Shift by one to 1-index instead of 0-index.
        firePageChangeHandler(event, $(this).data('page') + 1); //TODO: Can we simply this while still sending event parameter?
    });
    $('#userFilter #dynamicmodel-filter').keypress(function(event) {
        if(event.which == 13) {
            firePageChangeHandler(event, 1);
        }
    });
    $('#userGridview').on('pjax:success', function (event, data, status, xhr, options) {
        $('#loading').hide();
    });
    $('#userGridview').on('pjax:error', function (event, data, status, xhr, options) {
        console.log("Error loading PJAX user management gridview");
        //TODO: Consider resending PJAX call
    });
}

