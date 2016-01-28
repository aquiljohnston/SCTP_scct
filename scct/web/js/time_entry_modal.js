$(function(){
	$('#modalButton').click(function(){
	// get the click of the edit button
		$('#modal').modal('show')
			.find('#modalContent')
			.load($(this).attr('value'));
	})
})