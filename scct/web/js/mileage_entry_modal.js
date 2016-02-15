$(function(){
	$('#MileagemodalButtonSunday').click(function(){
	// get the click of the create button
		$('#MileagemodalSunday').modal('show')
			.find('#modalContentMileageSunday')
			.load($(this).attr('value'));
	});
	
	/*$('#modalButtonMonday').click(function(){
	// get the click of the create button
		$('#modalMonday').modal('show')
			.find('#modalContentMonday')
			.load($(this).attr('value'));
	})
	
	$('#modalButtonTuesday').click(function(){
	// get the click of the create button
		$('#modalTuesday').modal('show')
			.find('#modalContentTuesday')
			.load($(this).attr('value'));
	})
	
	$('#modalButtonWednesday').click(function(){
	// get the click of the create button
		$('#modalWednesday').modal('show')
			.find('#modalContentWednesday')
			.load($(this).attr('value'));
	})
	
	$('#modalButtonThursday').click(function(){
	// get the click of the create button
		$('#modalThursday').modal('show')
			.find('#modalContentThursday')
			.load($(this).attr('value'));
	})
	
	$('#modalButtonFriday').click(function(){
	// get the click of the create button
		$('#modalFriday').modal('show')
			.find('#modalContentFriday')
			.load($(this).attr('value'));
	})
	
	$('#modalButtonSaturday').click(function(){
	// get the click of the create button
		$('#modalSaturday').modal('show')
			.find('#modalContentSaturday')
			.load($(this).attr('value'));
	})*/
});