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

$chosen = "";

$this->title = 'Time Cards';
//$this->params['breadcrumbs'][] = $this->title;
$pageSize = ["50" => "50", "100" => "100", "200" => "200"];
$this->params['download_url'] = '/time-card/download-time-card-data?' . http_build_query([
        'dateRangeValue' => $dateRangeValue
    ]);
$column = [
    [
        'label' => 'User Full Name',
        'attribute' => 'UserFullName',
        'headerOptions' => ['class' => 'text-center'],
        'contentOptions' => ['class' => 'text-center'],
    ]/*,
    [
        'label' => 'User Last Name',
        'attribute' => 'UserLastName',
        'headerOptions' => ['class' => 'text-center'],
        'contentOptions' => ['class' => 'text-center'],
    ]*/,
    [
        'label' => 'Project Name',
        'attribute' => 'ProjectName',
        'headerOptions' => ['class' => 'text-center'],
        'contentOptions' => ['class' => 'text-center'],
    ],
    [
        'label' => 'Start Date - End Date',
        'attribute' => 'TimeCardDates',
        'headerOptions' => ['class' => 'text-center'],
        'contentOptions' => ['class' => 'text-center'],
        /*'value' => function ($model) {
            $date = date("m/d/Y", strtotime($model['TimeCardStartDate']))." - ".date("m/d/Y", strtotime($model['TimeCardEndDate'])); 
            return $date;
        }*/
    ]/*,
    [
        'label' => 'End Date',
        'attribute' => 'TimeCardEndDate',
        'headerOptions' => ['class' => 'text-center'],
        'contentOptions' => ['class' => 'text-center'],
        'value' => function ($model) {
            return date("m/d/Y", strtotime($model['TimeCardEndDate']));
        }
    ]*/,
    'SumHours',
   
    [
        'label' => 'Oasis Submitted',
        'attribute' => 'TimeCardOasisSubmitted',
        'headerOptions' => ['class' => 'text-center'],
        'contentOptions' => ['class' => 'text-center'],
    ],
    [
        'label' => 'QB Submitted',
        'attribute' => 'TimeCardQBSubmitted',
        'headerOptions' => ['class' => 'text-center'],
        'contentOptions' => ['class' => 'text-center'],
    ],
     [
        'label' => 'Approved',
        'attribute' => 'TimeCardApprovedFlag',
    ],
    ['class' => 'kartik\grid\ActionColumn',
        'template' => '{view}', // does not include delete
        'urlCreator' => function ($action, $model, $key, $index) {

            if ($action === 'view') {
                $url = '/time-card/show-entries?id=' . $model["TimeCardID"].'&projectName='.$model['ProjectName']
                .'&fName='.$model['UserFirstName']
                .'&lName='.$model['UserLastName']
                .'&timeCardProjectID='.$model['TimeCardProjectID'];
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
                || $model["SumHours"] == "0" || $model["TimeCardApprovedFlag"] == 1;
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
    <div class="lightBlueBar" style="height: 100px;">
        <h3 class="title"><?= Html::encode($this->title) ?></h3>
        <div id="timecard_filter">
            <div id="timeCardDropdownContainer">
                <?php Pjax::begin(['id' => 'timeCardForm', 'timeout' => false]) ?>
                <?php $form = ActiveForm::begin([
                    'type' => ActiveForm::TYPE_HORIZONTAL,
                    'formConfig' => ['labelSpan' => 7, 'deviceSize' => ActiveForm::SIZE_SMALL],
                    'method' => 'get',
                    'options' => [
                        'id' => 'TimeCardForm',
                    ],
                ]); ?>

                <div class="row">
                    <div style="float: right;margin-top: -2%;width: 21%;">
                        <?= $form->field($model, 'pagesize', ['labelSpan' => 6])->dropDownList($pageSize, ['value' => $timeCardPageSizeParams, 'id' => 'timeCardPageSize'])->label("Records Per Page", [
                            'class' => 'TimeCardRecordsPerPage'
                        ]); ?>
                        <input id="timeCardPageNumber" type="hidden" name="timeCardPageNumber" value="1"/>
                    </div>
                </div>
                <div class="row">
                    <div id="multiple_time_card_approve_btn">
                        <?php
                            echo Html::button('Submit',
                                [
                                    'class' => 'btn btn-primary multiple_submit_btn',
                                    'id' => $approvedTimeCardExist ? 'multiple_submit_btn_id' : 'multiple_submit_btn_id_hidden',
                                    'disabled' => $submitReady ? false : 'disabled'
                                ]);
                            echo Html::button('Approve',
                                [
                                    'class' => 'btn btn-primary multiple_approve_btn',
                                    'id' => $approvedTimeCardExist ? 'multiple_approve_btn_id' : 'multiple_approve_btn_id_only'
                                ]);
                        ?>
                        <?php
                        if ($pages->totalCount > 0) {
                            ?>
                            <a id="export_timecard_btn" class="btn btn-primary" target="_blank"
                               href="<?= $this->params['download_url']; ?>" style="display: none">Export</a>
                        <?php } ?>

                    </div>
                </div>
                <div class="col-md-3 col-md-offset-1 TimeCardSearch">
                    <?= $form->field($model, 'filter', ['labelSpan' => 3])->textInput(['value' => $timeCardFilterParams, 'id' => 'timeCardFilter'])->label("Search"); ?>
                </div>
                <?php echo Html::img('@web/logo/filter_clear_black.png', ['id' => 'timeCardSearchCleanFilterButton']) ?>
                <div class="col-md-2 DateRangeDropDown">
                    <?= $form->field($model, 'dateRangeValue', ['labelSpan' => 3])->dropDownList($dateRangeDD, ['value' => $dateRangeValue, 'id' => 'timeCardDateRange'])->label("Week"); ?>
                </div> <!--show filter-->
                <?php if($showFilter) : ?>
                  <div class="col-md-2 projectFilterDD">
                     <?php $chosen = isset(Yii::$app->request->queryParams['DynamicModel']) ? Yii::$app->request->queryParams['DynamicModel'] : "";?>
                    <?=
                    
                     $form->field($model, 'projectName', ['labelSpan' => 3])->dropDownList($projectDropDown,
                     ['options' =>[
                        isset($chosen["projectName"]) ? $chosen["projectName"]:"" =>['selected'=>'true'] 
                     ],"id"=>"projectFilterDD"]
                     )->label("Project"); ?>
                </div>
                 <?php echo Html::img('@web/logo/filter_clear_black.png', ['id' => 'clearProjectFilterButton']) ?>
            <?php endif; ?>
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
                'showOnEmpty' => true,
                'emptyText' => 'No results found!',
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
