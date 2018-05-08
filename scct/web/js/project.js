//Taken from commit a09e6b8d7fed3035d888ade56ffd0e1a623f4c00 on PGE-Web
$(function(){	

    unassignedTagCloud = {};
    assignedTagCloud = {};

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
        $(this).val('Please wait ...').attr('disabled','disabled');
        $('#projectAddUserResetBtn').attr('disabled','disabled');
        addRemoveUser();
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
		
        if(a.val()!=""){
            //clear input and trigger keypress on the input to only refresh the connected gridview
            //not both grid views
            a.val(""); 
            projectGridViewReload();     
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
	
	//move unassigned to the assigned table
    $(document).off('change', '#unAssignedGV input[type=checkbox]').on('change', '#unAssignedGV input[type=checkbox]', function () {
		//get selected users
        unassignedUsers = $("#unAssignedGV").yiiGridView('getSelectedRows');
		
		//loop users
		unassignedUsers.forEach((user) => {
			//get user row
			currentTableRow = $("tr[data-key ='" + JSON.stringify(user) + "']");
			username = currentTableRow.closest('tr').find('td').eq(0).text();
			//change classname for new table
			currentTableRow.closest('tr').find('td').find('input').removeClass('unAssignedUser').addClass('assignedUser');
			//append to new table
			var row = currentTableRow.closest('tr').html();
			$('#assignedGV-container table tbody').prepend('<tr data-key=' + JSON.stringify(user) + '>' + row + '</tr>');
			//remove empty row if it exist
			$('#assignedGV-container .empty').closest('tr').remove();
			//remove old row
			currentTableRow.closest('tr').remove();
			//reset select all check box
			$('#unAssignedGV .select-on-check-all').prop('checked', false);
			
			//remove from unassigned cloud tag
			if(jQuery.inArray(user,assignedTagCloud)){
				$("#"+user+"_uCloud").remove();
				toggleCloudVisibility('unassignedTagCloud');
			}
			
			//add to assigned cloud tag
			addToAssignedTagCloud(user,username);
			toggleCloudVisibility('assignedTagCloud');
			$("#assignedTagCloud").scrollTop($("#assignedTagCloud").children().height());
		})
    });
	
	//move assigned to the unassigned table
    $(document).off('change', '#assignedGV input[type=checkbox]').on('change', '#assignedGV input[type=checkbox]', function () {
		//get selected users
        assignedUsers = $("#assignedGV").yiiGridView('getSelectedRows');
		
		//loop users
		assignedUsers.forEach((user) => {
			//get user row
			currentTableRow = $("tr[data-key ='" + JSON.stringify(user) + "']");
			username = currentTableRow.closest('tr').find('td').eq(0).text();
			//change classname for new table
			currentTableRow.closest('tr').find('td').find('input').removeClass('assignedUser').addClass('unAssignedUser');  
			//append to new table
			var row = currentTableRow.closest('tr').html();
			$('#unAssignedGV-container table tbody').prepend('<tr data-key=' + JSON.stringify(user) + '>' + row + '</tr>');
			//remove empty row if it exist
			$('#unAssignedGV-container .empty').closest('tr').remove();
			//remove old row
			currentTableRow.closest('tr').remove();
			//reset select all check box
			$('#assignedGV .select-on-check-all').prop('checked', false);
		
			//remove from unassigned cloud tag
			if(jQuery.inArray(user,unassignedTagCloud)){
				$("#"+user+"_aCloud").remove();
				toggleCloudVisibility('assignedTagCloud');
			}
		
			//add to assigned cloud tag
			addToUnssignedTagCloud(user,username);
			toggleCloudVisibility('unassignedTagCloud');
			$("#unassignedTagCloud").scrollTop($("#unassignedTagCloud").children().height());
		})
    });

	$(document).on('click','#projectAddUserResetBtn',function(e){

		$('#projectFilter').val("");
		$('#projectFilterAssigned').val("");
		$('#unassignedTagCloud').html("");
		$('#unassignedTagCloud').css({"display":"none"})
		$('#assignedTagCloud').html("");
		$('#assignedTagCloud').css({"display":"none"})

		//add boolean flag means to refresh both grid views
		//if true will call both reload routines in succession
		//if not only one grid view will refresh
		projectGridViewReload(true);
	})

	function toggleCloudVisibility(cloud){
		if ( $('#'+cloud).children().length > 0 ) {
			$('#'+cloud).css({"display":"block"})
		}
		else{
			$('#'+cloud).css({"display":"none"});
		}
	}

	function addToAssignedTagCloud(key,value){
		tag = "<span id='"+key+"_aCloud' class='roundedTagSpan'>"+value+"</span>";

		if(!$("#"+key+"_aCloud").length > 0){
			if(!unassignedTagCloud[key]){
				$('#assignedTagCloud').append(tag);
				assignedTagCloud[key] = tag;
			}
		}
	}

	function addToUnssignedTagCloud(key,value){
		tag = "<span id='"+key+"_uCloud' class='roundedTagSpan'>"+value+"</span>";

		if(!$("#"+key+"_uCloud").length > 0){
			if(!assignedTagCloud[key]){
				$('#unassignedTagCloud').append(tag);
				unassignedTagCloud[key] = tag;
			}
		}
	}

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
		var projectID = $('#projectID').val();
		var unassignedVals = [];
		var assignedVals = [];

		if (form.find(".has-error").length) {
			return false;
		}

		//populate unassigned userid values
		$(".unAssignedUser").each(function(key,value){unassignedVals.push($(this).val());})

		//populate assigned userid values
		$(".assignedUser").each(function(key,value){assignedVals.push($(this).val());})

		data = {
			assignedUsers:assignedVals.join(','),
			unassignedUsers:unassignedVals.join(','),
			projectID: projectID
		}
		
		$('#loading').show();
		$.ajax({
			type: 'POST',
			url: '/project/add-user',
			data: data,
			timeout: 99999
		}).done(function () {
			//reset both buttons
			$('#projectAddUserSubmitBtn').attr('disabled', false);
			$('#projectAddUserResetBtn').attr('disabled', false);
			//reset 'cloud' data
			$('#assignedTagCloud').html("");
			$('#assignedTagCloud').css({"display":"none"})
			$('#unassignedTagCloud').html("");
			$('#unassignedTagCloud').css({"display":"none"})
			unassignedTagCloud = {};
			assignedTagCloud = {};
			//reload both tables
			projectGridViewReload(true);
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
			//special condition for reset button
			if(both){
				projectGridViewAssignedReload();
			}else{
				$('#loading').hide();
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

