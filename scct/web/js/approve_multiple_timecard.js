// BULK DELETE
$('#multiple_approve_btn_id').click(function(){
//$('#multiple_approve_btn_id').on('click',function() {
  var model = $(this).attr('model');
  var pks = $('#w1').yiiGridView('getSelectedRows');//.attr("timecardid");
  var TimeCardIDArray = [];
  
  //alert("key is :　"+pks);

  if (!pks || 0 !== pks.length) {
	  //yii.confirm = function(message, ok, cancel) {
		  //bootbox.confirm(message, function(result) {
			  //if (result) {
				  $.ajax({
					 type: 'POST',
					 url: 'index.php?r=time-card/approve-multiple',//-multiple',//'.\yii\helpers\Url::to(['/controller/action']).'
					 data: {timecardid: pks},
					 success: function(data) {
						//alert("Successfully Approved");
						  $.pjax.reload({container:'#w0'});
					 }
				  });
			  //} else { !cancel || cancel(); }
		  //});
	  //}
  } else {
	  alert("No TimeCard Selected!");
	  return false;
  }
});
	 