$(function(){
    var jqTimeCardFilter    = $('#timecard_filter');
    var jqTCDropDowns       = $('#timeCardDropdownContainer');
    var jqWeekSelection     = jqTimeCardFilter.find('#timeCardDateRange');
    var jqTCPageSize        = jqTCDropDowns.find('#timeCardPageSize');
    var projectFilterDD     = $('#projectFilterDD');
    entries                 = []; 

    jqWeekSelection.on('change', function (event) {
        event.preventDefault();
        var selected = $(this).find(":selected").val();
        if(selected == "other") {
            $('#datePickerContainer').css("display", "block");
        }else {
            $('#datePickerContainer').css("display", "none");
            reloadTimeCardGridView();
        }
    });

    jqTCPageSize.on('change', function (event) {
        $('#timeCardPageNumber').val(1);
        reloadTimeCardGridView();
        event.preventDefault();
        return false;
    });

    //
      projectFilterDD.on('change', function (event) {
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

    $('#clearProjectFilterButton').on('click',function (){
        projectFilterDD.val("");
        reloadTimeCardGridView();
    });

    //filter
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
        $('#timeCardGridview').on('pjax:success', function () {
            $('#loading').hide();
            applyOnClickListeners();
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
        console.log("ADD TASK CLICKED");
        var timeCardID = $('#timeCardId').val();
        $('#addTaskModal').modal('show')
            .find('#modalAddTask').html("Loading...");
        $('#addTaskModal').modal('show')
            .find('#modalAddTask')
            .load('/time-card/add-task-entry?TimeCardID=' + timeCardID );
    });
});

function TaskEntryCreation() {
    var form = $('#TaskEntryForm');
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
