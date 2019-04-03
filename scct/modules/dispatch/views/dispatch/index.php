<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\widgets\LinkPager;
use yii\bootstrap\Modal;
use kartik\form\ActiveForm;
use kartik\widgets\DepDrop;
use kartik\grid\GridView;
use kartik\daterange\DateRangePicker;

$this->title = 'Dispatch';
$this->params['breadcrumbs'][] = $this->title;
$pageSize = ["50" => "50", "100" => "100", "200" => "200"];
?>
<style>
.modal-xl{
	width: 90%;
	max-width:1200px;
}
</style>
<div class="dispatch">
    <div id="dispatchTab">
        <h3 class="title"><?= Html::encode($this->title) ?></h3>
        <div id="dispatch-dropDownList-form">
            <?php $form = ActiveForm::begin([
                'type' => ActiveForm::TYPE_VERTICAL,
                'options' => ['id' => 'dispatchActiveForm'],
                'action' => '/dispatch/dispatch/index'
            ]); ?>
            <div id="dispatchUnassignedTableDropdown">
				<div id="dispatchDatePickerContainer">
					<?= $form->field($model, 'dateRangePicker')
						->widget(DateRangePicker::classname(), [
							'name'=>'date_range_3',
							'hideInput'=>false,
							//'initRangeExpr' => true,
							'pluginOptions' => [
								'opens' => 'right',
							],
							'options' => [
								'placeholder' => 'Date Range',
								'class' => 'form-control',
							],
						])
						->label(''); ?>
				</div>
				<?php echo Html::img('@web/logo/filter_clear_black.png', ['id' => 'dispatchClearDateRange']) ?>
                <span id="dispatchPageSizeLabel">
                    <?= $form->field($model, 'pageSize')->dropDownList($pageSize,
                        ['value' => $model->pageSize, 'id' => 'dispatchPageSize'])
                        ->label('Records Per Page', [
                            'class' => 'recordsPerPage'
                        ]); ?>
                </span>
                <div id="dispatchSearchContainer" class="col-xs-3 col-md-3 col-lg-3">
                    <div id="dispatchSearchField">
                        <?= $form->field($model, 'dispatchFilter')->textInput(['value' => $model->dispatchFilter, 'id' => 'dispatchFilter', 'placeholder' => 'Search'])->label(''); ?>
                    </div>
					 <?php echo Html::img('@web/logo/filter_clear_black.png', ['id' => 'dispatchSearchCleanFilterButton']) ?>
                </div>
                <input id="dispatchPageNumber" type="hidden" name="dispatchPageNumber" value="1"/>
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
                'summary' => '',
                'columns' => [
                    [
                        'class' => 'kartik\grid\ExpandRowColumn',
                        'headerOptions' => ['class' => 'text-center', 'style' => 'width:2%'],
                        'contentOptions' => ['class' => 'text-center', 'style' => 'width:2%'],
                        'expandAllTitle' => 'Expand all',
                        'collapseTitle' => 'Collapse all',
                        'expandIcon' => '<span class="glyphicon glyphicon-expand"></span>',
                        'value' => function ($model, $key, $index, $column) {
                            return GridView::ROW_COLLAPSED;
                        },
                        'detailUrl' => Url::to(['dispatch/view-section']),
                        'detailAnimationDuration' => 'fast'
                    ],
                    [
                        'label' => 'Map Grid',
                        'attribute' => 'MapGrid',
                        'headerOptions' => ['class' => 'text-center', 'style' => 'width: 10%;'],
                        'contentOptions' => ['class' => 'text-center', 'style' => 'width: 10%'],
                    ],
					[
                        'label' => 'Division',
                        'attribute' => 'Division',
                        'headerOptions' => ['class' => 'text-center', 'style' => 'width: 10%;'],
                        'contentOptions' => ['class' => 'text-center', 'style' => 'width: 10%'],
						'visible' => $divisionFlag,
                    ],
                    [
                        'label' => 'Compliance Start',
                        'attribute' => 'ComplianceStart',
                        'headerOptions' => ['class' => 'text-center', 'style' => 'width: 15%;'],
                        'contentOptions' => ['class' => 'text-center', 'style' => 'width: 15%'],
                    ],
                    [
                        'label' => 'Compliance End',
                        'attribute' => 'ComplianceEnd',
                        'headerOptions' => ['class' => 'text-center', 'style' => 'width: 15%;'],
                        'contentOptions' => ['class' => 'text-center', 'style' => 'width: 15%'],
                    ],
                    [
                        'label' => 'Available Work Order',
                        'attribute' => 'AvailableWorkOrderCount',
                        'headerOptions' => ['class' => 'text-center', 'style' => 'width: 10%;'],
                        'contentOptions' => ['class' => 'text-center', 'style' => 'width: 10%'],
                    ], 
                    [
                        'label' => 'Inspection Type',
                        'attribute' => 'InspectionType',
                        'headerOptions' => ['class' => 'text-center', 'style' => 'width: 10%;'],
                        'contentOptions' => ['class' => 'text-center', 'style' => 'width: 10%'],
                    ],
                    [
                        'label' => 'Billing Code',
                        'attribute' => 'BillingCode',
                        'headerOptions' => ['class' => 'text-center', 'style' => 'width: 10%;'],
                        'contentOptions' => ['class' => 'text-center', 'style' => 'width: 10%'],
                    ],
                    [
                        'label' => 'Office Name',
                        'attribute' => 'OfficeName',
                        'headerOptions' => ['class' => 'text-center', 'style' => 'width: 10%;'],
                        'contentOptions' => ['class' => 'text-center', 'style' => 'width: 10%'],
                    ],
                    [
						'header' => 'View<br/>Assets',
                        'class' => 'kartik\grid\ActionColumn',
                        'template' => '{view}',
						'headerOptions' => ['class' => 'text-center', 'style' => 'width: 4%'],
                        'contentOptions' => ['class' => 'text-center ViewAssetBtn_DispatchMapGrid', 'style' => 'width: 4%'],
                        'buttons' => [
                            'view' => function($url, $model) {
                                $modalViewAssetDispatch = "#modalViewAssetDispatch";
                                $modalContentViewAssetDispatch = "#modalContentViewAssetDispatch";
                                return Html::a('', null, ['class' =>'glyphicon glyphicon-eye-open', 'onclick' =>
									"viewAssetRowClicked('/dispatch/dispatch/view-asset?billingCode=" . urlencode($model['BillingCode'])
									."&inspectionType=" . urlencode($model['InspectionType'])
									."&mapGridSelected=" . urlencode($model['MapGrid'])
									."&officeName=" . urlencode($model['OfficeName'])
									."','".$modalViewAssetDispatch 
									."','".$modalContentViewAssetDispatch
									."','".$model['MapGrid']."')"]);
                            }
                        ],
                        'urlCreator' => function ($action, $model, $key, $index) {
                        }
                    ],
                    [
                        'header' => 'Add<br/>Surveyor',
                        'class' => 'kartik\grid\CheckboxColumn',
						'headerOptions' => ['class' => 'text-center', 'style' => 'width: 4%'],
                        'contentOptions' => ['class' => 'text-center dispatchCheckbox', 'style' => 'width: 4%'],
                        'checkboxOptions' => function ($model, $key, $index, $column) {
                            return [
								'MapGrid' => $model['MapGrid'],
								'BillingCode' => $model['BillingCode'],
								'InspectionType' => $model['InspectionType'],
								'OfficeName' => $model['OfficeName'],
								'disabled' => false
							];
                        }
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

	<!-- unused? -->
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
        'header' => '<h4>ADD SURVEYORS TO SELECTED MAPS</h4>',
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
        'size' => 'modal-xl',
    ]);

    ?>
    <div id='modalContentViewAssetDispatch'>
        Loading...
    </div>
    <?php

    Modal::end();
    ?>
</div>


