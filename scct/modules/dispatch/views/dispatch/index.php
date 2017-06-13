<?php
use kartik\form\ActiveForm;
use yii\helpers\Html;
use kartik\widgets\DepDrop;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\widgets\LinkPager;
use kartik\grid\GridView;
use yii\bootstrap\Modal;

$this->title = 'Dispatch';
$this->params['breadcrumbs'][] = $this->title;
$pageSize = ["50" => "50", "100" => "100", "200" => "200"];
?>
<div class="dispatch">
    <div id="blue-header">
        <div id="dispatchTab">
            <h3 class="title"><?= Html::encode($this->title) ?></h3>
            <div id="dispatch-dropDownList-form">
                <?php $form = ActiveForm::begin([
                    'type' => ActiveForm::TYPE_VERTICAL,
                    'options' => ['id' => 'dispatchActiveForm']
                ]); ?>
                <div id="dispatchUnassignedTableDropdown">
                    <?php echo Html::img('@web/logo/filter_clear_black.png', ['id' => 'dipatchSearchCleanFilterButton'])  ?>
                    <div id="dispatchSearchContainer" class="col-xs-2 col-md-2 col-lg-2" style="float:right">
                        <div id="filtertitle" class="dropdowntitle">
                            <?= $form->field($model, 'dispatchfilter')->textInput(['value' => $dispatchFilterParams, 'id' => 'dispatchFilter'])->label('Search'); ?>
                        </div>
                    </div>
                    <span id="dispatchPageSizeLabel" style="float: right;">
                        <?= $form->field($model, 'pagesize')->dropDownList($pageSize,
                            ['value' => $dispatchPageSizeParams, 'id' => 'dispatchPageSize'])
                            ->label('Records Per Page', [
                                'class' => 'recordsPerPage'
                            ]); ?>
                    </span>
                <?php ActiveForm::end(); ?>
                <?php Pjax::begin(['id' => 'dispatchBtnPjax', 'timeout' => false]) ?>
                <div id="addSurveyorButtonDispatch" class="col-xs-2 col-md-2 col-lg-2">

                    <?php if ($can != 0) { ?>
                        <?php echo Html::button('ADD SURVEYOR', ['class' => 'btn btn-primary dispatch_btn', 'id' => 'dispatchButton']); ?>
                    <?php } else {
                        echo "";
                    } ?>
                </div>
                <?php Pjax::end() ?>
                </div>
            </div>
        </div>
    </div>


    <div id="dispatchGridViewContainer">
        <div id="dispatchUnassignedTable">
            <?php Pjax::begin(['id' => 'dispatchUnassignedGridview', 'timeout' => false]) ?>
            <?= GridView::widget([
                'id' => 'dispatchGV',
                'dataProvider' => $dispatchDataProvider, // Sent from DispatchController.php
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

                        'detailUrl' => Url::to(['dispatch/view-section'])
                        /*$searchModel = new CreateBookingsSearch();
                        $searchModel->booking_id = $model ->id;
                        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

                        return Yii::$app->controller->renderPartial('_expandrowview.php',[
                            'searchModel' => $searchModel,
                            'dataProvider' => $dataProvider,
                        ]);*/
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
                        'label' => 'Available Work Order',
                        'attribute' => 'AvailableWorkOrderCount',
                        'headerOptions' => ['class' => 'text-center'],
                        'contentOptions' => ['class' => 'text-center'],
                    ],
                    [
                        'class' => 'kartik\grid\ActionColumn',
                        'template' => '{view}',
                        'header' => 'View<br/>Assets',
                        'urlCreator' => function ($action, $model, $key, $index) {
                            /*if ($action === 'view') {
                                $url = '/dispatch/view-asset?id=' . $model['MapGrid']; //TODO: change to correct identifier.
                                return $url;
                            }
                            return "";*/
                        }
                    ],
                    [
                        'header' => 'Add Surveyor',
                        'class' => 'kartik\grid\CheckboxColumn',
                        'contentOptions' => ['class' => 'dispatchCheckbox'],
                        'checkboxOptions' => function ($model, $key, $index, $column) {
                            if (empty($model['SectionNumber']))
                                return ['SectionNumber' => '000', 'MapGrid' => $model['MapGrid'], 'disabled' => false];
                            else
                                return ['SectionNumber' => $model['SectionNumber'], 'MapGrid' => $model['MapGrid'], 'disabled' => false];
                        }
                    ]
                ],
                'beforeHeader' => [
                    [
                        'columns' => [
                            ['content' => 'Dispatch', 'options' => ['colspan' => 12, 'class' => 'kv-table-caption text-center']],
                        ],
                    ]
                ],
            ]); ?>
            <div id="unassignedTablePagination">
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

    <!-- The Modal -->
    <div id="dispatch-message" class="modal" style="display:none">
        <!-- Modal content -->
        <div class="modal-content">
            <div class="modal-body">
                <h4>Do you want to abandon your changes?</h4>
                <div id="dispatchNoButton" class="dispatchModalbtn">
                    <?php echo Html::button('No', ['class' => 'btn', 'id' => 'dispatchNoBtn']); ?>
                </div>
                <div id="dispatchYesButton" class="dispatchModalbtn">
                    <?php echo Html::button('Yes', ['class' => 'btn', 'id' => 'dispatchYesBtn']); ?>
                </div>
            </div>
        </div>

    </div>

    <?php

    Modal::begin([
        'header' => '<h4>ADD SURVEYORS TO FLOC SURVEY</h4>',
        'id' => 'addSurveyorModal',
    ]);
    echo "<div id='modalAddSurveyor'>Loading...</div>";
    Modal::end();
    ?>

    <div id="dialog-dispatch" title="Dispatch" style="display:none">
        <p>Dispatched Successfully.</p>
    </div>
</div>


