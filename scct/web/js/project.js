//Taken from commit a09e6b8d7fed3035d888ade56ffd0e1a623f4c00 on PGE-Web
$(function(){
    $('#projectAddUserSubmitBtn').on('click',function(){
        $(this).val('Please wait ...')
            .attr('disabled','disabled');
        $('#projectAddUserResetBtn').attr('disabled','disabled');
        $('#projectAdduserform').submit();
    });
    $('#projectAddModuleSubmitBtn').on('click',function(){
        $(this).val('Please wait ...')
            .attr('disabled','disabled');
        $('#projectAddModuleResetBtn').attr('disabled','disabled');
        $('#projectAddModuleform').submit();
    });
})

