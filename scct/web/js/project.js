//Taken from commit a09e6b8d7fed3035d888ade56ffd0e1a623f4c00 on PGE-Web
$(function(){
    $('#projectAddUserSubmitBtn').on('click',function(){
        $(this).val('Please wait ...')
            .attr('disabled','disabled');
        $('#projectAddUserResetBtn').attr('disabled','disabled');
        addRemoveUser();
        //$('#projectSortableInputForm').submit();
    });
    $('#projectAddModuleSubmitBtn').on('click',function(){
        $(this).val('Please wait ...')
            .attr('disabled','disabled');
        $('#projectAddModuleResetBtn').attr('disabled','disabled');
        $('#projectAddModuleform').submit();
    });

    // project filter listener
    $(document).off('keypress', '#projectFilter').on('keypress', '#projectFilter', function (e) {
        if (e.keyCode === 13 || e.keyCode === 10) {
            e.preventDefault();
            reloadProjectGridView();
        }
    });
});

function reloadProjectGridView() {
    var jqProjectAddUser = $('.project-add-user');
    var form = jqProjectAddUser.find("#projectAdduserform");
    if (form.find(".has-error").length) {
        return false;
    }
    $('#loading').show();
    $.pjax.reload({
        type: 'GET',
        url: '/project/add-user',
        container: '#projectSortableView', // id to update content
        data: form.serialize(),
        timeout: 99999
    }).done(function () {
        $('#loading').hide();
    });
}

function addRemoveUser() {
    var jqProjectAddUser = $('.project-add-user');
    var form = jqProjectAddUser.find("#projectSortableInputForm");
    if (form.find(".has-error").length) {
        return false;
    }
    $('#loading').show();
    $.ajax({
        type: 'POST',
        url: '/project/add-user',
        data: form.serialize(),
        timeout: 99999
    }).done(function () {
        var jqProjectAddUser = $('.project-add-user');
        var form = jqProjectAddUser.find("#projectAdduserform");
        $.pjax.reload({
            type: 'GET',
            url: '/project/add-user',
            container: '#projectSortableView', // id to update content
            data: form.serialize(),
            timeout: 99999
        }).done(function () {
            $('#loading').hide();
        });
    });
}

