$(function(){
	$(document).off('keyup', '.modal.bootstrap-dialog.type-warning').on('keyup', '.modal.bootstrap-dialog.type-warning', function (e) {
		if (e.keyCode === 27) {
			$('.modal.bootstrap-dialog.type-warning').remove();
			$('.modal-backdrop.fade.in').remove();
        }
    });
	
	$(document).click(function(event) { 
		if($('.modal.bootstrap-dialog.type-warning').length){
			if(!$(event.target).closest('.modal-content').length) {
				$('.modal.bootstrap-dialog.type-warning').remove();
				$('.modal-backdrop.fade.in').remove();
			}  
		}			
	});
});