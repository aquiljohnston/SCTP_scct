// BULK DELETE
$(function() {
	var counter = 0;
	var flag = "Yes";
	var totalworkhours = -1;
	$('#multiple_approve_btn_id_equipment').prop('disabled', true); //TO DISABLED
	$('.select-on-check-all').change(function() {
		var checkApproved = $(this).attr("accepted");

		//alert("checkApproved is :　"+checkApproved);
		var model = $(this).attr('model');
		var pks = $('#w0').yiiGridView('getSelectedRows');
		//alert("pks length is ：　"+pks);
		for(var i=0; i < pks.length; i++){
			// get approved value for this timecard
			flag = $(".kv-row-select input[equipmentid="+pks[i]+"]").attr("accepted");
			
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
				var confirmBox = confirm('Are you sure you want to approve this item?');
					if(confirmBox){
					
						$.ajax({
							type: 'POST',
							url: '/equipment/approve-multiple-equipment',
							data: {equipmentid: pks},
							success: function(data) {
								$.pjax.reload({container:'#w0'});
							}
						});
					}else{
						e.stopImmediatePropagation();
						e.preventDefault();
					}   
				});
			}else {
					$('#multiple_approve_btn_id_equipment').prop('disabled', true);
				}
	});
	
	$(".kv-row-select input[type=checkbox]").click(function(){
		var checkApproved = $(this).attr("accepted");

		//alert("checkApproved is :　"+checkApproved);
		var model = $(this).attr('model');
		var pks = $('#w0').yiiGridView('getSelectedRows');
		//alert("pks length is ：　"+pks);
		for(var i=0; i < pks.length; i++){
			// get approved value for this timecard
			flag = $(".kv-row-select input[equipmentid="+pks[i]+"]").attr("accepted");
			
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
				$('#multiple_approve_btn_id_equipment').click(function(e){
				var confirmBox = confirm('Are you sure you want to approve this item?');
					if(confirmBox){

					  $.ajax({
						 type: 'POST',
						 url: '/equipment/approve-multiple-equipment',
						 data: {equipmentid: pks},
						 success: function(data) {
							  $.pjax.reload({container:'#w0'});
						 }
					  });
					}else{
						e.stopImmediatePropagation();
						e.preventDefault();
					} 
				});
			}else {
					$('#multiple_approve_btn_id_equipment').prop('disabled', true);
				}
	}); 
 });
	 