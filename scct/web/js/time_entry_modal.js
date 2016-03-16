$(function(){
	
	// Modal view for Sunday
	$('#modalButtonSunday').click(function(){
	// get the click of the create button
		$('#modalSunday').modal('show')
			.find('#modalContentSunday')
			.load($(this).attr('value'));
	});	
	$('#modalSunday').on('hidden.bs.modal', function (e) {
	  // reload page when modal closed
	  location.reload(true);
	});
	
	// Modal view for Monday
	$('#modalButtonMonday').click(function(){
	// get the click of the create button
		$('#modalMonday').modal('show')
			.find('#modalContentMonday')
			.load($(this).attr('value'));
	});		
	$('#modalMonday').on('hidden.bs.modal', function (e) {
	  // reload page when modal closed
	  location.reload(true);
	});

	// Modal view for Tuesday
	$('#modalButtonTuesday').click(function(){
	// get the click of the create button
		$('#modalTuesday').modal('show')
			.find('#modalContentTuesday')
			.load($(this).attr('value'));
	});	
	$('#modalTuesday').on('hidden.bs.modal', function (e) {
	  // reload page when modal closed
	  location.reload(true);
	});	
	
	// Modal view for Wednesday
	$('#modalButtonWednesday').click(function(){
	// get the click of the create button
		$('#modalWednesday').modal('show')
			.find('#modalContentWednesday')
			.load($(this).attr('value'));
	});	
	$('#modalWednesday').on('hidden.bs.modal', function (e) {
	  // reload page when modal closed
	  location.reload(true);
	});
	
	// Modal view for Thursday
	$('#modalButtonThursday').click(function(){
	// get the click of the create button
		$('#modalThursday').modal('show')
			.find('#modalContentThursday')
			.load($(this).attr('value'));
	})	
	$('#modalThursday').on('hidden.bs.modal', function (e) {
	  // reload page when modal closed
	  location.reload(true);
	});	
	
	// Modal view for Friday
	$('#modalButtonFriday').click(function(){
	// get the click of the create button
		$('#modalFriday').modal('show')
			.find('#modalContentFriday')
			.load($(this).attr('value'));
	});	
	$('#modalFriday').on('hidden.bs.modal', function (e) {
	  // reload page when modal closed
	  location.reload(true);
	});	
	
	// Modal view for Saturday
	$('#modalButtonSaturday').click(function(){
	// get the click of the create button
		$('#modalSaturday').modal('show')
			.find('#modalContentSaturday')
			.load($(this).attr('value'));
	});	
	$('#modalSaturday').on('hidden.bs.modal', function (e) {
	  // reload page when modal closed
	  location.reload(true);
	});
});