//Taken from commit a09e6b8d7fed3035d888ade56ffd0e1a623f4c00 on PGE-Web
$(function(){	

    unassignedTagCloud = {};
    assignedTagCloud = {};
    unassignedUserArray = [];
    assignedUserArray = [];

	//think this should be moved into the function below
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

    // projectUserUnassignedFilter filter listener
    $(document).off('keypress', '#projectUserUnassignedFilter').on('keypress', '#projectUserUnassignedFilter', function (e) {
        if (e.keyCode === 13 || e.keyCode === 10) {
            e.preventDefault();
            projectUserGridViewUnassingedReload();
        }
    });

    // projectUserAssignedFilter filter listener
    $(document).off('keypress', '#projectUserAssignedFilter').on('keypress', '#projectUserAssignedFilter', function (e) {
        if (e.keyCode === 13 || e.keyCode === 10) {
            e.preventDefault();
            projectUserGridViewAssignedReload();
        }
    });

    $(document).off('click', '#projectUserUnassignedFilterClear').on('click', '#projectUserUnassignedFilterClear', function (){
        a = $('#projectUserUnassignedFilter');
		
        if(a.val()!=""){
            //clear input and trigger keypress on the input to only refresh the connected gridview
            a.val(""); 
            projectUserGridViewUnassingedReload();
        }
    });

    //separate gridview refresh filter 
    $(document).off('click', '#projectUserAssignedFilterClear').on('click', '#projectUserAssignedFilterClear', function (){
        u = $('#projectUserAssignedFilter');

         if(u.val()!=""){
              //clear input and trigger keypress on the input to only refresh the connected gridview
              u.val(""); 
              projectUserGridViewAssignedReload()
        }
    });
	
	//move unassigned to the assigned table
    $(document).off('change', '#unassignedProjectUserGV input[type=checkbox]').on('change', '#unassignedProjectUserGV input[type=checkbox]', function () {
		//get selected users
        unassignedUsers = $("#unassignedProjectUserGV").yiiGridView('getSelectedRows');
		
		//loop users
		unassignedUsers.forEach((user) => {
			//get user row
			currentTableRow = $("tr[data-key ='" + JSON.stringify(user) + "']");
			username = currentTableRow.closest('tr').find('td').eq(0).text();
			//change classname for new table
			currentTableRow.closest('tr').find('td').find('input').removeClass('unAssignedUser').addClass('assignedUser');
			//append to new table
			var row = currentTableRow.closest('tr').html();
			$('#assignedProjectUserGV-container table tbody').prepend('<tr data-key=' + JSON.stringify(user) + '>' + row + '</tr>');
			//remove empty row if it exist
			$('#assignedProjectUserGV-container .empty').closest('tr').remove();
			//remove old row
			currentTableRow.closest('tr').remove();
			//reset select all check box
			$('#unassignedProjectUserGV .select-on-check-all').prop('checked', false);
			
			//remove from unassigned cloud tag
			removeFromUnassignedTagCloud(user);
			
			//add to assigned cloud tag
			addToAssignedTagCloud(user,username);
			toggleCloudVisibility('assignedTagCloud');
			$("#assignedTagCloud").scrollTop($("#assignedTagCloud").children().height());
		})
    });
	
	//move assigned to the unassigned table
    $(document).off('change', '#assignedProjectUserGV input[type=checkbox]').on('change', '#assignedProjectUserGV input[type=checkbox]', function () {
		//get selected users
        assignedUsers = $("#assignedProjectUserGV").yiiGridView('getSelectedRows');
		
		//loop users
		assignedUsers.forEach((user) => {
			//get user row
			currentTableRow = $("tr[data-key ='" + JSON.stringify(user) + "']");
			username = currentTableRow.closest('tr').find('td').eq(0).text();
			//change classname for new table
			currentTableRow.closest('tr').find('td').find('input').removeClass('assignedUser').addClass('unAssignedUser');  
			//append to new table
			var row = currentTableRow.closest('tr').html();
			$('#unassignedProjectUserGV-container table tbody').prepend('<tr data-key=' + JSON.stringify(user) + '>' + row + '</tr>');
			//remove empty row if it exist
			$('#unassignedProjectUserGV-container .empty').closest('tr').remove();
			//remove old row
			currentTableRow.closest('tr').remove();
			//reset select all check box
			$('#assignedProjectUserGV .select-on-check-all').prop('checked', false);
		
			//remove from assigned cloud tag
			removeFromAssignedTagCloud(user);
		
			//add to unassigned cloud tag
			addToUnssignedTagCloud(user,username);
			toggleCloudVisibility('unassignedTagCloud');
			$("#unassignedTagCloud").scrollTop($("#unassignedTagCloud").children().height());
		})
    });

	$(document).on('click','#projectAddUserResetBtn',function(e){

		$('#projectUserUnassignedFilter').val("");
		$('#projectUserAssignedFilter').val("");
		$('#unassignedTagCloud').html("");
		$('#unassignedTagCloud').css({"display":"none"});
		$('#assignedTagCloud').html("");
		$('#assignedTagCloud').css({"display":"none"});
		unassignedUserArray = [];
		assignedUserArray = [];

		//reload both gridviews
		projectUserGridViewReload();
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
				assignedUserArray.push({
					'id' : key,
					'name' : value
				});
			}
		}
	}

	function addToUnssignedTagCloud(key,value){
		tag = "<span id='"+key+"_uCloud' class='roundedTagSpan'>"+value+"</span>";

		if(!$("#"+key+"_uCloud").length > 0){
			if(!assignedTagCloud[key]){
				$('#unassignedTagCloud').append(tag);
				unassignedTagCloud[key] = tag;
				unassignedUserArray.push({
					'id' : key,
					'name' : value
				});
			}
		}
	}
	
	function removeFromUnassignedTagCloud(user)
	{
		//remove from tag cloud
		if(jQuery.inArray(user,unassignedTagCloud)){
			$("#"+user+"_uCloud").remove();
			toggleCloudVisibility('unassignedTagCloud');
		}
		//remove from data array
		unassignedUserArray.forEach(function(value, index){				
			if(value['id'] == user)
			{	
				unassignedUserArray.splice(index,1);
			}
		});	
	}
	
	function removeFromAssignedTagCloud(user)
	{
		//remove from tag cloud
		if(jQuery.inArray(user,assignedTagCloud)){
			$("#"+user+"_aCloud").remove();
			toggleCloudVisibility('assignedTagCloud');
		}
		//remove from data array
		assignedUserArray.forEach(function(value, index){			
			if(value['id'] == user)
			{
				assignedUserArray.splice(index,1);
			}
		});	
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
			unassignedUserArray = [];
			assignedUserArray = [];
			//reload both tables
			projectUserGridViewReload();
		});
	}

	//want to look into a cleaner way to do this.	
	function projectUserGridViewReload() {
		var form = $("#projectUserForm");
		$('#loading').show();
		$.pjax.reload({
			container: "#unassignedProjectUserGridView",
			timeout: 99999,
			url: form.attr("action"),
			type: "GET",
			data: form.serialize()
		}).done(function () {
			updateUnassingedUsersFromCloudTag();
			projectUserGridViewAssignedReload();
		});
	}
	
	function projectUserGridViewUnassingedReload() {
		var form = $("#projectUserForm");
		$('#loading').show();
		$.pjax.reload({
			container: "#unassignedProjectUserGridView",
			timeout: 99999,
			url: form.attr("action"),
			type: "GET",
			data: form.serialize()
		}).done(function () {
			updateUnassingedUsersFromCloudTag();
			$('#loading').hide();
		});
	}

	function projectUserGridViewAssignedReload() {
		var form = $("#projectUserForm");
		$('#loading').show();
		$.pjax.reload({
			container: "#assignedProjectUserGridView",
			timeout: 99999,
			url: form.attr("action"),
			type: "GET",
			data: form.serialize()
		}).done(function () {
			updateAssignedUsersFromCloudTag();
			$('#loading').hide();
		});
	}
	
	function updateUnassingedUsersFromCloudTag()
	{
		//get filter value
		filterString = $('#projectUserUnassignedFilter').val().toLowerCase();
		//remove items that have been assigned
		assignedUserArray.forEach(function(user){
			//remove row
			$("#unassignedProjectUserGV tr[data-key ='" + user.id + "']").closest('tr').remove();			
		});
		//add items that have been unassigned
		unassignedUserArray.forEach(function(user){				
			//check if item should be displayed with filter
			if(user.name.toLowerCase().indexOf(filterString) !== -1)
			{				
				//remove empty row if it exist
				$('#unassignedProjectUserGV .empty').closest('tr').remove();
				//append to new table
				var row = currentTableRow.closest('tr').html();
				$('#unassignedProjectUserGV table tbody').prepend(
					'<tr data-key=' + user.id + '>' + 
						'<td data-col-seq="0">' + user.name + '</td>' + 
						'<td class="skip-export kv-align-center kv-align-middle kv-row-select" style="width:50px;" data-col-seq="1">' +
						'<input type="checkbox" class="unAssignedUser kv-row-checkbox" name="selection[]" value="' + user.id + '" userid="' + user.id + '"></td>' +
					'</tr>'
				);
			}
		});	
	}
	
	function updateAssignedUsersFromCloudTag()
	{
		//get filter value
		filterString = $('#projectUserAssignedFilter').val().toLowerCase();
		//remove items that have been unassigned
		unassignedUserArray.forEach(function(user){
			//remove row
			$("#assignedProjectUserGV tr[data-key ='" + JSON.stringify(user.id) + "']").closest('tr').remove();			
		});
		//add items that have been assigned
		assignedUserArray.forEach(function(user){
			//check if item should be displayed with filter
			if(user.name.toLowerCase().indexOf(filterString) !== -1)
			{				
				//remove empty row if it exist
				$('#assignedProjectUserGV .empty').closest('tr').remove();
				//append to new table
				var row = currentTableRow.closest('tr').html();
				$('#assignedProjectUserGV table tbody').prepend(
					'<tr data-key=' + user.id + '>' + 
						'<td data-col-seq="0">' + user.name + '</td>' + 
						'<td class="skip-export kv-align-center kv-align-middle kv-row-select" style="width:50px;" data-col-seq="1">' +
						'<input type="checkbox" class="assignedUser kv-row-checkbox" name="selection[]" value="' + user.id + '" userid="' + user.id + '"></td>' +
					'</tr>'
				);
			}
		});
	}
});

