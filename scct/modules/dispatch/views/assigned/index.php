<?php
use yii\helpers\Html;
use kartik\grid\GridView;
use app\controllers\Assigned;
use kartik\widgets\DepDrop;
use kartik\form\ActiveForm;
use yii\widgets\LinkPager;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\bootstrap\Modal;

$this->title = 'Assigned';
$this->params['breadcrumbs'][] = $this->title;
$pageSize = ["10" => "10", "25" => "25", "50" => "50", "100" => "100"];
?>
<div class="dispatch-assigned">
    <div id="assignedDropdownContainer">

        <h3 class="title"><?= Html::encode($this->title) ?></h3>

        <div id="Assigned-dropDownList-form">
            <?php $form = ActiveForm::begin([
                'type' => ActiveForm::TYPE_VERTICAL,
                //'method' => 'get',
                'options' => ['id' => 'AssignForm', 'data-pjax' => true],
            ]); ?>
            <div class="row">
                <div class="col-lg-9 col-md-9 col-xs-9">
                    <div id="assignedDropdownTitlesContainer">
                        <div class="division dropdowntitle col-lg-3 col-md-3 col-sm-3 col-xs-3">
                            <?php // division Dropdown
                            if (!empty($divisionDefaultVal)) {
                                $model->division = $divisionDefaultVal;
                            }
                            echo $form->field($model, 'division')->dropDownList($divisionList, ['id' => 'Assigned-division-id']); ?>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                        <label style="color: #0067a6; margin-bottom: 7px;"></label>
                        <?php Pjax::begin(['id' => 'assignButtons', 'timeout' => false]) ?>

                        <?php if ($canUnassign != 0) { ?>
                            <div id="assiunassignedButton">
                                <?php echo Html::button('UNASSIGN', ['class' => 'btn btn-primary',
                                    'id' => 'UnassignedButton']); ?>
                            </div>
                        <?php } else {
                            echo "";
                        } ?>
                        <?php Pjax::end() ?>
                    </div>

                </div>
                <div id="assignedSearchContainer">
                    <div id="filtertitle" class="dropdowntitle">
                        <?= $form->field($model, 'assignedfilter')->textInput(['value' => $assignedFilterParams, 'id' => 'assignedFilter'])->label('Search'); ?>
                    </div>
                    <?php echo Html::img('@web/logo/filter_clear_black.png', ['id' => 'assignedSearchCleanFilterButton']) ?>
                </div>
                <div class="col-lg-3 col-md-3 col-xs-3">
                    <span id="AssignedPageSizeLabel">
                        <?= $form->field($model, 'pagesize')->dropDownList($pageSize,
                            ['value' => $assignedPageSizeParams, 'id' => 'assignPageSize'])
                            ->label('Records Per Page', [
                                'class' => 'recordsPerPage'
                            ]); ?>
                    </span>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>

    <div id="assginedGridViewContainer">
        <div id="assignedTable">
            <?php Pjax::begin(['id' => 'assignedGridview', 'timeout' => false]) ?>
            <?= GridView::widget([
                'dataProvider' => $assignedDataProvider,
                'id' => 'assignedGV',
                'pjax' => true,
                'caption' => 'Assign',
                'pjaxSettings' => [
                    'options' => [
                        'enablePushState' => false,
                    ]
                ],
                'export' => false,
                'columns' => [
                    [
                        'label' => 'MapGrid',
                        'attribute' => 'MapGrid',
                        'headerOptions' => ['class' => 'text-center'],
                        'contentOptions' => ['class' => 'text-center'],
                        'format' => 'html',
                        /*'value' => function ($model) {
                            return "Office<br/>" . $model['Division'] . "<br/>" . $model['MapGrid'];
                        }*/
                    ],
                    [
                        'label' => 'Meter Number',
                        'attribute' => 'MeterNumber',
                        'headerOptions' => ['class' => 'text-center'],
                        'contentOptions' => ['class' => 'text-center'],
                        'format' => 'html',
                        /*'value' => function ($model) {
                            return "Start: " . $model['ComplianceStartDate'] . "<br/>End: " . $model['ComplianceEndDate'];
                        }*/
                    ],
                    [
                        'label' => 'Assigned To',
                        'attribute' => 'AssignedTo',
                        'headerOptions' => ['class' => 'text-center'],
                        'contentOptions' => ['class' => 'text-center'],
                        'format' => 'html',
                        /*'value' => function ($model) {
                            return "Start: " . $model['ComplianceStartDate'] . "<br/>End: " . $model['ComplianceEndDate'];
                        }*/
                    ],
                    [
                        'label' => 'InspectionType',
                        'attribute' => 'InspectionType',
                        'headerOptions' => ['class' => 'text-center'],
                        'contentOptions' => ['class' => 'text-center'],
                        'format' => 'html',
                        /*'value' => function ($model) {
                            return "Start: " . $model['ComplianceStartDate'] . "<br/>End: " . $model['ComplianceEndDate'];
                        }*/
                    ],
                    [
                        'label' => 'Assigned By',
                        'attribute' => 'AssignedBy',
                        'headerOptions' => ['class' => 'text-center'],
                        'contentOptions' => ['class' => 'text-center'],
                        'format' => 'html',
                        /*'value' => function ($model) {
                            return "Start: " . $model['ComplianceStartDate'] . "<br/>End: " . $model['ComplianceEndDate'];
                        }*/
                    ],
                    [
                        'class' => 'kartik\grid\ActionColumn',
                        'template' => '{view}',
                        'header' => 'View<br/>Assets',
                        'urlCreator' => function ($action, $model, $key, $index) {
                            /*if ($action === 'view') {
                                $url = '/dispatch/assets?id=' . $model['MapGrid']; //TODO: change to correct identifier.
                                return $url;
                            }*/
                            return "";
                        }
                    ],
                    [
                        'class' => 'kartik\grid\CheckboxColumn',
                        'contentOptions' => ['class' => 'unassignCheckbox'],
                        'checkboxOptions' => function ($model, $key, $index, $column) {
                            if ($model['WorkQueueStatus'] != 100) {
                                return ['disabled' => true];
                            } else {
                                return ['AssignedToID' => $model['AssignedToID'],'MapGrid' => $model['MapGrid'], 'disabled' => false ];
                            }
                        }
                    ]
                ]
            ]); ?>
            <div id="assignedPagination">
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
        </div>
        <?php Pjax::end() ?>
    </div>

    <!-- The Modal -->
    <div id="unassigned-message" class="modal">
        <!-- Modal content -->·
        <div class="modal-content">
            <div class="modal-header">
                <h3>Do you want to<br>un-assign the selected surveyors?</h3>
            </div>
            <div class="modal-body">
                <p>Press confirm to continue to un-assign <br> the selected surveyors. </p>
                <div id="unassignedConfirmButton" class="unassignedbtn">
                    <?php echo Html::button('Confirm', ['class' => 'btn', 'id' => 'unassignedConfirmBtn']); ?>
                </div>
                <div id="unassignedCancelButton" class="unassignedbtn">
                    <?php echo Html::button('Cancel', ['class' => 'btn', 'id' => 'unassignedCancelBtn']); ?>
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
    <div id="dialog-unassign" title="Unassign" style="display:none;">
        <p>Unassigned successfully.</p>
    </div>

    <?php
    Modal::begin([
        'header' => '<h4>ADD SURVEYORS TO FLOC SURVEY</h4>',
        'id' => 'addSurveyorModal',
    ]);
    echo "<div id='modalAddSurveyor'>Loading...</div>";
    Modal::end();
    ?>
    <div id="dialog-add-surveyor" title="Add New Surveyor" style="display: none">
        <p>New surveyor(s) has been added successfully.</p>
    </div>


