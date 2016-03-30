// BULK DELETE
/*$('#multiple_approve_btn_id_equipment').click(function(){
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
					 url: 'index.php?r=equipment/approve-multiple-equipment',
					 data: {equipmentid: pks},
					 success: function(data) {
						//alert("Successfully Accepted");
						$.pjax.reload({container:'#w0'});						  
					 }
				  });
			  //} else { !cancel || cancel(); }
		  //});
	  //}
  } else {
	  alert("No Equipment Selected!");
	  return false;
  }
});*/

// BULK DELETE
$(function() {
	var counter = 0;
	var flag = "Yes";
	var totalworkhours = -1;
	$('#multiple_approve_btn_id_equipment').prop('disabled', true); //TO DISABLED
	$("#w1 input[type=checkbox]").click(function(){
		var checkApproved = $(this).attr("accepted");

		//alert("checkApproved is :　"+checkApproved);
		var model = $(this).attr('model');
		var pks = $('#w1').yiiGridView('getSelectedRows');
		//alert("pks length is ：　"+pks);
		for(var i=0; i < pks.length; i++){
			// get approved value for this timecard
			flag = $("#w1 input[equipmentid="+pks[i]+"]").attr("accepted");
			
			//alert("loop flag is "+flag+" i is : "+i);
			if(flag == "Yes" || flag == "yes"){
				flag = "Yes";
				break;
			}else{
				continue;
			}
		}
		
		if (!pks || pks.length != 0 && flag != "Yes"){
				$('#multiple_approve_btn_id_equipment').prop('disabled', false); //TO ENABLE
				
				// triggered when checkbox selected
				$('#multiple_approve_btn_id_equipment').click(function(){

					  $.ajax({
						 type: 'POST',
						 url: 'index.php?r=equipment/approve-multiple-equipment',
						 data: {equipmentid: pks},
						 success: function(data) {
							  $.pjax.reload({container:'#w0'});
						 }
					  });
				});
			}else {
					$('#multiple_approve_btn_id_equipment').prop('disabled', true);
				}
	}); 
 });
	 