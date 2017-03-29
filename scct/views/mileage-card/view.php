<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\mileagecard */

$this->title = 'Mileage Entry';
$this->params['breadcrumbs'][] = ['label' => 'MileageCard', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$gridViewColumnList = [
    [
        'attribute' => 'MileageEntryStartingMileage',
        'label' => 'Starting Mileage'
    ],
    [
        'attribute' => 'MileageEntryEndingMileage',
        'label' => 'Ending Mileage'
    ],
    [
        'attribute' => 'MileageEntryStartDate',
        'label' => 'Start Date'
    ],
    [
        'attribute' => 'MileageEntryEndDate',
        'label' => 'End Date'
    ],
    [
        'attribute' => 'MileageEntryComment',
        'label' => 'Comment'
    ],
    [
        'attribute' => 'MileageEntryCreateDate',
        'label' => 'Created Date'
    ],
    [
        'attribute' => 'MileageEntryCreatedBy',
        'label' => 'CreatedBy'
    ],
    [
        'attribute' => 'MileageEntryActiveFlag',
        'label' => 'Active Flag'
    ],
    [
        'class' => 'yii\grid\CheckboxColumn',
        'checkboxOptions' => function ($model, $key, $index, $column) {
            return ['mileagecardid' => $model["MileageEntryMileageCardID"], 'mileageentryid' => $model["MileageEntryID"], 'activeStatus' => $model["MileageEntryActiveFlag"]];
        }
        /*'pageSummary' => true,
        'rowSelectedClass' => GridView::TYPE_SUCCESS,
        'contentOptions'=>['style'=>'width: 0.5%'],*/
    ],
];
?>
<div class="mileagecard-view" approved= <?php echo $ApprovedFlag; ?>>

    <?php if ($duplicateFlag == 1) { ?>
        <script>alert("The current Mileage Entry already exists, please try again.");</script>
    <?php } ?>

    <h1><?= Html::encode($this->title) ?></h1>

    <?php
    $approved = $ApprovedFlag;
    $approveUrl = urldecode(Url::to(['mileage-card/approve', 'id' => $model['MileageCardID']]));
    if ($model["MileageCardApprovedFlag"] === "Yes") {
        $approve_status = true;
    } else {
        $approve_status = false;
    }
    ?>
    <p>
        <?= Html::a('Back', ['index'], ['class' => 'btn btn-primary']) ?>

        <?php if ($approve_status === true || $Total_Mileage_Current_MileageCard == .0) { ?>
            <?= Html::button('Approve', [
                'class' => 'btn btn-primary',
                'disabled' => true,
                'id' => 'disable_single_approve_btn_id_mileagecard',
                /*'data' => [
                    'confirm' => 'Are you sure you want to approve this item?']*/
            ]) ?>

            <?= Html::button('Deactivate', [
                'class' => 'btn btn-primary',
                'disabled' => true,
                'id' => 'deactive_mileageEntry_btn_id',
                /*'data' => [
                           'confirm' => 'Are you sure you want to deactivate this item?']*/
            ]) ?>
        <?php } else { ?>
            <?= Html::a('Approve', $approveUrl, [
                'class' => 'btn btn-primary',
                'disabled' => false,
                'id' => 'enable_single_approve_btn_id_mileagecard',
                /*'data' => [
                    'confirm' => 'Are you sure you want to approve this item?']*/
            ]) ?>

            <?= Html::button('Deactivate', [
                'class' => 'btn btn-primary',
                'disabled' => false,
                'id' => 'deactive_mileageEntry_btn_id',
                /* 'data' => [
                            'confirm' => 'Are you sure you want to deactivate this item?']*/
            ]) ?>
        <?php } ?>

    </p>

    <?php Pjax::begin(['id' => 'MileageCardView', 'timeout' => false]); ?>
    <!--Sunday TableView-->
    <h2 class="mileage_entry_header">Sunday</h2>
    <?= GridView::widget([
        'dataProvider' => $SundayProvider,
        'columns' => $gridViewColumnList
    ]);
    ?>
    <?php
    // get current Mileage Card's Date
    $MileageCardStartDate = new DateTime($model["MileageStartDate"]);
    $SundayStr = $MileageCardStartDate->format('Y-m-d');

    $url = urldecode(Url::to(['mileage-card/create-mileage-entry', 'mileageCardId' => $model["MileageCardID"], 'mileageCardTechId' => $model['MileageCardTechID'], 'mileageCardDate' => $SundayStr]));
    ?>
    <p>
        <?= Html::button('Create New', ['value' => $url, 'class' => 'btn btn-success', 'id' => 'mileageModalButtonSunday', 'disabled' => $approve_status]) ?>
        <?php if ($Total_Mileage_Sun != 0) { ?>
            <span class="totalhours"><?php echo "Total mileage is : " . $Total_Mileage_Sun ?></span>
        <?php } else { ?>
            <span class="no_totalhours"></span>
        <?php } ?>
    </p>

    <br>

    <!--Monday TableView-->
    <h2 class="mileage_entry_header">Monday</h2>
    <?= GridView::widget([
        'dataProvider' => $MondayProvider,
        'columns' => $gridViewColumnList
    ])
    ?>
    <?php
    // get current Mileage Card's Date
    $MondayDate = $MileageCardStartDate->modify('+1 day');
    $MondayStr = $MondayDate->format('Y-m-d');

    $url = urldecode(Url::to(['mileage-card/create-mileage-entry', 'mileageCardId' => $model["MileageCardID"], 'mileageCardTechId' => $model['MileageCardTechID'], 'mileageCardDate' => $MondayStr]));
    ?>
    <p>
        <?= Html::button('Create New', ['value' => $url, 'class' => 'btn btn-success', 'id' => 'mileageModalButtonMonday', 'disabled' => $approve_status]) ?>
        <?php if ($Total_Mileage_Mon != 0) { ?>
            <span class="totalhours"><?php echo "Total mileage is : " . $Total_Mileage_Mon ?></span>
        <?php } else { ?>
            <span class="no_totalhours"></span>
        <?php } ?>
    </p>

    <br>

    <!--Tuesday TableView-->
    <h2 class="mileage_entry_header">Tuesday</h2>
    <?= GridView::widget([
        'dataProvider' => $TuesdayProvider,
        'columns' => $gridViewColumnList
    ]) ?>
    <?php
    // get current Mileage Card's Date
    $TuesdayDate = $MondayDate->modify('+1 day');
    $TuesdayStr = $TuesdayDate->format('Y-m-d');

    $url = urldecode(Url::to(['mileage-card/create-mileage-entry', 'mileageCardId' => $model["MileageCardID"], 'mileageCardTechId' => $model['MileageCardTechID'], 'mileageCardDate' => $TuesdayStr]));
    ?>
    <p>
        <?= Html::button('Create New', ['value' => $url, 'class' => 'btn btn-success', 'id' => 'mileageModalButtonTuesday', 'disabled' => $approve_status]) ?>
        <?php if ($Total_Mileage_Tue != 0) { ?>
            <span class="totalhours"><?php echo "Total mileage is : " . $Total_Mileage_Tue ?></span>
        <?php } else { ?>
            <span class="no_totalhours"></span>
        <?php } ?>
    </p>

    <br>

    <!--Wednesday TableView-->
    <h2 class="mileage_entry_header">Wednesday</h2>
    <?= GridView::widget([
        'dataProvider' => $WednesdayProvider,
        'columns' => $gridViewColumnList
    ]) ?>
    <?php
    // get current Mileage Card's Date
    $WednesdayDate = $TuesdayDate->modify('+1 day');
    $WednesdayStr = $WednesdayDate->format('Y-m-d');

    $url = urldecode(Url::to(['mileage-card/create-mileage-entry', 'mileageCardId' => $model["MileageCardID"], 'mileageCardTechId' => $model['MileageCardTechID'], 'mileageCardDate' => $WednesdayStr]));
    ?>
    <p>
        <?= Html::button('Create New', ['value' => $url, 'class' => 'btn btn-success', 'id' => 'mileageModalButtonWednesday', 'disabled' => $approve_status]) ?>
        <?php if ($Total_Mileage_Wed != 0) { ?>
            <span class="totalhours"><?php echo "Total mileage is : " . $Total_Mileage_Wed ?></span>
        <?php } else { ?>
            <span class="no_totalhours"></span>
        <?php } ?>
    </p>

    <br>

    <!--Thursday TableView-->
    <h2 class="mileage_entry_header">Thursday</h2>
    <?= GridView::widget([
        'dataProvider' => $ThursdayProvider,
        'columns' => $gridViewColumnList
    ]) ?>
    <?php
    // get current Mileage Card's Date
    $ThursdayDate = $WednesdayDate->modify('+1 day');
    $ThursdayStr = $ThursdayDate->format('Y-m-d');

    $url = urldecode(Url::to(['mileage-card/create-mileage-entry', 'mileageCardId' => $model["MileageCardID"], 'mileageCardTechId' => $model['MileageCardTechID'], 'mileageCardDate' => $ThursdayStr]));
    ?>
    <p>
        <?= Html::button('Create New', ['value' => $url, 'class' => 'btn btn-success', 'id' => 'mileageModalButtonThursday', 'disabled' => $approve_status]) ?>
        <?php if ($Total_Mileage_Thr != 0) { ?>
            <span class="totalhours"><?php echo "Total mileage is : " . $Total_Mileage_Thr ?></span>
        <?php } else { ?>
            <span class="no_totalhours"></span>
        <?php } ?>
    </p>

    <br>

    <!--Friday TableView-->
    <h2 class="mileage_entry_header">Friday</h2>
    <?= GridView::widget([
        'dataProvider' => $FridayProvider,
        'columns' => $gridViewColumnList
    ]) ?>
    <?php
    // get current Mileage Card's Date
    $FridayDate = $ThursdayDate->modify('+1 day');
    $FridayStr = $FridayDate->format('Y-m-d');

    $url = urldecode(Url::to(['mileage-card/create-mileage-entry', 'mileageCardId' => $model["MileageCardID"], 'mileageCardTechId' => $model['MileageCardTechID'], 'mileageCardDate' => $FridayStr]));
    ?>
    <p>
        <?= Html::button('Create New', ['value' => $url, 'class' => 'btn btn-success', 'id' => 'mileageModalButtonFriday', 'disabled' => $approve_status]) ?>
        <?php if ($Total_Mileage_Fri != 0) { ?>
            <span class="totalhours"><?php echo "Total mileage is : " . $Total_Mileage_Fri ?></span>
        <?php } else { ?>
            <span class="no_totalhours"></span>
        <?php } ?>
    </p>

    <br>

    <!--Saturday TableView-->
    <h2 class="mileage_entry_header">Saturday</h2>
    <?= GridView::widget([
        'dataProvider' => $SaturdayProvider,
        'columns' => $gridViewColumnList
    ]) ?>
    <?php
    // get current Mileage Card's Date
    $SaturdayDate = $FridayDate->modify('+1 day');
    $SaturdayStr = $SaturdayDate->format('Y-m-d');

    $url = urldecode(Url::to(['mileage-card/create-mileage-entry', 'mileageCardId' => $model["MileageCardID"], 'mileageCardTechId' => $model['MileageCardTechID'], 'mileageCardDate' => $SaturdayStr]));
    ?>
    <p>
        <?= Html::button('Create New', ['value' => $url, 'class' => 'btn btn-success', 'id' => 'mileageModalButtonSaturday', 'disabled' => $approve_status]) ?>
        <?php if ($Total_Mileage_Sat != 0) { ?>
            <span class="totalhours"><?php echo "Total mileage is : " . $Total_Mileage_Sat ?></span>
        <?php } else { ?>
            <span class="no_totalhours"></span>
        <?php } ?>
    </p>
    <?php Pjax::end(); ?>

    <?php
    // Sunday Time Entry Modal
    Modal::begin([
        'header' => '<h4>Sunday</h4>',
        'id' => 'mileageModalSunday',
        'size' => 'modal-lg',
    ]);

    echo "<div id='modalContentMileageSunday'></div>";
    Modal::end();

    // Monday Time Entry Modal
    Modal::begin([
        'header' => '<h4>Monday</h4>',
        'id' => 'mileageModalMonday',
        'size' => 'modal-lg',
    ]);

    echo "<div id='modalContentMileageMonday'></div>";
    Modal::end();

    // Tuesday Time Entry Modal
    Modal::begin([
        'header' => '<h4>Tuesday</h4>',
        'id' => 'mileageModalTuesday',
        'size' => 'modal-lg',
    ]);

    echo "<div id='modalContentMileageTuesday'></div>";
    Modal::end();

    // Wednesday Time Entry Modal
    Modal::begin([
        'header' => '<h4>Wednesday</h4>',
        'id' => 'mileageModalWednesday',
        'size' => 'modal-lg',
    ]);

    echo "<div id='modalContentMileageWednesday'></div>";
    Modal::end();

    // Thursday Time Entry Modal
    Modal::begin([
        'header' => '<h4>Thursday</h4>',
        'id' => 'mileageModalThursday',
        'size' => 'modal-lg',
    ]);

    echo "<div id='modalContentMileageThursday'></div>";
    Modal::end();

    // Friday Time Entry Modal
    Modal::begin([
        'header' => '<h4>Friday</h4>',
        'id' => 'mileageModalFriday',
        'size' => 'modal-lg',
    ]);

    echo "<div id='modalContentMileageFriday'></div>";
    Modal::end();

    // Saturday Time Entry Modal
    Modal::begin([
        'header' => '<h4>Saturday</h4>',
        'id' => 'mileageModalSaturday',
        'size' => 'modal-lg',
    ]);

    echo "<div id='modalContentMileageSaturday'></div>";
    Modal::end();
    ?>

</div>
