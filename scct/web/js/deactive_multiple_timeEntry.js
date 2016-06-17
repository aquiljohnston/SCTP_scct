// BULK DELETE
$(function() {
	var checkDeActiveArray = [];
	var counter = 0;
	var flag = "Yes";
	var approved = "No"
	var totalworkhours = -1;
	
	$('#deactive_timeEntry_btn_id').prop('disabled', true); //TO DISABLED
	$('.select-on-check-all').change(function() {

		// get approved value for this timecard
		approved = $(".timecard-view").attr("approved");
			
		var pks = $('.grid-view').yiiGridView('getSelectedRows');

		for(var i=0; i < pks.length; i++){
			// get activeStatus value for this timecard
			flag = $(".grid-view input[timeentryid="+pks[i]+"]").attr("activeStatus");
			
			// get totalworkhours for this timecard
			totalworkhours = $(".grid-view input[timeentryid="+pks[i]+"]").attr("totalworkhours");
			
			//alert("loop flag is "+flag+" i is : "+i);
			if(flag == "Inactive" || totalworkhours == .0){
				flag = "Inactive";
				break;
			}else{
				continue;
			}
		}
		
		if (!pks || pks.length != 0 && flag != "Inactive" && approved != "Yes"){
				$('#deactive_timeEntry_btn_id').prop('disabled', false); //TO ENABLE
				
				// triggered when checkbox selected
				$('#deactive_timeEntry_btn_id').click(function(){

					  $.ajax({
						 type: 'POST',
						 url: 'index.php?r=time-card/deactivate',
						 data: {timecardid: pks},
						  beforeSend: function(  ) {
							console.log(pks);
						  },
						 success: function(data) {
							  $.pjax.reload({container:'.grid-view'});
						 }
					  });
				});
			}else {
					$('#deactive_timeEntry_btn_id').prop('disabled', true);
				}
		
	});
	$(".grid-view input[type=checkbox]").click(function(){
		
		// get approved value for this timecard
		approved = $(".timecard-view").attr("approved");
			
		var pks = $('.grid-view').yiiGridView('getSelectedRows');

		for(var i=0; i < pks.length; i++){
			// get activeStatus value for this timecard
			flag = $(".grid-view input[timeentryid="+pks[i]+"]").attr("activeStatus");
			
			// get totalworkhours for this timecard
			totalworkhours = $(".grid-view input[timeentryid="+pks[i]+"]").attr("totalworkhours");
			
			//alert("loop flag is "+flag+" i is : "+i);
			if(flag == "Inactive" || totalworkhours == .0){
				flag = "Inactive";
				break;
			}else{
				continue;
			}
		}
		
		if (!pks || pks.length != 0 && flag != "Inactive" && approved != "Yes"){
				$('#deactive_timeEntry_btn_id').prop('disabled', false); //TO ENABLE
				
				// triggered when checkbox selected
				$('#deactive_timeEntry_btn_id').click(function(){

					  $.ajax({
						 type: 'POST',
						 url: 'index.php?r=time-card/deactivate',
						 data: {timecardid: pks},
						  beforeSend: function(  ) {
							console.log(pks);
						  },
						 success: function(data) {
							  $.pjax.reload({container:'.grid-view'});
						 }
					  });
				});
			}else {
					$('#deactive_timeEntry_btn_id').prop('disabled', true);
				}
	}); 
 });
	 