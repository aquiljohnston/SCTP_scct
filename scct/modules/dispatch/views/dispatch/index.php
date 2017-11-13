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
    <div id="dispatchTab">
        <h3 class="title"><?= Html::encode($this->title) ?></h3>
        <div id="dispatch-dropDownList-form">
            <?php $form = ActiveForm::begin([
                'type' => ActiveForm::TYPE_VERTICAL,
                'options' => ['id' => 'dispatchActiveForm'],
                'action' => '/dispatch/dispatch/heavy-dispatch'
            ]); ?>
            <div id="dispatchUnassignedTableDropdown">
                <span id="dispatchPageSizeLabel" style="float: right;">
                    <?= $form->field($model, 'pagesize')->dropDownList($pageSize,
                        ['value' => $dispatchPageSizeParams, 'id' => 'dispatchPageSize'])
                        ->label('Records Per Page', [
                            'class' => 'recordsPerPage'
                        ]); ?>
                </span>
                <div id="dispatchSearchContainer" class="col-xs-3 col-md-3 col-lg-3">
                    <div id="filtertitle" class="dropdowntitle" style="width: 100%;">
                        <?= $form->field($model, 'dispatchfilter')->textInput(['value' => $dispatchFilterParams, 'id' => 'dispatchFilter', 'placeholder' => 'Search'])->label(''); ?>
                    </div>
                </div>
                <input id="dispatchPageNumber" type="hidden" name="dispatchPageNumber" value="1"/>
                <input id="dispatchTableRecordsUpdate" type="hidden" name="dispatchTableRecordsUpdate"value="false">
            <?php ActiveForm::end(); ?>
            <?php Pjax::begin(['id' => 'dispatchBtnPjax', 'timeout' => false]) ?>
            <div id="addSurveyorButtonDispatch" class="col-xs-1 col-md-1 col-lg-1">
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

    <div id="dispatchGridViewContainer">
        <div id="dispatchUnassignedTable">
            <?php Pjax::begin(['id' => 'dispatchUnassignedGridview', 'timeout' => false]) ?>
            <?= GridView::widget([
                'id' => 'dispatchGV',
                'dataProvider' => $dispatchDataProvider, // Sent from DispatchController.php
                'export' => false,
                'pjax' => true,
                'floatHeader' => true,
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

                        'detailUrl' => Url::to(['dispatch/view-section']),
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
                        'label' => 'Division',
                        'attribute' => 'Division',
                        'headerOptions' => ['class' => 'text-center'],
                        'contentOptions' => ['class' => 'text-center'],
						'visible' => $divisionFlag,
                        'format' => 'html',
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
                        'contentOptions' => [
                            'class' => 'ViewAssetBtn_DispatchMapGrid'
                        ],
                        'buttons' => [
                            'view' => function($url, $model) {
                                $modalViewAssetDispatch = "#modalViewAssetDispatch";
                                $modalContentViewAssetDispatch = "#modalContentViewAssetDispatch";
                                return Html::a('', null, ['class' =>'glyphicon glyphicon-eye-open', 'onclick' => "viewAssetRowClicked('/dispatch/dispatch/view-asset?mapGridSelected=" . $model['MapGrid']."','".$modalViewAssetDispatch ."','".$modalContentViewAssetDispatch."','".$model['MapGrid']."')"]);
                            }
                        ],
                        'urlCreator' => function ($action, $model, $key, $index) {
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
                'floatOverflowContainer' => true,
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

    <!--View Asset Modal-->
    <?php
    Modal::begin([
		'header' => '<h4 id="assetModalTitle"></h4>',
        'id' => 'modalViewAssetDispatch',
        'size' => 'modal-lg',
    ]);

    ?>
    <div id='modalContentViewAssetDispatch'>
        Loading...
    </div>
    <?php

    Modal::end();
    ?>
</div>


