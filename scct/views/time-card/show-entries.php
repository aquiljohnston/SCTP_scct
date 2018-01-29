<?php

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\grid\GridView;
use yii\bootstrap\Modal;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\time-card */
?>
<style type="text/css">
[data-key="0"] 
{
    display:none;
}
</style>

<div class="time-card-entries">

    <?php
    $this->title = $projectName.' Week '.$from.' - '.$to;
    $this->params['breadcrumbs'][] = $this->title;
     ?>

    <div class="lightBlueBar">
    <h3> <?= Html::encode($this->title) ?></h3>


        <?php

    $approveUrl = urldecode(Url::to(['time-card/approve', 'id' => $model["TimeCardID"]]));

    if ($model["TimeCardApprovedFlag"] === "Yes") {
        $approve_status = true;
    } else {
        $approve_status = false;
    }
    //var_dump($task);
    ?>
    <p>
        <?= Html::a('Back', ['index'], ['class' => 'btn btn-primary']) ?>
        <?php if ($model['TimeCardApprovedFlag'] == 'Yes') : ?>
            <?= Html::button('Approve', [
                'class' => 'btn btn-primary',
                'disabled' => true,
                'id' => 'disable_single_approve_btn_id_timecard',

            ]) ?>
            <?= Html::button('Deactivate', [
                'class' => 'btn btn-primary',
                'disabled' => true,
                'id' => 'deactive_timeEntry_btn_id',
            ]) ?>
            <?= Html::button('Add Task', [
                'class' => 'btn btn-primary add_task_btn',
                'disabled' => false,
                'id' => 'add_task_btn_id',
            ]) ?>
        <?php  else : ?>
            <?= Html::a('Approve', $approveUrl, [
                'class' => 'btn btn-primary',
                'disabled' => false,
                'id' => 'enable_single_approve_btn_id_timecard',
            ]) ?>
            <?= Html::button('Deactivate', [
                'class' => 'btn btn-primary',
                'disabled' => false,
                'id' => 'deactive_timeEntry_btn_id',
            ]) ?>
            <?= Html::button('Add Task', [
                'class' => 'btn btn-primary add_task_btn',
                'disabled' => false,
                'id' => 'add_task_btn_id',
            ]) ?>
        <?php endif; ?>

        <input type="hidden" value=<?php echo $model["TimeCardID"]?> name="timeCardId" id="timeCardId">

    <!--create new button start
        <?= Html::button('Create New', ['value' =>'', 'class' => 'btn btn-success', 'id' => 'modalNewTimeEntry', 'disabled' => $approve_status]) ?>
    create new button end-->

    </p>
    <br>


      <!--modal start-->
    <?php
    Modal::begin([
        'header' => '<h4>New Time Entry</h4>',
        'id' => 'modalNewTimeEntry',
        'size' => 'modal-lg',
    ]);

    echo "<div id='modalNewTimeEntryContent'></div>";

    Modal::end();
    ?>
      <!--modal end-->
  
    </div>
    <?php Pjax::begin(['id' => 'ShowEntriesView', 'timeout' => false]) ?>
    <?= \kartik\grid\GridView::widget([
        'id' => 'allTaskEntries',
        'dataProvider' => $task,
        'export' => false,
        'pjax' => true,
        'summary' => '',
        'caption' => "",
        'columns' => [
            [
                'label' => 'Task',
                'attribute' => 'Task',
            ],
            [
                'label' => 'Sunday ' . $SundayDate,
                'attribute' => 'Date1',
            ],
            [
                'label' => 'Monday '. $MondayDate,
                'attribute' => 'Date2',
            ],
            [
                'label' => 'Tuesday '. $TuesdayDate,
                'attribute' => 'Date3',
            ],
            [
                'label' => 'Wednesday '. $WednesdayDate,
                'attribute' => 'Date4',
            ],
            [
                'label' => 'Thursday '. $ThursdayDate,
                'attribute' => 'Date5',
            ],
            [
                'label' => 'Friday '. $FridayDate,
                'attribute' => 'Date6',
            ],
            [
                'label' => 'Saturday '. $SaturdayDate,
                'attribute' => 'Date7',
            ]
        ]
    ]);
    ?>
    <?php Pjax::end() ?>

    <?php
    Modal::begin([
        'header' => '<h4>ADD TASK</h4>',
        'id' => 'addTaskModal',
    ]);
    echo "<div id='modalAddTask'>Loading...</div>";
    Modal::end();
    ?>
</div>
