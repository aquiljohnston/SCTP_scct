// BULK DELETE
$(function() {
	var checkDeActiveArray = [];
	var counter = 0;
	var flag = "Yes";
	var totalworkhours = -1;
	$('#deactive_mileageEntry_btn_id').prop('disabled', true); //TO DISABLED
	$(".grid-view input[type=checkbox]").click(function(){

		var checkDeActive = $(this).attr("activeStatus");
		//checkDeactiveArray[counter++] = checkApproved;
		//var model = $(this).attr('model');
		var pks = $('.grid-view').yiiGridView('getSelectedRows');

		for(var i=0; i < pks.length; i++){
			// get activeStatus value for this timecard
			flag = $(".grid-view input[mileageentryid="+pks[i]+"]").attr("activeStatus");
			
			// get totalworkhours for this timecard
			totalworkhours = $(".grid-view input[mileageentryid="+pks[i]+"]").attr("totalworkhours");
			
			//alert("loop flag is "+flag+" i is : "+i);
			if(flag == "Inactive" || totalworkhours == .0 || flag == "Inactive"){
				flag = "Inactive";
				break;
			}else{
				continue;
			}
		}
		
		if (!pks || pks.length != 0 && flag != "Inactive"){
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
	 