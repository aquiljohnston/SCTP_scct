//Taken from commit a09e6b8d7fed3035d888ade56ffd0e1a623f4c00 on PGE-Web
$(function(){	

	var environment = getSubDomainEnvironment();
	
	//autofill url prefix based on project name
	$('#projectName').keyup(function(){
		//if length is greater than 20 then use acronym+environment instead
		if(($(this).val() + environment).length < 20){
			$('#urlPrefix').val($(this).val().toLowerCase().replace(/\s/g, '') + environment);
		}else{			
			var acronym = $(this).val().toLowerCase().match(/\b\w/g).join('');
			$('#urlPrefix').val(acronym + environment);
		}
	});
	
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
             projectGridViewReload();
         }
     });

    $(document).off('click', '#projectSearchCleanFilterButton').on('click', '#projectSearchCleanFilterButton', function (){
        $('#projectSearchField').val("");
        projectGridViewReload();
    });
});

///move unassigned to the assigned table
$('#unassignedTableGrid').on('change','.moveToAssigned', function (e) {

    if($(this).is(":checked")){
     //change classname for the return trip
     $(this).removeClass('moveToAssigned').addClass('moveToUnAssigned'); 
     var row = $(this).closest('tr').html();
     $('#assignedGV-container table tbody').prepend('<tr>'+row+'</tr>');
     $(this).closest('tr').remove();
    }
});

//move assigned to the unassigned table
$('#assignedTableGrid').on('change','.moveToUnAssigned', function (e) {

    if($(this).is(":checked")){
    //change classname for the return trip
     $(this).removeClass('moveToUnAssigned').addClass('moveToAssigned');    
     var row = $(this).closest('tr').html();
     $('#unAssignedGV-container table tbody').prepend('<tr>'+row+'</tr>');
     $(this).closest('tr').remove();
    }
   
});



function getSubDomainEnvironment() {
	//get environment variable
	var urlPrefix = location.hostname.split( '.' )[0];
	var environment = "";
	
	if (urlPrefix.indexOf("dev") >= 0 || urlPrefix.indexOf("localhost") >= 0){
		environment = "dev";
	}
	if (urlPrefix.indexOf("stage") >= 0){
		environment = "stage";
	}
	
	return environment;
}

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

    //stuff = form.serializeArray();

   // console.log(stuff); return false;

    $('#loading').show();
    $.ajax({
        type: 'POST',
        url: '/project/add-user',
        //container: '#projectSortableView', // id to update content
        data: form.serialize(),
        timeout: 99999
    }).done(function () {
        $('#loading').hide();
        /*var jqProjectAddUser = $('.project-add-user');
        var form = jqProjectAddUser.find("#projectAdduserform");
        $.pjax.reload({
            type: 'GET',
            url: '/project/add-user',
            container: '#projectSortableView', // id to update content
            data: form.serialize(),
            timeout: 99999
        }).done(function () {
            $('#loading').hide();
        });*/
    });
}

function projectGridViewReload() {
    var form = $("#projectForm");
    $('#loading').show();
    $.pjax.reload({
        container: "#projectGridview",
        timeout: 99999,
        url: form.attr("action"),
        type: "GET",
        data: form.serialize()
    }).done(function () {
    });
    $('#projectGridview').on('pjax:success', function (event, data, status, xhr, options) {
        $('#loading').hide();
    });
    $('#projectGridview').on('pjax:error', function (event, data, status, xhr, options) {
        console.log("Error");
    });
}

