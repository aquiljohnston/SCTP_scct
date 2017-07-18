<?php
use kartik\form\ActiveForm;
use yii\helpers\Html;
use kartik\widgets\DepDrop;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\widgets\LinkPager;
use kartik\grid\GridView;
use yii\bootstrap\Modal;

$this->title = 'Inspection';
$this->params['breadcrumbs'][] = $this->title;
$pageSize = ["50" => "50", "100" => "100", "200" => "200"];
?>
<div class="inspection">
    <div id="inspectionTab">
        <h3 class="title"><?= Html::encode($this->title) ?></h3>
        <div id="inspection-dropDownList-form">
            <?php $form = ActiveForm::begin([
                'type' => ActiveForm::TYPE_VERTICAL,
                'options' => ['id' => 'inspectionActiveForm']
            ]); ?>
            <div id="inspectionTableDropdown">
                <span id="inspectionPageSizeLabel" style="float: right;">
                    <?= $form->field($model, 'pagesize')->dropDownList($pageSize,
                        ['value' => $inspectionPageSizeParams, 'id' => 'inspectionPageSize'])
                        ->label('Records Per Page', [
                            'class' => 'recordsPerPage'
                        ]); ?>
                </span>
                <div id="inspectionSearchContainer" class="col-xs-3 col-md-3 col-lg-3" style="float:left; margin-left: 60%;">
                    <div id="filtertitle" class="dropdowntitle" style="width: 100%;">
                        <?= $form->field($model, 'inspectionfilter')->textInput(['value' => $inspectionFilterParams, 'id' => 'inspectionFilter', 'placeholder' => 'Search'])->label(''); ?>
                    </div>
                </div>
                <input id="inspectionPageNumber" type="hidden" name="inspectionPageNumber" value="1"/>
                <input id="inspectionTableRecordsUpdate" type="hidden" name="inspectionTableRecordsUpdate"value="false">
            <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>

    <div id="inspectionGridViewContainer">
        <div id="inspectionTable">
            <?php Pjax::begin(['id' => 'inspectionGridview', 'timeout' => false]) ?>
            <?= GridView::widget([
                'id' => 'inspectionGV',
                'dataProvider' => $inspectionDataProvider, // Sent from inspectionController.php
                'export' => false,
                'pjax' => true,
                //'floatHeader' => true,
                'summary' => '',
                'columns' => [
                    [
                        'class' => 'kartik\grid\ExpandRowColumn',
                        'expandAllTitle' => 'Expand all',
                        'collapseTitle' => 'Collapse all',
                        'expandIcon' => '<span class="glyphicon glyphicon-expand"></span>',
                        'value' => function ($model, $key, $index, $column) {
                            /*if ($model['sectionCount'] == null){
                                return GridView::ROW_NONE;
                            }*/
                            return GridView::ROW_COLLAPSED;
                        },

                        'detailUrl' => Url::to(['inspections/view-inspection']),
                        'detailAnimationDuration' => 'fast'
                    ],
                    [
                        'label' => 'Map Grid',
                        'attribute' => 'MapGrid',
                        'headerOptions' => ['class' => 'text-center'],
                        'contentOptions' => ['class' => 'text-center'],
                        'format' => 'html',
                        /*'value' => function ($model) {
                            return "Office<br/>" . $model['Division'] . "<br/>" . $model['MapGrid'];
                        }*/
                    ],
                    [
                        'label' => 'Compliance Start',
                        'attribute' => 'ComplianceStart',
                        'headerOptions' => ['class' => 'text-center'],
                        'contentOptions' => ['class' => 'text-center'],
                        'format' => 'html',
                        /*'value' => function ($model) {
                            return "Start: " . $model['ComplianceStartDate'] . "<br/>End: " . $model['ComplianceEndDate'];
                        }*/
                    ],
                    [
                        'label' => 'Compliance End',
                        'attribute' => 'ComplianceEnd',
                        'headerOptions' => ['class' => 'text-center'],
                        'contentOptions' => ['class' => 'text-center'],
                    ],
                    [
                        'label' => 'Total Inspections Count',
                        'attribute' => 'TotalInspections',
                        'headerOptions' => ['class' => 'text-center'],
                        'contentOptions' => ['class' => 'text-center'],
                    ],
                    /*[
                        'header' => 'View Asset',
                        'class' => 'kartik\grid\ActionColumn',
                        'template' => '{view}',
                        'headerOptions' => ['class' => 'text-center', 'style' => 'visibility: hidden;'],
                        'buttons' => [
                            'view' => function($url, $model) {
                                $modalViewEventDetailInspection = "#modalViewEventDetailInspection";
                                $modalContentViewEventDetailInspection = "#modalContentViewEventDetailInspection";
                                return Html::a('', null, ['class' =>'glyphicon glyphicon-eye-open', 'onclick' => "viewAssetRowClicked('/dispatch/inspections/view-event?inspectionID=".$model['InspectionID']."','".$modalViewEventDetailInspection ."','".$modalContentViewEventDetailInspection."')"]);
                            }
                        ],
                        'urlCreator' => function ($action, $model, $key, $index) {
                        }
                    ],*/
                    [
                        'header' => 'View Asset',
                        'class' => 'kartik\grid\ActionColumn',
                        'template' => '{view}',
                        'headerOptions' => ['class' => 'text-center', 'style' => 'visibility: hidden;'],
                        'buttons' => [
                            'view' => function($url, $model) {
                                $modalViewSectionDetailInspection = "#modalViewSectionDetailInspection";
                                $modalContentViewSectionDetailInspection = "#modalContentViewSectionDetailInspection";
                                return Html::a('', null, ['class' =>'glyphicon glyphicon-eye-open', 'onclick' => "viewAssetRowClicked('/dispatch/inspections/view-section-detail-modal?mapGridSelected=" . $model['MapGrid']."&sectionNumberSelected="."','".$modalViewSectionDetailInspection ."','".$modalContentViewSectionDetailInspection."')"]);
                            }
                        ],
                        'urlCreator' => function ($action, $model, $key, $index) {
                        }
                    ],
                ],
                'beforeHeader' => [
                    [
                        'columns' => [
                            ['content' => 'Inspection', 'options' => ['colspan' => 12, 'class' => 'kv-table-caption text-center']],
                        ],
                    ]
                ],
            ]); ?>
            <div id="InspectionTablePagination">
                <?php
                // display pagination
                echo LinkPager::widget([
                    'pagination' => $pages,
                ]); ?>
            </div>
            <div class="GridviewTotalNumber">
                <?php echo "Showing " . ($pages->offset + 1) . "  to " . ($pages->offset + $pages->getPageSize()) . " of " . $pages->totalCount . " entries"; ?>
            </div>
            <?php Pjax::end() ?>
        </div>
    </div>

    <!--View Event Detail Modal-->
    <?php
    Modal::begin([
        'header' => '<h4>Event</h4>',
        'id' => 'modalViewEventDetailInspection',
        'size' => 'modal-lg',
    ]);

    ?>
    <div id='modalContentViewEventDetailInspection'>
        Loading...
    </div>
    <?php

    Modal::end();
    ?>

</div>


