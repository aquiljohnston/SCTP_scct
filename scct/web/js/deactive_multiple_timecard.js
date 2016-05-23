// BULK DELETE
$(function() {
	var checkDeActiveArray = [];
	var counter = 0;
	var flag = "Yes";
	var totalworkhours = -1;
	$('#deactive_timeEntry_btn').prop('disabled', true); //TO DISABLED
	$(".kv-row-select input[type=checkbox]").click(function(){
		var checkDeActive = $(this).attr("activeStatus");
		checkDeactiveArray[counter++] = checkApproved;

		//alert("checkApproved is :　"+checkApproved);
		//var model = $(this).attr('model');
		var pks = $('#w0').yiiGridView('getSelectedRows');
		//alert("pks length is ：　"+pks.length);
		for(var i=0; i < pks.length; i++){
			// get approved value for this timecard
			flag = $(".kv-row-select input[timecardid="+pks[i]+"]").attr("approved");
			
			// get totalworkhours for this timecard
			totalworkhours = $(".kv-row-select input[timecardid="+pks[i]+"]").attr("totalworkhours");
			
			//alert("loop flag is "+flag+" i is : "+i);
			if(flag == "Yes" || totalworkhours == .0 || flag == "yes"){
				flag = "Yes";
				break;
			}else{
				continue;
			}
		}
		
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
	 