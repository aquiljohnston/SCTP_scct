// BULK DELETE
$('#multiple_approve_btn_id').click(function(){
  var model = $(this).attr('model');
  var pks = $('#w1').yiiGridView('getSelectedRows');
  var TimeCardIDArray = [];
  
  //alert("key is :　"+pks);

  if (!pks || 0 !== pks.length) {
	  $.ajax({
		 type: 'POST',
		 url: 'index.php?r=time-card/approve-multiple',
		 data: {timecardid: pks},
		 success: function(data) {
			  $.pjax.reload({container:'#w0'});
		 }
	  });
  } else {
	  return false;
  }
});
	 