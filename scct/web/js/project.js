//Taken from commit a09e6b8d7fed3035d888ade56ffd0e1a623f4c00 on PGE-Web
$(function(){	

    unAssignedUsersArray = [];
    assignedUsersArray   = [];

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

     // projectFilterAssigned filter listener
     $(document).off('keypress', '#projectFilterAssigned').on('keypress', '#projectFilterAssigned', function (e) {
         if (e.keyCode === 13 || e.keyCode === 10) {
             e.preventDefault();
             projectGridViewAssignedReload();
         }
     });

    $(document).off('click', '#projectSearchCleanFilterButton').on('click', '#projectSearchCleanFilterButton', function (){
        a = $('#projectFilter');
        //u = $('.projectFilterAssigned')
         
        /*if(u.val()!=""){
              //clear input and trigger keypress on the input to only refresh the connected gridview
              //not both grid views
              u.val(""); 
              projectGridViewReload()
         
        }*/
         if(a.val()!=""){
              //clear input and trigger keypress on the input to only refresh the connected gridview
              //not both grid views
              a.val(""); 
              projectGridViewReload()
     
        }
    });

    //separate gridview refresh filter 
    $(document).off('click', '.assignedSearchCleanFilterButton').on('click', '.assignedSearchCleanFilterButton', function (){
        u = $('#projectFilterAssigned');

         if(u.val()!=""){
              //clear input and trigger keypress on the input to only refresh the connected gridview
              //not both grid views
              u.val(""); 
              projectGridViewAssignedReload()
     
        }
    });


///move unassigned to the assigned table
$(document).on('change','.moveToAssigned', function (e) {

    if($(this).is(":checked")){
     //change classname for the return trip
     $(this).removeClass('moveToAssigned').addClass('moveToUnAssigned'); 
     var row = $(this).closest('tr').html();
     $('#assignedGV-container table tbody').prepend('<tr>'+row+'</tr>');
     $(this).closest('tr').remove();
    }
});

//move assigned to the unassigned table
$(document).on('change','.moveToUnAssigned', function (e) {

    if($(this).is(":checked")){
    //change classname for the return trip
     $(this).removeClass('moveToUnAssigned').addClass('moveToAssigned');    
     var row = $(this).closest('tr').html();
     $('#unAssignedGV-container table tbody').prepend('<tr>'+row+'</tr>');
     $(this).closest('tr').remove();
    }
   
});

$(document).on('click','#projectAddUserResetBtn',function(e){

    $('#projectFilter').val("");
    $('.projectFilterAssigned').val("");

    //add boolean flag means to refresh both grid views
    //if true will call both reload routines in succession
    //if not only one grid view will refresh
    projectGridViewReload(true);

})


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
    var jqProjectAddUser    = $('.project-add-user');
    var form                = jqProjectAddUser.find("#projectSortableInputForm");
    var projectID           = $('#projectID').val();
    var unassignedVals      = [];
    var assignedVals        = [];
   // var csrf_param          = $("meta[name=csrf-param]");   
    //var csrf_token          = $("meta[name=csrf-token]");



    if (form.find(".has-error").length) {
        return false;
    }

     //populate unassigned userid values
    $(".moveToAssigned").each(function(key,value){unassignedVals.push($(this).val());})
     //unAssignedUsersArray.push({unassignedVals});

    //populate assigned userid values
    $(".moveToUnAssigned").each(function(key,value){assignedVals.push($(this).val());})
      //assignedUsersArray.push({assignedVals});

    data = {
        assignedUsers:assignedVals.join(','),
        unassignedUsers:unassignedVals.join(','),
        projectID: projectID
    }

   console.log('DATA',data);

    //return false;


    $('#loading').show();
    $.ajax({
        type: 'POST',
        url: '/project/add-user',
        //container: '#projectSortableView', // id to update content
        data: data,
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

function projectGridViewReload(both=false) {
    var form = $("#projectForm");
    $('#loading').show();
    $.pjax.reload({
        container: "#projectGridView",
        timeout: 99999,
        url: form.attr("action"),
        type: "GET",
        data: form.serialize()
    }).done(function () {
        $('#loading').hide();
        //special condition for reset button
        if(both){
          projectGridViewAssignedReload();
        }
    });
}

function projectGridViewAssignedReload() {
    var form = $("#projectForm");
    $('#loading').show();
    $.pjax.reload({
        container: "#projectGridViewAssigned",
        timeout: 99999,
        url: form.attr("action"),
        type: "GET",
        data: form.serialize()
    }).done(function () {
        $('#loading').hide();
    });
}




});

