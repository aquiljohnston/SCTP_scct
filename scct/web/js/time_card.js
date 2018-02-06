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
            $('#datePickerContainer').css("display", "block");
        }else {
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
    $(document).off('keypress', '#timeCardFilter').on('keypress', '#timeCardFilter', function (e) {
        if (e.keyCode === 13 || e.keyCode === 10) {
            e.preventDefault();
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
        reloadTimeCardGridView();
    });

    //filter
    function reloadTimeCardGridView(clear=false) {
        var form = jqTCDropDowns.find("#TimeCardForm");
        if (form.find(".has-error").length){
            return false;
        }
        $('#loading').show();
        chosen =  $('#projectFilterDD').val();
        $.pjax.reload({
            type: 'GET',
            url: form.attr("action"),
            container: '#timeCardGridview', // id to update content
            data: form.serialize(),
            timeout: 99999
        });
        $('#timeCardGridview').on('pjax:success', function () {

            $.pjax.reload({container: '#timeCardForm', timeout:false});
            $('#timeCardForm').on('pjax:success', function () {
                  
                //$('#loading').hide();
                //applyOnClickListeners();
                if(!clear){
                     //$('#projectFilterDD').val(chosen).prop('selected',true);
                }
               
            });
            $('#loading').hide();
        });
        $('#timeCardGridview').on('pjax:error', function () {
            $('#loading').hide();
            location.reload();
        });
    }


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
        $('#deactive_timeEntry_btn_id').prop('disabled',false);
  }
  else{
        $('#deactive_timeEntry_btn_id').prop('disabled',true);
    }
}


    $(document).off('click', '.add_task_btn').on('click', '.add_task_btn', function (){

        var weekStart   = $("table th").eq(1).attr('class');
        var weekEnd     = $("table th").eq(7).attr('class');

        console.log("ADD TASK CLICKED");
        var timeCardID = $('#timeCardId').val();
        var SundayDate = $('#SundayDate').val();
        var SaturdayDate = $('#SaturdayDate').val();
        var timeCardProjectID = $('#TimeCardProjectID').val();
        $('#addTaskModal').modal('show')
            .find('#modalAddTask').html("Loading...");
        $('#addTaskModal').modal('show')
            .find('#modalAddTask')

            .load('/time-card/add-task-entry?weekStart='+weekStart+'&weekEnd='+weekEnd+'&TimeCardID=' + timeCardID + '&SundayDate=' + SundayDate + '&SaturdayDate=' + SaturdayDate + '&timeCardProjectID=' + timeCardProjectID);
    });
});

function TaskEntryCreation() {
        var form            = $('#TaskEntryForm');
        var weekStartTest   = new Date($("table th").eq(1).attr('class').replace(/-/g, '\/'));
        var weekStart       = $("table th").eq(1).attr('class');
        var weekEndTest     = new Date($("table th").eq(7).attr('class').replace(/-/g, '\/'));
        var weekEnd         = $("table th").eq(7).attr('class');
        var date            = new Date ($('#dynamicmodel-date').val());

    

    if(date < weekStartTest  ||  date > weekEndTest){

          var timeCardID = $('#timeCardId').val();
        $('#addTaskModal').modal('show')
            .find('#modalAddTask').html("Loading...");
        $('#addTaskModal').modal('show')
            .find('#modalAddTask')
            .load('/time-card/add-task-entry?weekStart='+weekStart+'&weekEnd='+weekEnd+'&TimeCardID=' + timeCardID );

            $('span[id^="errorSpot"]').remove();
            $('#TaskEntryForm, .modal-header').prepend('<span id="errorSpot" class="bg-warning">Date is not within week range!</span>');

        return false;

    }

    $('#loading').show();

    $.ajax({
        type: 'GET',
        url: form.attr("action"),
        data: form.serialize(),
        success: function () {
            $('#loading').hide();
            $.pjax.reload({container:"#ShowEntriesView", timeout: 99999}); //for pjax update
        },
        error  : function () {
            console.log("internal server error");
        }
    });
}
