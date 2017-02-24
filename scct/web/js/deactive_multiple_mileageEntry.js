// BULK DELETE
$(function() {
	var checkDeActiveArray = [];
	var flag = "Yes";
	var approved = "No";
	$('#deactive_mileageEntry_btn_id').prop('disabled', true); //TO DISABLED
	
	$(".select-on-check-all").click(function(){

		var pks = $('.grid-view').yiiGridView('getSelectedRows');
		
		// get approved value for this mileagecard
			approved = $(".mileagecard-view").attr("approved");

		for(var i=0; i < pks.length; i++){
			// get activeStatus value for this timecard
			flag = $(".grid-view input[mileageentryid="+pks[i]+"]").attr("activeStatus");
			
			//alert("loop flag is "+flag+" i is : "+i);
			if(flag == "Inactive"){
				flag = "Inactive";
				break;
			}else{
				continue;
			}
		}
		
		if (!pks || pks.length != 0 && flag != "Inactive" && approved != "Yes"){
				$('#deactive_mileageEntry_btn_id').prop('disabled', false); //TO ENABLE
				
				// triggered when checkbox selected
				$('#deactive_mileageEntry_btn_id').click(function(e){
					var confirmBox = confirm('Are you sure you want to deactivate this item?');
					
					if(confirmBox){
						$.ajax({
							type: 'POST',
							url: '/mileage-card/deactivate',
							data: {timecardid: pks},
							beforeSend: function(  ) {
								console.log(pks);
							},
							success: function(data) {
								$.pjax.reload({container:'.grid-view'});
							}
						});
					}else{
						e.stopImmediatePropagation();
						e.preventDefault();
					}  
				});
			}else {
					$('#deactive_mileageEntry_btn_id').prop('disabled', true);
				}
	});
	
	$(".mileagecard-view input[type=checkbox]").click(function(){

		var pks = $('.grid-view').yiiGridView('getSelectedRows');
		
		// get approved value for this mileagecard
			approved = $(".mileagecard-view").attr("approved");

		for(var i=0; i < pks.length; i++){
			// get activeStatus value for this timecard
			flag = $(".grid-view input[mileageentryid="+pks[i]+"]").attr("activeStatus");
			
			//alert("loop flag is "+flag+" i is : "+i);
			if(flag == "Inactive"){
				flag = "Inactive";
				break;
			}else{
				continue;
			}
		}
		
		if (!pks || pks.length != 0 && flag != "Inactive" && approved != "Yes"){
				$('#deactive_mileageEntry_btn_id').prop('disabled', false); //TO ENABLE
				
				// triggered when checkbox selected
				$('#deactive_mileageEntry_btn_id').click(function(e){
					var confirmBox = confirm('Are you sure you want to deactivate this item?');
					
						if(confirmBox){
							$.ajax({
							type: 'POST',
							url: '/mileage-card/deactivate',
							data: {timecardid: pks},
							beforeSend: function(  ) {
								console.log(pks);
							},
							success: function(data) {
							  $.pjax.reload({container:'.grid-view'});
							}
						});
					}else{
						e.stopImmediatePropagation();
						e.preventDefault();
					}   
				});
			}else {
					$('#deactive_mileageEntry_btn_id').prop('disabled', true);
				}
	}); 
 });
	 