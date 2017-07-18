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
                        <?= $form->field($model, 'assignedfilter')->textInput(['value' => $assignedFilterParams, 'id' => 'assignedFilter', 'placeholder' => 'Search'])->label(''); ?>
                    </div>
                </div>
            <input id="assignedTableRecordsUpdate" type="hidden" name="assignedTableRecordsUpdate" value="no" />
            <input id="assignedPageNumber" type="hidden" name="assignedPageNumber" value="1" />
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
                        'class' => 'kartik\grid\ExpandRowColumn',
                        'headerOptions' => ['class' => 'text-center', 'style' => 'width:5.1%'],
                        'contentOptions' => ['class' => 'text-center', 'style' => 'width:5.1%'],
                        'expandAllTitle' => 'Expand all',
                        'collapseTitle' => 'Collapse all',
                        'expandIcon' => '<span class="glyphicon glyphicon-expand"></span>',
                        'value' => function ($model, $key, $index, $column) {
                            /*if ($model['sectionCount'] == null){
                                return GridView::ROW_NONE;
                            }*/
                            return GridView::ROW_COLLAPSED;
                        },

                        'detailUrl' => Url::to(['assigned/view-section']),
                        'detailAnimationDuration' => 'fast'
                        /*$searchModel = new CreateBookingsSearch();
                        $searchModel->booking_id = $model ->id;
                        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

                        return Yii::$app->controller->renderPartial('_expandrowview.php',[
                            'searchModel' => $searchModel,
                            'dataProvider' => $dataProvider,
                        ]);*/
                    ],
                    [
                        'label' => 'MapGrid',
                        'attribute' => 'MapGrid',
                        'headerOptions' => ['class' => 'text-center', 'style' => 'width: 16%'],
                        'contentOptions' => ['class' => 'text-center', 'style' => 'width: 16%'],
                        'format' => 'html',
                        /*'value' => function ($model) {
                            return "Office<br/>" . $model['Division'] . "<br/>" . $model['MapGrid'];
                        }*/
                    ],
                    [
                        'label' => 'Assigned User(s)',
                        'attribute' => 'SearchString',
                        'headerOptions' => ['class' => 'text-center', 'style' => 'width: 16.3%'],
                        'contentOptions' => ['class' => 'text-center', 'style' => 'width: 16.3%'],
                        'format' => 'html',
                        'value' => function ($model) {
                            if ($model['AssignedCount'] == "MANY")
                                return "MANY";
                            else
                                return $model['SearchString'];
                        }
                    ],
                    [
                        'label' => 'Compliance Start',
                        'attribute' => 'ComplianceStart',
                        'headerOptions' => ['class' => 'text-center', 'style' => 'width: 20%'],
                        'contentOptions' => ['class' => 'text-center', 'style' => 'width: 20%'],
                        'format' => 'html',
                        /*'value' => function ($model) {
                            return "Start: " . $model['ComplianceStartDate'] . "<br/>End: " . $model['ComplianceEndDate'];
                        }*/
                    ],
                    [
                        'label' => 'Compliance End',
                        'attribute' => 'ComplianceEnd',
                        'headerOptions' => ['class' => 'text-center', 'style' => 'width: 20%'],
                        'contentOptions' => ['class' => 'text-center', 'style' => 'width: 20%'],
                        'format' => 'html',
                        /*'value' => function ($model) {
                            return "Start: " . $model['ComplianceStartDate'] . "<br/>End: " . $model['ComplianceEndDate'];
                        }*/
                    ],
                    [
                        'label' => 'Completed (%)',
                        'attribute' => 'Percent Completed',
                        'headerOptions' => ['class' => 'text-center', 'style' => 'width: 5%'],
                        'contentOptions' => ['class' => 'text-center', 'style' => 'width: 5%'],
                        'format' => 'html',
                        /*'value' => function ($model) {
                            return "Start: " . $model['ComplianceStartDate'] . "<br/>End: " . $model['ComplianceEndDate'];
                        }*/
                    ],
					[
                        'label' => 'Remaining/Total',
                        'attribute' => 'Counts',
                        'headerOptions' => ['class' => 'text-center', 'style' => 'width: 5%'],
                        'contentOptions' => ['class' => 'text-center', 'style' => 'width: 5%'],
                        'format' => 'html',
                        'value' => function ($model) {
                            return $model['Remaining'] . "/" . $model['Total'];
                        }
                    ],
                    [
                        'class' => 'kartik\grid\ActionColumn',
                        'template' => '{view}',
                        'header' => 'View<br/>Assets',
                        'headerOptions' => ['class' => 'text-center', 'style' => 'width: 5%'],
                        'contentOptions' => ['class' => 'text-center ViewAssetBtn_AssignedMapGrid', 'style' => 'width: 5%'],
                        'buttons' => [
                            'view' => function($url, $model) {
                                $modalViewAssetAssigned = "#modalViewAssetAssigned";
                                $modalContentViewAssetAssigned = "#modalContentViewAssetAssigned";
                                return Html::a('', null, ['class' =>'glyphicon glyphicon-eye-open', 'onclick' => "viewAssetRowClicked('/dispatch/assigned/view-asset?mapGridSelected=" . $model['MapGrid'] . "','".$modalViewAssetAssigned ."','".$modalContentViewAssetAssigned."')"]);
                            }
                        ],
                        'urlCreator' => function ($action, $model, $key, $index) {
                        }
                    ],
                    [
                        'header' => 'Remove Surveyor',
                        'class' => 'kartik\grid\CheckboxColumn',
                        'headerOptions' => ['class' => 'text-center', 'style' => 'width: 5%'],
                        'contentOptions' => ['class' => 'text-center unassignCheckbox', 'style' => 'width: 5%'],
                        'checkboxOptions' => function ($model, $key, $index, $column) {
                            if (/*$model['WorkQueueStatus'] != 100&&*/ $model['AssignedCount'] == "MANY") {
                                return ['disabled' => true];
                            } else {
                                return ['AssignedToID' => $model['UIDList'],'MapGrid' => $model['MapGrid'], 'disabled' => false, 'UserName' => $model['SearchString'] ];
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
            <?php Pjax::end() ?>
        </div>
    </div>

    <!-- The Modal -->
    <div id="unassigned-message" class="modal">
        <!-- Modal content -->·
        <div class="modal-content">
            <div class="modal-header">
                <h3>Do you want to<br>un-assign the selected surveyors?</h3>
            </div>
            <div class="modal-body">
                <p>Press confirm to continue to un-assign <br> <span class="unassignedUserName"></span> . </p>
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

    <!--View Asset Modal-->
    <?php
    Modal::begin([
        'header' => '<h4>Assets</h4>',
        'id' => 'modalViewAssetAssigned',
        'size' => 'modal-m',
    ]);

    ?>
    <div id='modalContentViewAssetAssigned'>
        Loading...
    </div>
    <?php

    Modal::end();
    ?>
</div>


