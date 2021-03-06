<?php
/**
 * Created by PhpStorm.
 * User: tzhang
 * Date: 9/28/2017
 * Time: 3:40 PM
 */
use yii\helpers\Html;
use kartik\grid\GridView;
use app\controllers\Cge;
use kartik\form\ActiveForm;
use yii\widgets\LinkPager;
use yii\widgets\Pjax;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use app\modules\dispatch\assets\CGEAsset;

//register assets
CGEAsset::register($this);

$this->title = 'CGE';
$this->params['breadcrumbs'][] = $this->title;
$pageSize = ["50" => "50", "100" => "100", "200" => "200"];
?>
<div class="dispatch-cge">
    <div id="cgeDropdownContainer">
        <h3 class="title"><?= Html::encode($this->title) ?></h3>
        <div id="cge-dropDownList-form">
            <?php $form = ActiveForm::begin([
                'type' => ActiveForm::TYPE_VERTICAL,
                'options' => ['id' => 'cgeActiveForm', 'data-pjax' => true],
				'action' => Url::to(['cge/index'])
            ]); ?>
            <span id="cgePageSizeLabel">
                        <?= $form->field($model, 'pagesize')->dropDownList($pageSize,
                            ['value' => $cgePageSizeParams, 'id' => 'cgePageSize'])
                            ->label('Records Per Page', [
                                'class' => 'recordsPerPage'
                            ]); ?>
                </span>
			<div id="cgeSearchContainer" class="col-xs-3 col-md-3 col-lg-3">
                <div id="cgeSearchField">
                    <?= $form->field($model, 'cgefilter')->textInput(['value' => $cgeFilterParams, 'id' => 'cgeFilter', 'placeholder' => 'Search'])->label(''); ?>
                </div>
                <?php echo Html::img('@web/logo/filter_clear_black.png', ['id' => 'cgeSearchCleanFilterButton']) ?>
            </div>
			<div class="col-xs-1 col-md-1 col-lg-1" id="cgeButtonContainer">
				<label style="color: #0067a6; margin-bottom: 7px;"></label>
				<?php Pjax::begin(['id' => 'cgeButtons', 'timeout' => false]) ?>
				<?php echo Html::button('ADD SURVEYOR', ['class' => 'btn btn-primary cge_dispatch_btn', 'id' => 'cgeDispatchButton', 'disabled' => 'disabled']); ?>
				<?php Pjax::end() ?>
			</div>
            <input id="cgeTableRecordsUpdate" type="hidden" name="cgeTableRecordsUpdate" value="no" />
            <input id="cgePageNumber" type="hidden" name="cgePageNumber" value="1" />
            <?php ActiveForm::end(); ?>
        </div>
    </div>

    <div id="cgeGridViewContainer">
        <div id="cgeTable">
            <?php Pjax::begin(['id' => 'cgeGridview', 'timeout' => false]) ?>
            <?= GridView::widget([
                'dataProvider' => $cgeDataProvider,
                'id' => 'cgeGV',
                'summary' => '',
                'pjax' => true,
                'pjaxSettings' => [
                    'options' => [
                        'enablePushState' => false,
                    ]
                ],
                'export' => false,
                'columns' => [
                    [
                        'class' => 'kartik\grid\ExpandRowColumn',
                        'headerOptions' => ['class' => 'text-center', 'style' => 'width: 3.5%'],
                        'contentOptions' => ['class' => 'text-center', 'style' => 'width: 3.5%'],
                        'expandAllTitle' => 'Expand all',
                        'collapseTitle' => 'Collapse all',
                        'expandIcon' => '<span class="glyphicon glyphicon-expand"></span>',
                        'value' => function ($model, $key, $index, $column) {
                            return GridView::ROW_COLLAPSED;
                        },
                        'detailUrl' => Url::to(['cge/view-section']),
                        'detailAnimationDuration' => 'fast'
                    ],
                    [
                        'label' => 'Map Grid',
                        'attribute' => 'MapGrid',
                        'headerOptions' => ['class' => 'text-center', 'style' => 'width: 12.5%;'],
                        'contentOptions' => ['class' => 'text-center', 'style' => 'width: 12.5%'],
                        'format' => 'html'
                    ],
                    [
                        'label' => 'Compliance Start',
                        'attribute' => 'ComplianceStart',
                        'headerOptions' => ['class' => 'text-center', 'style' => 'width: 12.5%;'],
                        'contentOptions' => ['class' => 'text-center', 'style' => 'width: 12.5%'],
                        'format' => 'html'
                    ],
                    [
                        'label' => 'Compliance End',
                        'attribute' => 'ComplianceEnd',
                        'headerOptions' => ['class' => 'text-center', 'style' => 'width: 12.5%;'],
                        'contentOptions' => ['class' => 'text-center', 'style' => 'width: 12.5%'],
                        'format' => 'html'
                    ],
                    [
                        'label' => 'Available Work Order',
                        'attribute' => 'AvailableWorkOrderCount',
                        'headerOptions' => ['class' => 'text-center', 'style' => 'width: 12.5%;'],
                        'contentOptions' => ['class' => 'text-center', 'style' => 'width: 12.5%'],
                    ],
                    [
                        'label' => 'Inspection Type',
                        'attribute' => 'InspectionType',
                        'headerOptions' => ['class' => 'text-center', 'style' => 'width: 12.5%;'],
                        'contentOptions' => ['class' => 'text-center', 'style' => 'width: 12.5%'],
                    ],
                    [
                        'label' => 'Billing Code',
                        'attribute' => 'BillingCode',
                        'headerOptions' => ['class' => 'text-center', 'style' => 'width: 12.5%;'],
                        'contentOptions' => ['class' => 'text-center', 'style' => 'width: 12.5%'],
                    ],
                    [
                        'label' => 'Office Name',
                        'attribute' => 'OfficeName',
                        'headerOptions' => ['class' => 'text-center', 'style' => 'width: 12.5%;'],
                        'contentOptions' => ['class' => 'text-center', 'style' => 'width: 12.5%'],
                    ],
                    [
                        'header' => 'Add Surveyor',
                        'class' => 'kartik\grid\CheckboxColumn',
                        'headerOptions' => ['class' => 'text-center', 'style' => 'width: 9%;'],
                        'contentOptions' => ['class' => 'text-center cgeDispatchCheckbox', 'style' => 'width: 15%'],
                        'checkboxOptions' => function ($model, $key, $index, $column) {
                            return[
								'MapGrid' => $model['MapGrid'],
								'BillingCode' => $model['BillingCode'],
								'InspectionType' => $model['InspectionType'],
								'OfficeName' => $model['OfficeName'],
								'disabled' => ($model['ScheduleRequired'] == 1 ? 'disabled' : false)
							];
                        }
                    ]
                ]
            ]); ?>
            <div id="cgeTablePagination">
                <?php
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

    <?php
    Modal::begin([
        'header' => '<h4>CGE History</h4>',
        'id' => 'modalViewHistoryDetailCGE',
        'size' => 'modal-lg',
    ]);

    ?>
    <div id='modalContentViewHistoryDetailCGE'>
        Loading...
    </div>
    <?php

    Modal::end();
    ?>
    <!--CGE Add Surveyor Modal-->
    <?php

    Modal::begin([
        'header' => '<h4>ADD SURVEYORS</h4>',
        'id' => 'addSurveyorModal',
    ]);
    echo "<div id='modalAddSurveyor'>Loading...</div>";
    Modal::end();
    ?>
</div>
