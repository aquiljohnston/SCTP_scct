$(function(){
	$('#mileageModalButtonSunday').click(function(){
	// get the click of the create button
		$('#mileageModalSunday').modal('show')
			.find('#modalContentMileageSunday')
			.load($(this).attr('value'));
	});
	
	$('#mileageModalButtonMonday').click(function(){
	// get the click of the create button
		$('#mileageModalMonday').modal('show')
			.find('#modalContentMileageMonday')
			.load($(this).attr('value'));
	});

	$('#mileageModalButtonTuesday').click(function(){
	// get the click of the create button
		$('#mileageModalTuesday').modal('show')
			.find('#modalContentMileageTuesday')
			.load($(this).attr('value'));
	});
	
	$('#mileageModalButtonWednesday').click(function(){
	// get the click of the create button
		$('#mileageModalWednesday').modal('show')
			.find('#modalContentMileageWednesday')
			.load($(this).attr('value'));
	});
	
	$('#mileageModalButtonThursday').click(function(){
	// get the click of the create button
		$('#mileageModalThursday').modal('show')
			.find('#modalContentMileageThursday')
			.load($(this).attr('value'));
	});
	
	$('#mileageModalButtonFriday').click(function(){
	// get the click of the create button
		$('#mileageModalFriday').modal('show')
			.find('#modalContentMileageFriday')
			.load($(this).attr('value'));
	});
	
	$('#mileageModalButtonSaturday').click(function(){
	// get the click of the create button
		$('#mileageModalSaturday').modal('show')
			.find('#modalContentMileageSaturday')
			.load($(this).attr('value'));
	})
});