<?php
/**
 * Created by PhpStorm.
 * User: tzhang
 * Date: 6/27/2017
 * Time: 3:22 PM
 */
use kartik\form\ActiveForm;
use yii\helpers\Html;
use kartik\widgets\DepDrop;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\widgets\LinkPager;
use kartik\grid\GridView;
use yii\bootstrap\Modal;

$this->title = 'Notification';
$this->params['breadcrumbs'][] = $this->title;
$pageSize = ["50" => "50", "100" => "100", "200" => "200"];
?>
<div class="notification">
    <div id="notificationTab">
        <h3 class="title" style="padding-left: 1%; padding-top: 1%"><?= Html::encode($this->title) ?></h3>
        <div id="notification-dropDownList-form">
            <?php $form = ActiveForm::begin([
                'type' => ActiveForm::TYPE_VERTICAL,
                'options' => ['id' => 'notificationActiveForm']
            ]); ?>
            <div id="notificationTableDropdown">
                    <span id="notificationPageSizeLabel" style="float: right;">
                        <?= $form->field($model, 'pagesize')->dropDownList($pageSize,
                            ['value' => $notificationPageSizeParams, 'id' => 'notificationPageSize'])
                            ->label('Records Per Page', [
                                'class' => 'recordsPerPage'
                            ]); ?>
                    </span>
                <div id="notificationSearchContainer" class="col-xs-3 col-md-3 col-lg-3" style="float:left; margin-left: 60%;">
                    <div id="filtertitle" class="dropdowntitle" style="width: 100%;">
                        <?= $form->field($model, 'notificationfilter')->textInput(['value' => $notificationFilterParams, 'id' => 'notificationFilter', 'placeholder' => 'Search'])->label(''); ?>
                    </div>
                </div>
                <input id="notificationPageNumber" type="hidden" name="notificationPageNumber" value="1"/>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>

    <div id="notificationGridViewContainer">
        <div id="notificationUnassignedTable">
            <?php Pjax::begin(['id' => 'notificationGridview', 'timeout' => false]) ?>
            <?= GridView::widget([
                'id' => 'notificationGV',
                'dataProvider' => $notificationDataProvider,
                'export' => false,
                'pjax' => true,
                //'floatHeader' => true,
                'summary' => '',
                'columns' => [
                    [
                        'label' => 'Notification Type',
                        'attribute' => 'NotificationType',
                        'headerOptions' => ['class' => 'text-center'],
                        'contentOptions' => ['class' => 'text-center'],
                    ],
                    [
                        'label' => 'Created Date',
                        'attribute' => 'SrvDTLT',
                        'headerOptions' => ['class' => 'text-center'],
                        'contentOptions' => ['class' => 'text-center'],
                    ]
                ],
                'beforeHeader' => [
                    [
                        'columns' => [
                            ['content' => 'Notification', 'options' => ['colspan' => 12, 'class' => 'kv-table-caption text-center']],
                        ],
                    ]
                ],
            ]); ?>
            <div id="notificationTablePagination">
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
</div>


