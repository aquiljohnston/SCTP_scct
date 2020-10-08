<?php

use app\models\EmployeeDetailTime;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\grid\GridView;
use yii\bootstrap\Modal;
use yii\widgets\Pjax;
use app\assets\EmployeeApprovalAsset;
use app\controllers\BaseController;

//register assets
EmployeeApprovalAsset::register($this);

/* @var $this yii\web\View */
$this->title = 'Employee Detail';

?>
    <style type="text/css">
        /* [data-key="0"]
         {
             display:none;
         } */
    </style>

    <div class="report-summary-employee-detail index-div">
        <div class="lightBlueBar" style="height: 60px; padding: 10px;">
            <p>
                <?= Html::a('Back', ['index'], ['class' => 'btn btn-primary']) ?>
                <?php
                if (!$canAddTask) {
                    echo Html::button('Add Task', [
                        'class'    => 'btn btn-primary add_time_btn',
                        'disabled' => true,
                        'id'       => 'add_task_btn_id',
                        'style'    => ['margin' => '.2%']
                    ]);
                } else {
                    echo Html::button('Add Task', [
                        'class' => 'btn btn-primary add_time_btn',
                        'id'    => 'add_task_btn_id',
                        'style' => ['margin' => '.2%']
                    ]);
                }
                ?>
            </p>
            <br>
        </div>

        <?php Pjax::begin(['id' => 'EmployeeDetailView', 'timeout' => false]) ?>
        <div class="project-hours-container">
            <h4><span><?= 'Technician Name: ' . $totalData['Tech'] ?></span><span
                        style="float:right"><?= ' Weekly Total Hours: ' . $totalData['WeeklyTotal']; ?></span></h4>
            <?= GridView::widget([
                'id'           => 'DailyProjectHours',
                'dataProvider' => $projectDataProvider,
                'showHeader'   => false,
                'export'       => false,
                'pjax'         => true,
                'summary'      => '',
                'caption'      => '',
                'columns'      => [
                    [
                        'label'          => 'Label',
                        'attribute'      => 'Label',
                        'contentOptions' => ['class' => 'text-left'],
                    ],
                    [
                        'label'          => 'Value',
                        'attribute'      => 'Value',
                        'contentOptions' => ['class' => 'text-center'],
                    ]
                ]
            ]);
            ?>
        </div>
        <?= GridView::widget([
            'id'           => 'DailyBreakdownHours',
            'dataProvider' => $breakdownDataProvider,
            'export'       => false,
            'pjax'         => true,
            'summary'      => '',
            'caption'      => '',
            'columns'      => [
                [
                    'label'     => 'RowID',
                    'attribute' => 'RowID',
                    'hidden'    => true
                ],
                [
                    'label'     => 'ProjectID',
                    'attribute' => 'ProjectID',
                    'hidden'    => true
                ],
                [
                    'label'          => 'Project',
                    'attribute'      => 'Project',
                    'headerOptions'  => ['class' => 'text-left'],
                    'contentOptions' => ['class' => 'text-left'],
                ],
                [
                    'label'     => 'TaskID',
                    'attribute' => 'TaskID',
                    'hidden'    => true
                ],
                [
                    'label'          => 'Task',
                    'attribute'      => 'TaskName',
                    'headerOptions'  => ['class' => 'text-left'],
                    'contentOptions' => ['class' => 'text-left'],
                ],
                [
                    'label'          => 'Start Time',
                    'attribute'      => 'Start Time',
                    'headerOptions'  => ['class' => 'text-center'],
                    'contentOptions' => ['class' => 'text-center'],
                ],
                [
                    'label'          => 'End Time',
                    'attribute'      => 'End Time',
                    'headerOptions'  => ['class' => 'text-center'],
                    'contentOptions' => ['class' => 'text-center'],
                ],
                [
                    'label'          => 'Time On Task',
                    'attribute'      => 'Time On Task',
                    'headerOptions'  => ['class' => 'text-center'],
                    'contentOptions' => ['class' => 'text-center'],
                ]
            ]
        ]);
        ?>
        <p style="float:right; text-align:right;">
            <?= $totalData['Total']; ?>
            <br>
            <?= $totalData['TotalNoLunch']; ?>
        </p>
        <p style="float:right; text-align:right; font-weight:bold; margin-right:5px;">
            Total:
            <br>
            Total w/out lunch:
        </p>
        <?php Pjax::end() ?>
        <?php
        Pjax::begin(['id' => 'editTime', 'timeout' => false]);
        Modal::begin([
            'header' => '<h4>Edit Time</h4>',
            'id'     => 'editTimeModal',
            'size'   => 'modal-lg',
        ]);
        echo "<div id='editTimeModal'><span id='editTimeModalContentSpan'></span></div>";
        Modal::end();
        Pjax::end();
        ?>

        <?php
        // add task
        Pjax::begin(['id' => 'addTask', 'timeout' => false]);
        Modal::begin([
            'header' => '<h4>Add Task</h4>',
            'id'     => 'addTaskModal',
            'size'   => 'modal-lg',
        ]);
        echo "<div id='addTaskModal'><span id='addTaskModalContentSpan'></span></div>";
        Modal::end();
        Pjax::end();
        ?>
    </div>
    <input type="hidden" value="<?php echo $userID ?>" id="userID">
    <!--TODO consolidate date values-->
    <input type="hidden" value="<?php echo @$_GET['date']; ?>" id="timeCardDate">
    <input type="hidden" value="<?php echo $date ?>" id="currentDate">
    <script>

    </script>
<?php $this->registerJs("
    //form on project change reload task dropDownList
    $(document).
        off('change', '#employeedetailtime-projectid').
        on('change', '#employeedetailtime-projectid', function() {
            reloadTaskDropdown();
        });

    // includes disabled fields in serialize
    $.fn.serializeIncludeDisabled = function() {
        let disabled = this.find(':input:disabled').removeAttr('disabled');
        let serialized = this.serialize();
        disabled.attr('disabled', 'disabled');
        return serialized;
    };
$(function() {
    body = $('body');


//        $.pjax.reload({container: '#EmployeeDetailView', async: false}).done(function() {alert('test')});
//        $(document).on('pjax:send', function() {
//            $('#loading').show()
//        })
//        $(document).on('pjax:complete', function() {
//            $('#loading').hide()
//        })

    body.on('click', '#employee_detail_add_task_submit_btn', function(e) {

        //
        let formData = $('#EmployeeDetailAddTaskModalForm').serializeIncludeDisabled();

          $('#loading').show();

        let url = '/employee-approval/add-task?userID=" . $userID . "&date=" . $date . "';
        $.ajax({
            url: url,
            data: formData,
            type: 'POST',
            dataType: 'JSON',
        }).done(function(data) {
            
            if(data.success){
                $('#addTaskModal').modal('hide');
                $.pjax.reload({container: '#EmployeeDetailView', async: false});
            } else {
                alert(data.msg);
            }
              $('#loading').hide();
            
        }).fail(function(jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
             $('#loading').hide();
        });
    });

    body.on('change', '#employeedetailtime-taskid', function(e) {

        let taskName = $('#employeedetailtime-taskid option:selected').text();
        $('#employeedetailtime-taskname').val('Task ' + taskName);
    });

    //
    body.on('click', '.time-of-day-checkbox', function(e) {

        let timeOfDay = $(this).data('time-of-day');
        let time = $(this).data('time');
        let isChecked = $(this).is(':checked');
        
        // remove disabled attrs
        $('#employeedetailtime-starttime').removeAttr('disabled');
        $('#employeedetailtime-endtime').removeAttr('disabled');
        $('#employee_detail_add_task_submit_btn').removeAttr('disabled');        

        if (timeOfDay == '" . EmployeeDetailTime::TIME_OF_DAY_MORNING . "') {

            if (isChecked) {

                //
                $('#employeedetailtime-endtime').val(time);
                $('#employeedetailtime-endtime').attr('disabled', true);

                //
                $('#employeedetailtime-starttime').val('');
                $('#employeedetailtime-starttime').removeAttr('disabled');

                $('#employeedetailtime-timeofdayname').val('morning');
            }

        } else if (timeOfDay == '" . EmployeeDetailTime::TIME_OF_DAY_AFTERNOON . "') {

            if (isChecked) {

                //
                $('#employeedetailtime-starttime').val(time);
                $('#employeedetailtime-starttime').attr('disabled', true);

                //
                $('#employeedetailtime-endtime').val('');
                $('#employeedetailtime-endtime').removeAttr('disabled');

                $('#employeedetailtime-timeofdayname').val('afternoon');
            }
        }
    });
});
    //form pjax reload
    function reloadTaskDropdown() {
        //get current user for project dropdown
        userID = $('#userID').val();
        //fetch formatted form values
        //  data = getFormData();

        $('#loading').show();
        $.pjax.reload({
            type: 'POST',
            replace: false,
            url: '/employee-approval/add-task-modal?userID=' + userID + '&date=' + $('#date').val(),
            data: {projectID: $('#employeedetailtime-projectid').val()},
            container: '#addTaskDropDownPjax',
            timeout: 99999,
        });
        $('#addTaskDropDownPjax').off('pjax:success').on('pjax:success', function() {
            $('#loading').hide();
        });
    }");
?>