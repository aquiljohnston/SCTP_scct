<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\widgets\Pjax;
use app\controllers\TimeCard;
use kartik\form\ActiveForm;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Time Cards';
$this->params['breadcrumbs'][] = $this->title;
$pageSize = ["50" => "50", "100" => "100", "200" => "200"];
$this->params['download_url'] = '/time-card/download-time-card-data?' . http_build_query([
        'dateRange' => $dateRangeValue
    ]);
$column = [
    [
        'label' => 'User First Name',
        'attribute' => 'UserFirstName',
        'headerOptions' => ['class' => 'text-center'],
        'contentOptions' => ['class' => 'text-center'],
    ],
    [
        'label' => 'User Last Name',
        'attribute' => 'UserLastName',
        'headerOptions' => ['class' => 'text-center'],
        'contentOptions' => ['class' => 'text-center'],
    ],
    [
        'label' => 'Project Name',
        'attribute' => 'ProjectName',
        'headerOptions' => ['class' => 'text-center'],
        'contentOptions' => ['class' => 'text-center'],
    ],
    [
        'label' => 'Start Date',
        'attribute' => 'TimeCardStartDate',
        'headerOptions' => ['class' => 'text-center'],
        'contentOptions' => ['class' => 'text-center'],
        'value' => function ($model) {
            return date("m/d/Y", strtotime($model['TimeCardStartDate']));
        }
    ],
    [
        'label' => 'End Date',
        'attribute' => 'TimeCardEndDate',
        'headerOptions' => ['class' => 'text-center'],
        'contentOptions' => ['class' => 'text-center'],
        'value' => function ($model) {
            return date("m/d/Y", strtotime($model['TimeCardEndDate']));
        }
    ],
    'SumHours',
    [
        'label' => 'Approved',
        'attribute' => 'TimeCardApprovedFlag',
    ],
    ['class' => 'kartik\grid\ActionColumn',
        'template' => '{view}', // does not include delete
        'urlCreator' => function ($action, $model, $key, $index) {
            if ($action === 'view') {
                $url = '/time-card/show-entries?id=' . $model["TimeCardID"];
                return $url;
            }
        },
        'buttons' => [
            // Currently unused due to template string above
            'delete' => function ($url, $model, $key) {
                $url = '/time-card/delete?id=' . $model["TimeCardID"];
                $options = [
                    'title' => Yii::t('yii', 'Delete'),
                    'aria-label' => Yii::t('yii', 'Delete'),
                    'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                    'data-method' => 'Delete',
                    'data-pjax' => '0',
                ];
                return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, $options);
            },
        ],
    ],
    [
        'class' => 'kartik\grid\CheckboxColumn',
        'checkboxOptions' => function ($model, $key, $index, $column) {
            // Disable if already approved or SumHours is 0
            $disabledBoolean = strtoupper($model["TimeCardApprovedFlag"]) == "YES"
                || $model["SumHours"] == "0";
            $result = [
                'timecardid' => $model["TimeCardID"],
                'approved' => $model["TimeCardApprovedFlag"],
                'totalworkhours' => $model["SumHours"]
            ];
            if ($disabledBoolean) {
                $result['disabled'] = 'true';
            }

            return $result;
        }
        /*'pageSummary' => true,
        'rowSelectedClass' => GridView::TYPE_SUCCESS,
        'contentOptions'=>['style'=>'width: 0.5%'],*/
    ],
];
?>

<div class="timecard-index">
    <div class="lightBlueBar">
        <h3 class="title"><?= Html::encode($this->title) ?></h3>
        <div id="timecard_filter">
            <div id="multiple_time_card_approve_btn" class="col-xs-4 col-md-3 col-lg-2">
                <?php
                echo Html::button('Approve',
                    [
                        'class' => 'btn btn-primary multiple_approve_btn',
                        'id' => 'multiple_approve_btn_id',
                    ]);
                ?>
                <?php
                if ($pages->totalCount > 0) {
                    ?>
                    <a id="export_timecard_btn" class="btn btn-primary" target="_blank"
                       href="<?= $this->params['download_url']; ?>">Export</a>
                <?php } ?>

            </div>
            <div id="timeCardDropdownContainer" class="col-xs-8 col-md-9 col-lg-10">
                <?php Pjax::begin(['id' => 'timeCardForm', 'timeout' => false]) ?>
                <?php $form = ActiveForm::begin([
                    'type' => ActiveForm::TYPE_HORIZONTAL,
                    'formConfig' => ['labelSpan' => 7, 'deviceSize' => ActiveForm::SIZE_SMALL],
                    'method' => 'get',
                    'options' => [
                        'id' => 'TimeCardForm',
                    ],
                ]); ?>
                <div class="col-md-2" >
                    <?= $form->field($model, 'dateRangeValue', ['labelSpan' => 3])->dropDownList($dateRangeDD, ['value' => $dateRangeValue, 'id' => 'timeCardDateRange'])->label("Week"); ?>
                </div>
                <div id="datePickerContainer" style="float: left; width: auto; display: none;">
                    <?= $form->field($model, 'DateRangePicker', [
                        'showLabels' => false
                    ])->widget(DateRangePicker::classname(), [
                        'pluginOptions' => [
                        ],
                        'name'=>'date_range_3',
                        'presetDropdown'=>true,
                        'hideInput'=>true,
                        'pluginEvents' => [
                            "apply.daterangepicker" => "function(ev, picker) {
                                "." var jqTCDropDowns = $('#timeCardDropdownContainer');
                                    var form = jqTCDropDowns.find(\"#TimeCardForm\");
                                    if (form.find(\".has-error\").length){
                                        return false;
                                    }
                                    $('#loading').show();
                                    $.pjax.reload({
                                        type: 'GET',
                                        url: form.attr(\"action\"),
                                        container: '#timeCardGridview', // id to update content
                                        data: form.serialize(),
                                        timeout: 99999
                                    });
                                    $('#timeCardGridview').on('pjax:beforeSend', function () {
                                        console.log(form.serialize());
                                    });
                                    $('#timeCardGridview').on('pjax:success', function () {
                                        $('#loading').hide();
                                        applyOnClickListeners();
                                    });
                                    $('#timeCardGridview').on('pjax:error', function () {
                                        $('#loading').hide();
                                        location.reload();
                                    });
                                    $('#datePickerContainer').css(\"display\", \"block\"); "."
                            }"],
                    ]); ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($model, 'filter', ['labelSpan' => 3])->textInput(['value' => $timeCardFilterParams, 'id' => 'timeCardFilter'])->label("Search"); ?>
                </div>
                <?php echo Html::img('@web/logo/filter_clear_black.png', ['id' => 'timeCardSearchCleanFilterButton']) ?>
                <div class="col-md-3" style="float:right;">
                    <?= $form->field($model, 'pagesize', ['labelSpan' => 8])->dropDownList($pageSize, ['value' => $timeCardPageSizeParams, 'id' => 'timeCardPageSize'])->label("Records Per Page"); ?>
                    <input id="timeCardPageNumber" type="hidden" name="timeCardPageNumber" value="1"/>
                </div>
                <?php ActiveForm::end(); ?>
                <?php Pjax::end() ?>
            </div>
        </div>
    </div>
    <div id="timeCardGridViewContainer">
        <div id="timeCardGV" class="timeCardForm">
            <?php Pjax::begin(['id' => 'timeCardGridview', 'timeout' => false]) ?>
            <?= GridView::widget([
                'id' => 'GridViewForTimeCard',
                'dataProvider' => $dataProvider,
                'export' => false,
                'bootstrap' => false,
                'pjax' => true,
                'summary' => '',
                'columns' => $column
            ]); ?>
            <div id="TCPagination">
                <?php
                // display pagination
                echo LinkPager::widget([
                    'pagination' => $pages,
                ]);
                ?>
            </div>
            <div class="GridviewTotalNumber">
                <?php echo "Showing " . ($pages->offset + 1) . "  to " . ($pages->offset + $pages->getPageSize()) . " of " . $pages->totalCount . " entries"; ?>
            </div>
            <?php Pjax::end() ?>
        </div>
    </div>
</div>
