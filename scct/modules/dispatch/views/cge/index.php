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

$this->title = 'CGE';
$this->params['breadcrumbs'][] = $this->title;
$pageSize = ["50" => "50", "100" => "100", "200" => "200"];
?>
<div class="dispatch-cge" style="margin-top: 2%;">
    <div id="cgeDropdownContainer" style="height: 105px;float: left;width: 100%;background-color: #E6F0F6;z-index: 3;border-bottom: 1px solid black;padding: 10px;">
        <h3 class="title"><?= Html::encode($this->title) ?></h3>
        <div id="cge-dropDownList-form">
            <?php $form = ActiveForm::begin([
                'type' => ActiveForm::TYPE_VERTICAL,
                'options' => ['id' => 'cgeActiveForm', 'data-pjax' => true],
            ]); ?>
            <span id="cgePageSizeLabel" style="float: right;margin: -39px auto;width: 10%;">
                        <?= $form->field($model, 'pagesize')->dropDownList($pageSize,
                            ['value' => $cgePageSizeParams, 'id' => 'cgePageSize'])
                            ->label('Records Per Page', [
                                'class' => 'recordsPerPage'
                            ]); ?>
                </span>
            <div class="col-xs-1 col-md-1 col-lg-1" id="cgeButtonContainer" style="float: right;margin: 0.3% auto;width: 11%;">
                <label style="color: #0067a6; margin-bottom: 7px;"></label>
                <?php Pjax::begin(['id' => 'cgeButtons', 'timeout' => false]) ?>
                <?php echo Html::button('ADD SURVEYOR', ['class' => 'btn btn-primary dispatch_btn', 'id' => 'dispatchButton', 'disabled' => 'disabled']); ?>
                <?php Pjax::end() ?>
            </div>
            <div id="cgeSearchContainer">
                <div id="filtertitle" class="dropdowntitle" style="float: left;width: 30%;color: #0067a6; margin-left: 50%">
                    <?= $form->field($model, 'cgefilter')->textInput(['value' => $cgeFilterParams, 'id' => 'cgeFilter', 'placeholder' => 'Search'])->label(''); ?>
                </div>
            </div>
            <input id="cgeTableRecordsUpdate" type="hidden" name="cgeTableRecordsUpdate" value="no" />
            <input id="cgePageNumber" type="hidden" name="cgePageNumber" value="1" />
            <?php ActiveForm::end(); ?>
        </div>
    </div>

    <div id="cgeGridViewContainer" style="position: relative;float: left;width: 100%;margin-top: 1%;overflow-x: hidden;">
        <div id="cgeTable">
            <?php Pjax::begin(['id' => 'cgeGridview', 'timeout' => false]) ?>
            <?= GridView::widget([
                'dataProvider' => $cgeDataProvider,
                'id' => 'cgeGV',
                'summary' => false,
                'pjax' => true,
                'floatHeader' => true,
                'floatOverflowContainer' => true,
                'responsive'=>true,
                'responsiveWrap' => true,
                'pjaxSettings' => [
                    'options' => [
                        'enablePushState' => false,
                    ]
                ],
                'export' => false,
                'columns' => [
                    /*[
                        'class' => 'kartik\grid\ExpandRowColumn',
                        'headerOptions' => ['class' => 'text-center', 'style' => 'width:5%'],
                        'contentOptions' => ['class' => 'text-center', 'style' => 'width:5%'],
                        'expandAllTitle' => 'Expand all',
                        'collapseTitle' => 'Collapse all',
                        'expandIcon' => '<span class="glyphicon glyphicon-expand"></span>',
                        'value' => function ($model, $key, $index, $column) {
                            return GridView::ROW_COLLAPSED;
                        },

                        'detailUrl' => Url::to(['assigned/view-section']),
                        'detailAnimationDuration' => 'fast'
                    ],*/
                    [
                        'label' => 'MapGrid',
                        'attribute' => 'MapGrid',
                        'headerOptions' => ['class' => 'text-center', 'style' => 'width: 20%;'],
                        'contentOptions' => ['class' => 'text-center', 'style' => 'width: 20%'],
                        'format' => 'html'
                    ],
                    [
                        'label' => 'Available WorkOrder Count',
                        'attribute' => 'AvailableWorkOrderCount',
                        'headerOptions' => ['class' => 'text-center', 'style' => 'width: 20%;'],
                        'contentOptions' => ['class' => 'text-center', 'style' => 'width: 20%'],
                    ],
                    [
                        'label' => 'Compliance Start',
                        'attribute' => 'ComplianceStart',
                        'headerOptions' => ['class' => 'text-center', 'style' => 'width: 20%;'],
                        'contentOptions' => ['class' => 'text-center', 'style' => 'width: 20%'],
                        'format' => 'html'
                    ],
                    [
                        'label' => 'Compliance End',
                        'attribute' => 'ComplianceEnd',
                        'headerOptions' => ['class' => 'text-center', 'style' => 'width: 20%;'],
                        'contentOptions' => ['class' => 'text-center', 'style' => 'width: 20%'],
                        'format' => 'html'
                    ],
                    [
                        'header' => 'Add Surveyor',
                        'class' => 'kartik\grid\CheckboxColumn',
                        'headerOptions' => ['class' => 'text-center', 'style' => 'width: 15%;'],
                        'contentOptions' => ['class' => 'text-center dispatchCheckbox', 'style' => 'width: 15%'],
                        'checkboxOptions' => function ($model, $key, $index, $column) {
                            if ( $model['ScheduleRequired'] != 1 )
                                return [/*'SectionNumber' => '000', 'MapGrid' => $model['MapGrid'],*/ 'disabled' => false];
                            else
                                return [/*'SectionNumber' => $model['SectionNumber'], 'MapGrid' => $model['MapGrid'],*/ 'disabled' => true];
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
</div>
