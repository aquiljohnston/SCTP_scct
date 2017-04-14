<?php
use kartik\form\ActiveForm;
use yii\helpers\Html;
use kartik\widgets\DepDrop;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\widgets\LinkPager;
use kartik\grid\GridView;

$this->title = 'Dispatch';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="dispatch">
    <div id="dispatchTab">
        <h3 class="title"><?= Html::encode($this->title) ?></h3>
        <div id="dispatch-dropDownList-form">
            <?php $form = ActiveForm::begin([
                'type' => ActiveForm::TYPE_VERTICAL,
                'method' => 'get',
                'options' => ['id' => 'dispatchAcForm']
            ]); ?>
            <div id="dispatchUnassignedTableDropdown">
                <div class="division dropdowntitle">
                    <?php // division Dropdown
                    echo $form->field($model, 'division')->dropDownList($divisionList, ['id' => 'dispatch-division-id'])->label('Division'); ?>
                </div>
                <div class="workcenter dropdowntitle">
                    <?php // workCenter Dropdown
                    echo $form->field($model, 'complianceDate')->widget(DepDrop::classname(), [
                        'options' => ['id' => 'dispatch-complianceDate-id'],
                        'data' => [$complianceDateParams => $complianceDateParams],
                        'pluginOptions' => [
                            'initialize' => true,
                            'depends' => ['dispatch-division-id'],
                            'placeholder' => 'Select..',
                            'url' => Url::to(['dispatch/getcompliancedate'])
                        ]
                    ])->label('Work Center'); ?>
                    <input type="hidden" name="isNewWorkCenterUpdate" id="isNewWorkCenterUpdate" value="false">
                </div>
                <input id="UnassignedTableRecordsUpdate" type="hidden" name="UnassignedTableRecordsUpdate"
                       value="false">
                <?php /*echo Html::img('@web/logo/filter_clear_black.png', ['id' => 'dispatchUnassignedTableClearFilterButton']) */?>
            </div>

            <!--<div id="dispatchSurveyorTableDropdown">
                <div id="surveyorWorkcenter" class="dropdowntitle">
                    <?php /*// surveyorWorkcenter Dropdown
                    echo $form->field($model, 'surveyorWorkcenter')->dropDownList($surveyorWorkCenterList, ['id' => 'dispatch-surveyorWorkcenter-id'])->label('Work Center'); */ ?>
                </div>
                <div id="surveyorsfiltertitle" class="dropdowntitle">
                    <? /*= $form->field($model, 'surveyorsfilter')->textInput(['value' => $surveyorsFileter, 'id' => 'dispatchsurveyorsFilter', 'placeholder' => 'Search'])->label('Surveyor / Inspector'); */ ?>
                </div>
                <input id="SurveyorTableRecordsUpdate" type="hidden" name="SurveyorTableRecordsUpdate" value="false">
                <?php /*echo Html::img('@web/logo/filter_clear_black.png', ['id' => 'dispatchSurveyorTableClearFilterButton']) */ ?>
            </div>-->
            <input id="dispatchPageNumber" type="hidden" name="dispatchPageNumber" value="1"/>
            <input id="dispatchSurveyorPageNumber" type="hidden" name="dispatchSurveyorPageNumber" value="1"/>
            <?php ActiveForm::end(); ?>
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
                'floatHeader'=>true,
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
                        'header' => 'Add Surveyor',
                        'class' => 'kartik\grid\CheckboxColumn'
                    ]
                ],
                'beforeHeader'=>[
                    [
                        'columns'=>[
                            ['content'=>'Dispatch', 'options'=>['colspan'=>12, 'class'=>'kv-table-caption text-center']],
                        ],
                    ]
                ],
            ]); ?>
            <!--<div id="unassignedTablePagination">
                <?php
/*                   // display pagination
                    echo LinkPager::widget([
                        'pagination' => $dispatchTablePages,
                    ]);*/?>
            </div>
            <div class="GridviewTotalNumber">
                <?php /*echo "Showing " . ($pages->offset + 1) . "  to " . ($pages->offset + $pages->getPageSize()) . " of " . $pages->totalCount . " entries"; */?>
            </div>-->
            <?php Pjax::end() ?>
        </div>
        <div id="dispatchSurveyorsContainer">
            <?php Pjax::begin(['id' => 'dispatchSurveyorsGridview', 'timeout' => false]) ?>
            <?= GridView::widget([
                'id' => 'surveyorsGV',
                'dataProvider' => $surveyorsDataProvider, // Sent from DispatchController.php
                'export' => false,
                'pjax' => true,
                'floatHeader'  => true,
                'columns' => [
                    [
                        'class' => 'kartik\grid\CheckboxColumn'
                    ],
                    [
                        'label' => 'Name',
                        'attribute' => 'name',
                        'value' => function ($model) {
                            return $model['Name'];
                        }
                    ],
                    [
                        'label' => 'Division',
                        'format' => 'html',
                        'value' => function ($model) {
                            return $model['Division'];
                        }
                    ]
                ],
                'beforeHeader'=>[
                [
                    'columns'=>[
                        ['content'=>'Surveyors', 'options'=>['colspan'=>12, 'class'=>'kv-table-caption text-center']],
                    ],
                ]
            ],
            ]); ?>
            <!--<div id="surveyorTablePagination">
                <?php
/*                    // display pagination
                    echo LinkPager::widget([
                        'pagination' => $surveyorTablePages,
                    ]);*/?>
            </div>
            <div id="SurveyorGridviewTotalNumber">
                <?php /*echo "Showing " . ($surveyorTablePages->offset + 1) . "  to " . ($surveyorTablePages->offset + $surveyorTablePages->getPageSize()) . " of " . $surveyorTablePages->totalCount . " entries"; */?>
            </div>-->
            <?php Pjax::end() ?>
        </div>
    </div>
    <?php Pjax::begin(['id' => 'dispatchBtnPjax', 'timeout' => false]) ?>
        <?php if ($can != 0) { ?>
            <?php echo Html::button('DISPATCH', ['class' => 'btn btn-primary dispatch_btn', 'id' => 'dispatchButton']); ?>
        <?php } else {
            echo "";
        } ?>
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
    <div id="dialog-dispatch" title="Dispatch" style="display:none">
        <p>Dispatched Successfully.</p>
    </div>
</div>
