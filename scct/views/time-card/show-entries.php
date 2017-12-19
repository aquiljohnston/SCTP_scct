<?php

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\grid\GridView;
//use yii\bootstrap\Modal;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\time-card */
?>
<div class="time-card-entries">

    <?php
    $this->title = 'Week '.$from.' - '.$to;
    $this->params['breadcrumbs'][] = $this->title;
     ?>

    <div class="lightBlueBar">
    <h3> <?= Html::encode($this->title) ?></h3>


        <?php

           // check start date for this timecard.
    $approveUrl = urldecode(Url::to(['time-card/approve', 'id' => $model["TimeCardID"]]));

    //disactive TimeEntry
    //$disApproveUrl = urldecode(Url::to(['time-card/approve', 'id' => $model["TimeCardID"]]));
    if ($model["TimeCardApprovedFlag"] === "Yes") {
        $approve_status = true;
    } else {
        $approve_status = false;
    }
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
        <?php endif; ?>
    </p>
    <br>
    </div>

    <?= \kartik\grid\GridView::widget([
        'id' => 'assetsGV',
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
                'label' => 'Sunday',
                'attribute' => 'Date1',
            ],
            [
                'label' => 'Monday',
                'attribute' => 'Date2',
            ],
            [
                'label' => 'Tuesday',
                'attribute' => 'Date3',
            ],
            [
                'label' => 'Wednesday',
                'attribute' => 'Date4',
            ],
            [
                'label' => 'Thurday',
                'attribute' => 'Date5',
            ],
            [
                'label' => 'Friday',
                'attribute' => 'Date6',
            ],
            [
                'label' => 'Saturday',
                'attribute' => 'Date7',
            ]
        ]
    ]);
    ?>
</div>
