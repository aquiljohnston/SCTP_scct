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
        $('#addTaskModal').modal('show')
            .find('#modalAddTask').html("Loading...");
        $('#addTaskModal').modal('show')
            .find('#modalAddTask')

            .load('/task/add-task-entry?weekStart='+weekStart+'&weekEnd='+weekEnd+'&TimeCardID=' + timeCardID + '&SundayDate=' + SundayDate + '&SaturdayDate=' + SaturdayDate + '&timeCardProjectID=' + timeCardProjectID);
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
            .load('/task/add-task-entry?weekStart='+weekStart+'&weekEnd='+weekEnd+'&TimeCardID=' + timeCardID );

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
