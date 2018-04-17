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

    <input id="SundayDate" type="hidden" name="SundayDate" value=<?php echo $SundayDateFull; ?>>
    <input id="SaturdayDate" type="hidden" name="SaturdayDate" value=<?php echo $SaturdayDateFull; ?>>
    <input id="TimeCardProjectID" type="hidden" name="TimeCardProjectID" value=<?php echo $timeCardProjectID; ?>>
  
    <div class="lightBlueBar">
    <h3> <?= $projectName.' Week '.$from.' - '.$to.': '.$lName.', '.$fName; ?></h3>

    <?php
    if ($model['TimeCardApprovedFlag'] == 1) {
        $approve_status = true;
    } else {
        $approve_status = false;
    }
	?>
    <p>
        <?= Html::a('Back', ['index'], ['class' => 'btn btn-primary']) ?>
        <?php if ($approve_status && !$isAccountant) : ?>
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
                'disabled' => true,
                'id' => 'add_task_btn_id',
            ]) ?>
        <?php elseif ($approve_status && $isAccountant) : ?>
            <?= Html::button('Approve', [
                'class' => 'btn btn-primary',
                'disabled' => true,
                'id' => 'disable_single_approve_btn_id_timecard',
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
        <?php  else : ?>
            <?= Html::button('Approve', [
                'class' => 'btn btn-primary',
                'disabled' => false,
                'id' => 'enable_single_approve_btn_id_timecard',
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
        <?php endif; ?>
       
        <input type="hidden" value=<?php echo $model["TimeCardID"]?> name="timeCardId" id="timeCardId">
        <input type="hidden" value=<?php echo $isAccountant ?> id="isAccountant">
		<input type="hidden" value=<?php echo $inOvertime ?> id="inOvertime">
    </p>
    <br>

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
                'headerOptions' => ['class'=>$SundayDateFull]
            ],
            [
                'label' => 'Monday '. $MondayDate,
                'attribute' => 'Date2',
                'headerOptions' => ['class'=>$MondayDateFull],
            ],
            [
                'label' => 'Tuesday '. $TuesdayDate,
                'attribute' => 'Date3',
                'headerOptions' => ['class'=>$TuesdayDateFull],
            ],
            [
                'label' => 'Wednesday '. $WednesdayDate,
                'attribute' => 'Date4',
                'headerOptions' => ['class'=>$WednesdayDateFull],
            ],
            [
                'label' => 'Thursday '. $ThursdayDate,
                'attribute' => 'Date5',
                'headerOptions' => ['class'=>$ThursdayDateFull],
            ],
            [
                'label' => 'Friday '. $FridayDate,
                'attribute' => 'Date6',
                'headerOptions' => ['class'=>$FridayDateFull],
            ],
            [
                'label' => 'Saturday '. $SaturdayDate,
                'attribute' => 'Date7',
                'headerOptions' => ['class'=>$SaturdayDateFull],
            ],
                    [
                        'header'            => 'Deactivate All Task',
                        'class'             => 'kartik\grid\CheckboxColumn',
                        'contentOptions'    => [],
                        'checkboxOptions'   => function ($model, $key, $index, $column) {

                            return ['timeCardId' => Yii::$app->getRequest()->getQueryParam('id'),'disabled' => false,'taskName' => $model['Task'],'entry' => '','class'=>'entryData'];
                        }
                    ]
        ]
    ]);
    ?>
    <?php Pjax::end() ?>
    <?php
    Pjax::begin(['id' => 'showTime', 'timeout' => false]);
    Modal::begin([
        'header' => '<h4>ADD TASK</h4>',
        'id' => 'addTaskModal',
        'size' => 'modal-lg',
    ]);
    echo "<div id='modalAddTask'><span id='modalContentSpan'></span></div>";
    Modal::end();
    Pjax::end();
    ?>

</div>
