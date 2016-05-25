// BULK DELETE
$(function() {
	var checkDeActiveArray = [];
	var counter = 0;
	var flag = "Yes";
	var approved = "No";
	var totalworkhours = -1;
	$('#deactive_mileageEntry_btn_id').prop('disabled', true); //TO DISABLED
	$(".grid-view input[type=checkbox]").click(function(){

		var pks = $('.grid-view').yiiGridView('getSelectedRows');
		
		// get approved value for this mileagecard
			approved = $(".mileagecard-view").attr("approved");

		for(var i=0; i < pks.length; i++){
			// get activeStatus value for this timecard
			flag = $(".grid-view input[mileageentryid="+pks[i]+"]").attr("activeStatus");
			
			//alert("loop flag is "+flag+" i is : "+i);
			if(flag == "Inactive" || totalworkhours == .0){
				flag = "Inactive";
				break;
			}else{
				continue;
			}
		}
		
		if (!pks && pks.length != 0 && flag != "Inactive" && approved != "Yes"){
				alert("approved if "+approved);
				$('#deactive_mileageEntry_btn_id').prop('disabled', false); //TO ENABLE
				
				// triggered when checkbox selected
				$('#deactive_mileageEntry_btn_id').click(function(){

					  $.ajax({
						 type: 'POST',
						 url: 'index.php?r=mileage-card/deactivate',
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
					$('#deactive_mileageEntry_btn_id').prop('disabled', true);
				}
	}); 
 });
	 