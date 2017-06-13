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
$pageSize = ["50" => "50", "100" => "100", "200" => "200"];
?>
<div class="dispatch-assigned">
    <div id="assignedDropdownContainer" style="height: 105px;">

        <h3 class="title"><?= Html::encode($this->title) ?></h3>

        <div id="Assigned-dropDownList-form">
            <?php $form = ActiveForm::begin([
                'type' => ActiveForm::TYPE_VERTICAL,
                //'method' => 'get',
                'options' => ['id' => 'AssignForm', 'data-pjax' => true],
            ]); ?>
                <span id="AssignedPageSizeLabel">
                        <?= $form->field($model, 'pagesize')->dropDownList($pageSize,
                            ['value' => $assignedPageSizeParams, 'id' => 'assignPageSize'])
                            ->label('Records Per Page', [
                                'class' => 'recordsPerPage'
                            ]); ?>
                </span>
                <div class="col-xs-1 col-md-1 col-lg-1" style="float: right; margin: 0px auto; width: 11%;">
                    <label style="color: #0067a6; margin-bottom: 7px;"></label>
                    <?php Pjax::begin(['id' => 'assignButtons', 'timeout' => false]) ?>

                    <?php if ($canUnassign != 0) { ?>
                        <div id="assiunassignedButton">
                            <?php echo Html::button('Remove Surveyor', ['class' => 'btn btn-primary',
                                'id' => 'UnassignedButton']); ?>
                        </div>
                    <?php } else {
                        echo "";
                    } ?>
                    <?php Pjax::end() ?>
                </div>

                <div id="assignedSearchContainer">
                    <div id="filtertitle" class="dropdowntitle">
                        <?= $form->field($model, 'assignedfilter')->textInput(['value' => $assignedFilterParams, 'id' => 'assignedFilter'])->label('Search'); ?>
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
                'summary' => false,
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
                        'visible' => false
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
</div>


