<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use app\controllers\MileageCard;
use yii\widgets\LinkPager;
use yii\widgets\Pjax;
use kartik\form\ActiveForm;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Mileage Cards';
$this->params['breadcrumbs'][] = $this->title;
$pageSize = ["50" => "50", "100" => "100", "200" => "200"];
//TODO rework this to handle new params
$this->params['download_url'] = '/mileage-card/download-mileage-card-data?' . http_build_query([
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
        'attribute' => 'MileageStartDate',
        'headerOptions' => ['class' => 'text-center'],
        'contentOptions' => ['class' => 'text-center'],
        'value' => function ($model) {
            return date("m/d/Y", strtotime($model['MileageStartDate']));
        }
    ],
    [
        'label' => 'End Date',
        'attribute' => 'MileageEndDate',
        'headerOptions' => ['class' => 'text-center'],
        'contentOptions' => ['class' => 'text-center'],
        'value' => function ($model) {
            return date("m/d/Y", strtotime($model['MileageEndDate']));
        }
    ],
	[
        'label' => 'Total Miles',
        'attribute' => 'MileageCardAllMileage_calc',
        'headerOptions' => ['class' => 'text-center'],
        'contentOptions' => ['class' => 'text-center'],
    ],
    [
        'label' => 'Approved',
        'attribute' => 'MileageCardApproved',
//        'filter' => $approvedInput
    ],

    ['class' => 'kartik\grid\ActionColumn',
        'template' => '{view}',
        'urlCreator' => function ($action, $model, $key, $index) {
            if ($action === 'view') {
                $url = '/mileage-card/view?id=' . $model["MileageCardID"];
                return $url;
            }
        },
    ],
    [
        'class' => 'kartik\grid\CheckboxColumn',
        'checkboxOptions' => function ($model, $key, $index, $column) {
            // Disable if already approved or SumHours is 0
            /*$disabledBoolean = strtoupper($model["MileageCardApproved"]) == "YES";
            $result = [
                'mileageCardId' => $model["MileageCardID"],
                'approved' => $model["MileageCardApproved"],
                'totalmileage' => $model["MileageCardAllMileage_calc"]
            ];
            if ($disabledBoolean) {
                $result['disabled'] = 'true';
            }

            return $result;*/
            //if (strtoupper($model["MileageCardApproved"]) == "YES")
              if (strtoupper($model["MileageCardApproved"]) == "YES"
                  || $model["MileageCardApproved"] == 1)
                return ['disabled' => true];
            else
                return ['disabled' => false];
        }
    ],
];
?>
<div class="mileagecard-index">
    <div class="lightBlueBar">
        <h3 class="title"><?= Html::encode($this->title) ?></h3>
        <div id="mileage_card_filter">
            <div id="mileage_card_approve_btn" class="col-xs-4 col-md-2 col-lg-2">
                <?php
                echo Html::button('Approve',
                    [
                        'class' => 'btn btn-primary multiple_approve_btn',
                        'id' => 'multiple_mileage_card_approve_btn',
                    ]);
                ?>
                <?php if ($pages->totalCount > 0) { ?>
                    <a id="export_mileagecard_btn" class="btn btn-primary" target="_blank"
                       href="<?= $this->params['download_url']; ?>">Export</a>
                <?php } ?>
            </div>
            <div id="mileageCardDropdownContainer" class="col-xs-8 col-md-10 col-lg-10">
                <?php Pjax::begin(['id' => 'mileageCardForm', 'timeout' => false]) ?>
                <?php $form = ActiveForm::begin([
                    'type' => ActiveForm::TYPE_HORIZONTAL,
                    'formConfig' => ['deviceSize' => ActiveForm::SIZE_SMALL],
                    'method' => 'get',
                    'options' => [
                        'id' => 'MileageCardForm',
                    ]
                ]); ?>
				<div class="col-md-2">
					<?= $form->field($model, 'dateRangeValue', ['labelSpan' => 3])->dropDownList($dateRangeDD, ['value' => $dateRangeValue, 'id' => 'mileageCardDateRange'])->label("Week"); ?>
				</div>
                <div id="datePickerContainer" style="float: left; width: auto; display: none;">
                    <?= $form->field($model, 'DateRangePicker', [
                        'showLabels' => false
                    ])->widget(DateRangePicker::classname(), [
                        'pluginOptions' => [
                        ],
                        'name'=>'date_range_2',
                        'presetDropdown'=>true,
                        'hideInput'=>true,
                        'pluginEvents' => [
                            "apply.daterangepicker" => "function(ev, picker) {
                                "." var jqTCDropDowns = $('#mileageCardDropdownContainer');
                                    var form = jqTCDropDowns.find(\"#MileageCardForm\");
                                    if (form.find(\".has-error\").length){
                                        return false;
                                    }
                                    $('#loading').show();
                                    $.pjax.reload({
                                        type: 'GET',
                                        url: form.attr(\"action\"),
                                        container: '#mileageCardGridview', // id to update content
                                        data: form.serialize(),
                                        timeout: 99999
                                    });
                                    $('#mileageCardGridview').on('pjax:success', function () {
                                        $('#loading').hide();
                                        applyOnClickListeners();
                                    });
                                    $('#mileageCardGridview').on('pjax:error', function () {
                                        $('#loading').hide();
                                        location.reload();
                                    });
                                    $('#datePickerContainer').css(\"display\", \"block\"); "."
                            }"],
                    ]); ?>
                </div>
				<div class="col-md-3">
					<?= $form->field($model, 'filter', ['labelSpan' => 3])->textInput(['value' => $mileageCardFilterParams, 'id' => 'mileageCardFilter'])->label("Search"); ?>
				</div>
                <?php echo Html::img('@web/logo/filter_clear_black.png', ['id' => 'mileageCardSearchCleanFilterButton']) ?>
                <div class="col-md-3" style="float:right;">
					<?= $form->field($model, 'pagesize', ['labelSpan' => 8])->dropDownList($pageSize, ['value' => $mileageCardPageSizeParams, 'id' => 'mileageCardPageSize'])->label("Records Per Page"); ?>
					<input id="mileageCardPageNumber" type="hidden" name="mileageCardPageNumber" value="1"/>	
				</div>				
                <?php ActiveForm::end(); ?>
                <?php Pjax::end() ?>
            </div>
        </div>
    </div>
    <!-- General Table Layout for displaying Mileage Card Information -->
    <div id="mileageCardGridViewContainer">
        <div id="mileageCardGV" class="mileageCardForm">
            <?php Pjax::begin(['id' => 'mileageCardGridview', 'timeout' => false]) ?>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'export' => false,
                'bootstrap' => false,
                'pjax' => true,
                'summary' => '',
                'columns' => $column
            ]); ?>
            <div id="MCPagination">
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
