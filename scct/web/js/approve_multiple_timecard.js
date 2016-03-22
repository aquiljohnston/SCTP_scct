// BULK DELETE
$(function() {
	var checkApprovedArray = [];
	var counter = 0;
	var flag = "Yes";
	var totalworkhours = -1;
	$('#multiple_approve_btn_id').prop('disabled', true); //TO DISABLED
	$("#w1 input[type=checkbox]").click(function(){
		var checkApproved = $(this).attr("approved");
		checkApprovedArray[counter++] = checkApproved;

		//alert("checkApproved is :　"+checkApproved);
		var model = $(this).attr('model');
		var pks = $('#w1').yiiGridView('getSelectedRows');
		//alert("pks length is ：　"+pks);
		for(var i=0; i < pks.length; i++){
			// get approved value for this timecard
			flag = $("#w1 input[timecardid="+pks[i]+"]").attr("approved");
			// get totalworkhours for this timecard
			totalworkhours = $("#w1 input[timecardid="+pks[i]+"]").attr("totalworkhours");
			//alert("loop flag is "+flag+" i is : "+i);
			if(flag == "Yes" || totalworkhours < 0 || flag == "yes"){
				flag = "Yes";
				break;
			}else{
				//flag = "No";
				continue;
			}
		}
		//alert("flag is : "+ flag);
		//if (!pks || pks.length != 0 && checkApproved != "Yes"){
		if (!pks || pks.length != 0 && flag != "Yes"){
				$('#multiple_approve_btn_id').prop('disabled', false); //TO ENABLE
				
				// triggered when checkbox selected
				$('#multiple_approve_btn_id').click(function(){

					  $.ajax({
						 type: 'POST',
						 url: 'index.php?r=time-card/approve-multiple',
						 data: {timecardid: pks},
						 success: function(data) {
							  $.pjax.reload({container:'#w0'});
						 }
					  });
				});
			}else {
					$('#multiple_approve_btn_id').prop('disabled', true);
				}
	}); 
 });
	 