$(function(){
    var jqTimeCardFilter    = $('#timecard_filter');
    var jqTCDropDowns       = $('#timeCardDropdownContainer');
    var jqWeekSelection     = jqTimeCardFilter.find('#timeCardDateRange');
    var jqTCPageSize        = jqTCDropDowns.find('#timeCardPageSize');
    var projectFilterDD     = $('#projectFilterDD');
    entries                 = [];           
	
    $(document).off('change', "#timeCardDateRange").on('change', "#timeCardDateRange", function (event) {
        event.preventDefault();
        var selected = $(this).find(":selected").val();
        if(selected == "other") {
			//reset date picker
			resetDatePicker();
			//show date picker
            $('#datePickerContainer').css("display", "block");
        }else {
			//hide date picker
            $('#datePickerContainer').css("display", "none");
            reloadTimeCardGridView();
        }
    });

    $(document).off('change', "#timeCardPageSize").on('change', "#timeCardPageSize", function (event) {
        $('#timeCardPageNumber').val(1);
        reloadTimeCardGridView();
        event.preventDefault();
        return false;
    });



     $(document).off('change', '#projectFilterDD').on('change', '#projectFilterDD', function (event) {
        reloadTimeCardGridView();
        event.preventDefault();
        return false;
    });

    $(document).off('click', "#TCPagination ul li a").on('click', "#TCPagination ul li a", function (event) {
        var page = $(this).data('page') + 1; // Shift by one to 1-index instead of 0-index.
        $('#timeCardPageNumber').val(page);
        reloadTimeCardGridView();
        event.preventDefault();
        return false;
    });
	
	// timecard filter listener
    $(document).off('keypress', '#timeCardFilter').on('keypress', '#timeCardFilter', function (event) {
        if (event.keyCode === 13 || event.keyCode === 10) {
            event.preventDefault();
			$('#timeCardPageNumber').val(1);
            reloadTimeCardGridView();
        }
    });

    $(document).off('click', '#timeCardSearchCleanFilterButton').on('click', '#timeCardSearchCleanFilterButton', function (){
        $('#timeCardFilter').val("");
        reloadTimeCardGridView();
    });

    $(document).off('click', '#clearProjectFilterButton').on('click', '#clearProjectFilterButton',function (){
        projectFilterDD.val("");
		//either or not both, first line returns you to the base screen /time-card second line reloads the page
        //window.location.href = window.location.href.split('?')[0];
        reloadTimeCardGridView();
    });

    //reload table
    function reloadTimeCardGridView() {
        var form = jqTCDropDowns.find("#TimeCardForm");
        if (form.find(".has-error").length){
            return false;
        }
        $('#loading').show();
        $.pjax.reload({
            type: 'GET',
            url: form.attr("action"),
            container: '#timeCardGridview', // id to update content
            data: form.serialize(),
            timeout: 99999
        });
        $('#timeCardGridview').off('pjax:success').on('pjax:success', function () {
			$.pjax.reload({
				container: '#timeCardForm',
				timeout:false
			});
			$('#timeCardForm').off('pjax:success').on('pjax:success', function () {
				applyTimeCardOnClickListeners();
				applyTimeCardSubmitButtonListener();
				$('#loading').hide();
			});
			$('#timeCardForm').off('pjax:error').on('pjax:error', function () {
				location.reload();
			});
		});
        $('#timeCardGridview').off('pjax:error').on('pjax:error', function () {
            location.reload();
        });
    }

	//I believe everything below this should probably be in the task.js file because it is limited to the task screen
    $(document).on('click','#deactive_timeEntry_btn_id',function(e){
             $('#loading').show();
        id = $('#timeCardId').val();

       $(".entryData").each(function(k,value){

         if($(this).is(":checked")){
            
            //var isThere = $.grep(entries, function(e){ return e.id == k; });

            //if(isThere.length == 0){
                 entries.push({
                   /// id : k,
                    taskName : $(this).attr('taskName'),
                    day : $(this).attr('entry'),
                    timeCardID : id
                })
            }
       // }
    })

        data = {entries}

         // console.log(data);
        // return false;

        $.ajax({
            type: 'POST',
            url: '/time-card/deactivate/',
            data: data,
            beforeSend: function(  ) {
          
            },
            success: function(data) {
                $.pjax.reload({container:"#ShowEntriesView", timeout: 99999}); //for pjax update
                $('#loading').hide();
                $('#deactive_timeEntry_btn_id').prop('disabled',true);
            }
        });
    });


    //iterate table row get row that has time entrydata
	$(document).on('change','.entryData', function (e) {

		tr    = $(this).closest('tr')
		input = $(this);

		if($(this).is(":checked")){
			tr.find('td').each(function(index,value){
			  if(index != 0 && $(this).text()!=""){

				 th_class = $(this).closest('table').find('th').eq(index).attr('class');

				}

			})
			 input.attr('entry',th_class);
		}
		checkDeactivateBtn();
	})

	function checkDeactivateBtn(){

	  if ($("#allTaskEntries-container input:checkbox:checked").length > 0){
		 disableButton = $("#disable_single_approve_btn_id_timecard").prop("disabled");
		 if(!disableButton){
			$('#deactive_timeEntry_btn_id').prop('disabled',false);
		 }
			
	  }
	  else{
			$('#deactive_timeEntry_btn_id').prop('disabled',true);
		}
	}


   $('#enable_single_approve_btn_id_timecard').click(function (e) {
        var timeCardId = $('#timeCardId').val();
        var confirmBox = confirm('Are you sure you want to approve this?');
        if (confirmBox) {

            $('#loading').show();

            $.ajax({
                type: 'POST',
                url: '/time-card/approve?id='+timeCardId,
                success: function(data) {
                 //$.pjax.reload({container:"#allTheButtons", timeout: 99999}); //for pjax update
                $('#deactive_timeEntry_btn_id').prop('disabled',true);
                $('#enable_single_approve_btn_id_timecard').addClass('disabled');
                $('#loading').hide();
            }
            });
        } else {
            event.stopImmediatePropagation();
            event.preventDefault();
        }

       
    });

	//function called when other is selected in week dropdown to reset widget to default
	function resetDatePicker(){
		//get date picker object
		var datePicker = $('#dynamicmodel-daterangepicker-container').find('.kv-drp-dropdown').data('daterangepicker');
		//create default start end
		var fm = moment().startOf('day') || '';
		var to = moment() || '';
		//set default selections in widget
		datePicker.setStartDate(fm);
		datePicker.setEndDate(to);
		//set default date range
		daterange = fm.format('YYYY-MM-DD') + ' - ' + to.format('YYYY-MM-DD');
		$('#dynamicmodel-daterangepicker-container').find('.kv-drp-dropdown').find('.range-value').html(daterange);
	}
	
});
