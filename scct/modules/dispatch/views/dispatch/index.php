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
$pageSize = ["10" => "10", "25" => "25", "50" => "50", "100" => "100"];
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
                    <div class="division dropdowntitle">
                        <?php // division Dropdown
                        echo $form->field($model, 'division')->dropDownList($divisionList, ['id' => 'dispatch-division-id'])->label('Division'); ?>
                    </div>
                    <div id="dispatchSearchContainer">
                        <div id="filtertitle" class="dropdowntitle">
                            <?= $form->field($model, 'dispatchfilter')->textInput(['value' => $dispatchFilterParams, 'id' => 'dispatchFilter'])->label('Search'); ?>
                        </div>
                        <?php /*echo Html::img('@web/logo/filter_clear_black.png', ['id' => 'dipatchSearchCleanFilterButton']) */?>
                    </div>
                    <div class="col-lg-3 col-md-3 col-xs-3">
                    <span id="dispatchPageSizeLabel">
                        <?= $form->field($model, 'pagesize')->dropDownList($pageSize,
                            ['value' => $dispatchPageSizeParams, 'id' => 'dispatchPageSize'])
                            ->label('Records Per Page', [
                                'class' => 'recordsPerPage'
                            ]); ?>
                    </span>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
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
                        'label' => 'ClientWorkOrderID',
                        'attribute' => 'ClientWorkOrderID',
                        'headerOptions' => ['class' => 'text-center'],
                        'contentOptions' => ['class' => 'text-center'],
                        'format' => 'html',
                        /*'value' => function ($model) {
                            return "Office<br/>" . $model['Division'] . "<br/>" . $model['MapGrid'];
                        }*/
                    ],
                    [
                        'label' => 'CreatedBy',
                        'attribute' => 'CreatedBy',
                        'headerOptions' => ['class' => 'text-center'],
                        'contentOptions' => ['class' => 'text-center'],
                        'format' => 'html',
                        /*'value' => function ($model) {
                            return "Start: " . $model['ComplianceStartDate'] . "<br/>End: " . $model['ComplianceEndDate'];
                        }*/
                    ],
                    [
                        'label' => 'CreatedDateTime',
                        'attribute' => 'CreatedDateTime',
                        'headerOptions' => ['class' => 'text-center'],
                        'contentOptions' => ['class' => 'text-center'],
                    ],
                    [
                        'class' => 'kartik\grid\ActionColumn',
                        'template' => '{view}',
                        'header' => 'View<br/>Assets',
                        /*'urlCreator' => function ($action, $model, $key, $index) {
                            if ($action === 'view') {
                                $url = '/dispatch/assets?id=' . $model['MapGrid']; //TODO: change to correct identifier.
                                return $url;
                            }
                            return "";
                        }*/
                    ],
                    [
                        'header' => 'Add Surveyor',
                        'class' => 'kartik\grid\CheckboxColumn'
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
                <?php echo "Showing " . ($pages->offset + 1) . "  to " . ($pages->offset + $pages->getPageSize()) . " of " . $pages->totalCount . " entries";  ?>
            </div>
            <?php Pjax::end() ?>
        </div>
    </div>
    <?php Pjax::begin(['id' => 'dispatchBtnPjax', 'timeout' => false]) ?>
    <div id="addSurveyorButtonDispatch">

        <?php if ($can != 0) { ?>
            <?php echo Html::button('ADD SURVEYOR', ['class' => 'btn btn-primary dispatch_btn', 'id' => 'dispatchButton']); ?>
        <?php } else {
            echo "";
        } ?>
    </div>
    <?php Pjax::end() ?>

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


