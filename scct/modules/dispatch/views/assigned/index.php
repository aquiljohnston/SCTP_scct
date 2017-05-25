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

            <div id="assignedDropdownTitlesContainer">
                <div class="division dropdowntitle">
                    <?php // division Dropdown
                    if (!empty($divisionDefaultVal)) {
                        $model->division = $divisionDefaultVal;
                    }
                    echo $form->field($model, 'division')->dropDownList($divisionList, ['id' => 'Assigned-division-id']); ?>
                </div>
                <div class="workcenter dropdowntitle">
                    <?php // workCenter Dropdown
                    if ((!empty($workCenterDefaultVal)))
                        $workCenterParams = $workCenterDefaultVal;
                    echo $form->field($model, 'workcenter')->widget(DepDrop::classname(), [
                        'options' => ['id' => 'Assigned-workcenter-id'],
                        'data' => [$workCenterParams => $workCenterParams],
                        'pluginOptions' => [
                            'initialize' => true,
                            'depends' => ['Assigned-division-id'],
                            'placeholder' => 'Select...',
                            'url' => Url::to(['assigned/getworkcenter'])
                        ]
                    ]); ?>
                </div>
            </div>
            <label id="AssignedPageSizeLabel">
                <?= $form->field($model, 'pagesize')->dropDownList($pageSize, ['value' => $assignedPageSizeParams, 'id' => 'assignPageSize'])->label(''); ?>
                <span>Records Per Page</span>
            </label>
            <input id="AssignedTableRecordsUpdate" type="hidden" name="AssignedTableRecordsUpdate" value="no"/>
            <input id="pageNumber" type="hidden" name="pageNumber" value="1"/>
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
                        'label' => 'Division',
                        'attribute' => 'division',
                        'format' => 'html',
                        'value' => function ($model) {
                            return "Office<br/>" . $model['Division'] . "<br/>" . $model['MapGrid'];
                        }
                    ],
                    [
                        'label' => 'Compliance Date',
                        'attribute' => 'complianceDate',
                        'format' => 'html',
                        'value' => function ($model) {
                            return "Start: " . $model['ComplianceStartDate'] . "<br/>End: " . $model['ComplianceEndDate'];
                        }
                    ],
                    [
                        'class' => 'kartik\grid\ActionColumn',
                        'template' => '{view}',
                        'header' => 'View<br/>Assets',
                        'urlCreator' => function ($action, $model, $key, $index) {
                            if ($action === 'view') {
                                $url = '/dispatch/assets?id=' . $model['MapGrid']; //TODO: change to correct identifier.
                                return $url;
                            }
                            return "";
                        }
                    ],
                    [
                        'class' => 'kartik\grid\CheckboxColumn'
                    ]
                ]
            ]); ?>
            <!--<div id="assignedPagination">
                <?php
            /*                // display pagination
                            echo LinkPager::widget([
                                'pagination' => $pages,
                            ]);
                            */ ?>
            </div>
            <div class="GridviewTotalNumber">
                <?php /*echo "Showing " . ($pages->offset + 1) . "  to " . ($pages->offset + $pages->getPageSize()) . " of " . $pages->totalCount . " entries"; */ ?>
            </div>-->
        </div>
        <?php Pjax::end() ?>
    </div>
    <?php Pjax::begin(['id' => 'assignButtons', 'timeout' => false]) ?>
    <div id="addSurveyorButtonDispatch">
    <?php if ($canUnassign != 0) { ?>
        <div id="assiunassignedButton">
            <?php echo Html::button('UNASSIGN', ['class' => 'btn btn-primary', 'id' => 'UnassignedButton']); ?>
        </div>
    <?php } else {
        echo "";
    } ?>
    <?php if ($canAddSurveyor != 0) { ?>
        <div id="addSurveyorButton">
            <?php echo Html::button('ADD SURVEYOR', ['class' => 'btn btn-primary', 'id' => 'addSurveyor']); ?>
        </div>
    <?php } else {
        echo "";
    } ?>
        <div>
    <?php Pjax::end() ?>

    <!-- The Modal -->
    <div id="unassigned-message" class="modal">
        <!-- Modal content -->
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
