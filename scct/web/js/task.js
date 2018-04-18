/**
 * Created by tzhang on 12/20/2017.
 */
$(function () {
    $(document).off('keypress', '#taskSearchField').on('keypress', '#taskSearchField', function (e) {
        if (e.keyCode === 13 || e.keyCode === 10) {
            e.preventDefault();
            reloadTaskGridView();
        }
    });

    $(document).off('click', '#taskSearchCleanFilterButton').on('click', '#taskSearchCleanFilterButton', function () {
        $('#taskSearchField').val("");
        reloadTaskGridView();
    });

    $(document).off('keypress', '#userSearchField').on('keypress', '#userSearchField', function (e) {
        if (e.keyCode === 13 || e.keyCode === 10) {
            e.preventDefault();
            reloadUserGridView();
        }
    });

    $(document).off('click', '#userSearchCleanFilterButton').on('click', '#userSearchCleanFilterButton', function () {
        $('#userSearchField').val("");
        reloadUserGridView();
    });
	
	$(document).off('click', '.add_task_btn').on('click', '.add_task_btn', function (){
        var weekStart   = $("table th").eq(1).attr('class');
        var weekEnd     = $("table th").eq(7).attr('class');
        var timeCardID = $('#timeCardId').val();
        var SundayDate = $('#SundayDate').val();
        var SaturdayDate = $('#SaturdayDate').val();
        var timeCardProjectID = $('#TimeCardProjectID').val();
		var inOvertime = $('#inOvertime').val();
      $('#addTaskModal').modal('show').find('#modalContentSpan').html("Loading...");
       //Fetch modal content via pjax to prevent sync console warning and FF page flash
       $.pjax.reload({
        type: 'GET',
        replace:false,
        url: '/task/add-task-entry?weekStart='+weekStart+'&weekEnd='+weekEnd+'&TimeCardID=' + timeCardID + '&SundayDate=' + SundayDate + '&SaturdayDate=' + SaturdayDate + '&timeCardProjectID=' + timeCardProjectID + '&inOvertime=' + inOvertime,
        container: '#modalContentSpan', // id to update content
        timeout: 99999
        })
    });
});

function reloadTaskGridView() {
    var jqTaskContainer = $('#taskSearchContainer');
    var form = jqTaskContainer.find("#taskForm");
    if (form.find(".has-error").length) {
        return false;
    }
    $('#loading').show();
    $.pjax.reload({
        type: 'GET',
        url: form.attr("action"),
        container: '#taskGridView', // id to update content
        data: form.serialize(),
        timeout: 99999
    }).done(function () {
        $('#loading').hide();
    });
}

function reloadUserGridView() {
    var jqTaskContainer = $('#taskSearchContainer');
    var form = jqTaskContainer.find("#taskForm");
    if (form.find(".has-error").length) {
        return false;
    }
    $('#loading').show();
    $.pjax.reload({
        type: 'GET',
        url: form.attr("action"),
        container: '#userGridView', // id to update content
        data: form.serialize(),
        timeout: 99999
    }).done(function () {
        $('#loading').hide();
    });
}

function TaskEntryCreation() {
	var form            = $('#TaskEntryForm');

    $('#loading').show();

    $.ajax({
        type: 'GET',
        url: form.attr("action"),
        data: form.serialize(),
        success: function () {
            $('#loading').hide();
           $.pjax.reload({container:"#ShowEntriesView", timeout: 99999}).done(function(){
    //
             $.each($('#ShowEntriesView tbody tr td'),function(index,value){
                 if($(this).attr('data-col-seq') >=1 && ($(this).text()!="") && ($(this).parent().attr('data-key')>0) 
                    && (!$('#disable_single_approve_btn_id_timecard').length > 0)
                    ){

                   $(this).attr("title","Click to deactivate this time entry!");

                 } else if($('#isAccountant').val()){
                    $(this).attr("title","Click to deactivate this time entry!");
                 }
            })

     });; //for pjax update
        },
        error  : function () {
            console.log("internal server error");
        }
    });
}



$(document).on('click','#deactive_timeEntry_btn_id',function(e){
        var id           =   $('#timeCardId').val();
        var tasks        =    [];
        var name         =    "";
       
       $(".entryData").each(function(k,value){   
         if($(this).is(":checked")){ 

            //get task name for payload and confirm message
            name = $(this).attr('taskName');
            tasks.push(name);
              
            //walk each cell and build payload
            $.each($(this).closest('tr').find('td'),function(index,value) {
                if($(this).attr('data-col-seq') >=1 && ($(this).text()!="") && ($(this).parent().attr('data-key')>0))
                    {   
                        entry_date = $(this).closest('table').find('th').eq(index).attr('class');
                        entries.push({taskName:name, day:entry_date, timeCardID:id})
                    }       
                 }) 
            }
        })
      
        tasks.join(', ');
        data = {entries}
       
        krajeeDialog.defaults.confirm.title = 'Deactivate All Task';
        krajeeDialog.confirm('Are you sure you want to deactivate all ' +tasks+ '? Please confirm...', function (resp) {
        
        if (resp) {
            $.ajax({
            type: 'POST',
            url: '/time-card/deactivate/',
            data: data,
            beforeSend: function(  ) {
              applyToolTip();
            },
            success: function(data) {
                $.pjax.reload({container:"#ShowEntriesView", timeout: 99999}).done(function(){
            applyToolTip();
        });; //for pjax update

                $('#loading').hide();
                $('#deactive_timeEntry_btn_id').prop('disabled',true);
            },
             error: function(data) {
                console.log('error')
             }
        });
            } else {
                $('#w0').modal('toggle');
                 return false;
          }
        });

    });
