// BULK DELETE
	  $('#multiple_approve_btn_id').click(function(){
	  //$('#multiple_approve_btn_id').on('click',function() {
		  var model = $(this).attr('model');
		  var pks = $('#w1').yiiGridView('getSelectedRows');
		  alert("pks is :　"+pks);
		  if (!pks || 0 !== pks.length) {
		  alert("get in if statement");
			  //yii.confirm = function(message, ok, cancel) {
					alert("get in yii comfirm");
				  //bootbox.confirm(message, function(result) {
					  alert("before calling ajax");
					  //if (result) {
						  $.ajax({
						     url: '/timecard/actionApproveM',
						     data: {id: pks},
						     success: function(data) {
								  $.pjax.reload({container:'#w0'});
						     }
						  });
					  //} else { !cancel || cancel(); }*/
				  //});
			  //}
		  } else {
			  alert("Aucune ligne sélectionnée<br/>Veuillez sélectionner au moins un enregistrement!");
			  return false;
		  }
	  });
	 
	 /*$(document).ready(function(){
		$('#multiple_approve_btn_id').click(function(){

			var HotId = $('#w1').yiiGridView('getSelectedRows');
			alert("call ajax function");
			   $.ajax({
				 type: \'POST\',
				 url : \'index.php?r=hotel/multiple-delete\',
				 data : {row_id: HotId},
				 success : function() {
				   $(this).closest(\'tr\').remove(); //or whatever html you use for displaying rows
				 }
			 });

		 });
    });*/