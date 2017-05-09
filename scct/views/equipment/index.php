<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use app\controllers\Equipment;
use app\controllers\BaseController;
use yii\widgets\LinkPager;
use yii\widgets\Pjax;
use kartik\form\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel app\models\EquipmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Equipment Management';
$this->params['breadcrumbs'][] = $this->title;
$pageSize = ["10" => "10", "25" => "25", "50" => "50", "100" => "100"];
$column = [
    ['class' => 'kartik\grid\SerialColumn'],

    [
        'label' => 'Name',
        'attribute' => 'EquipmentName',
        'filter' => '<input class="form-control" name="filtername" value="' . Html::encode($searchModel['EquipmentName']) . '" type="text">'
    ],
    [
        'label' => 'Type',
        'attribute' => 'EquipmentType',
        'filter' => '<input class="form-control" name="filtertype" value="' . Html::encode($searchModel['EquipmentType']) . '" type="text">'
    ],
    [
        'label' => 'Serial Number',
        'attribute' => 'EquipmentSerialNumber',
        'filter' => '<input class="form-control" name="filterserialnumber" value="' . Html::encode($searchModel['EquipmentSerialNumber']) . '" type="text">'
    ],
    [
        'label' => 'SC Number',
        'attribute' => 'EquipmentSCNumber',
        'filter' => '<input class="form-control" name="filterscnumber" value="' . Html::encode($searchModel['EquipmentSCNumber']) . '" type="text">'
    ],
    [
        'label' => 'Client Name',
        'attribute' => 'ClientName',
        'filter' => '<input class="form-control" name="filterclientname" value="' . Html::encode($searchModel['ClientName']) . '" type="text">'
    ],
    [
        'label' => 'Project Name',
        'attribute' => 'ProjectName',
        'filter' => '<input class="form-control" name="filterprojectname" value="' . Html::encode($searchModel['ProjectName']) . '" type="text">'
    ],
    [
        'label' => 'Accepted Flag',
        'attribute' => 'EquipmentAcceptedFlag',
        'filter' => $acceptedFilterInput
    ],

    ['class' => 'kartik\grid\ActionColumn',
        'template' => '{view} {update}',
        'urlCreator' => function ($action, $model, $key, $index) {
            if ($action === 'view') {
                $url = '/equipment/view?id=' . $model["EquipmentID"];
                return $url;
            }
            if ($action === 'update') {
                $url = '/equipment/update?id=' . $model["EquipmentID"];
                return $url;
            }
            if ($action === 'delete') {
                $url = '/equipment/delete?id=' . $model["EquipmentID"];
                return $url;
            }
        },
        'buttons' => [
            'delete' => function ($url, $model, $key) {
                $url = '/equipment/delete?id=' . $model["EquipmentID"];
                $options = [
                    'title' => Yii::t('yii', 'Delete'),
                    'aria-label' => Yii::t('yii', 'Delete'),
                    'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                    'data-method' => 'Delete',
                    'data-pjax' => '0',
                ];
                return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, $options);
            },
        ],
    ],
    [
        'class' => 'kartik\grid\CheckboxColumn',
        'checkboxOptions' => function ($model, $key, $index, $column) {
            return ['equipmentid' => $model["EquipmentID"], 'accepted' => $model["EquipmentAcceptedFlag"]];
        },
        'checkboxOptions' => function ($model, $key, $index, $column) {
            // Disable if already approved or SumHours is 0
            $disabledBoolean = strtoupper($model["EquipmentAcceptedFlag"]) == "YES";
            $result = [
                'equipmentid' => $model["EquipmentID"],
                'accepted' => $model["EquipmentAcceptedFlag"]
            ];
            if ($disabledBoolean) {
                $result['disabled'] = 'true';
            }

            return $result;
        }
        /*'pageSummary' => true,
        'rowSelectedClass' => GridView::TYPE_SUCCESS,
        'contentOptions'=>['style'=>'width: 0.5%'],*/
    ],
];
?>
<div class="equipment-index">

    <h3 class="title"><?= Html::encode($this->title) ?></h3>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <div class="equipment_filter">
        <div id="equipmentButtons">
            <?php if (BaseController::can('equipmentCreate')): ?>
                <?= Html::a('Create Equipment', ['create'], ['class' => 'btn btn-success']) ?>
            <?php endif; ?>

            <?= Html::button('Accept Equipment', [
                'class' => 'btn btn-primary multiple_approve_btn',
                'id' => 'multiple_approve_btn_id_equipment',
            ]) ?>
        </div>
        <div id="equipmentDropdownContainer">

            <?php $form = ActiveForm::begin([
                'type' => ActiveForm::TYPE_HORIZONTAL,
                'formConfig' => ['labelSpan' => 7, 'deviceSize' => ActiveForm::SIZE_SMALL],
                'method' => 'post',
                'options' => [
                    'id' => 'equipmentForm',
                ]

            ]); ?>
            <label id="equipmentPageSizeLabel">
                <?= $form->field($model, 'pagesize')->dropDownList($pageSize, ['value' => $equipmentPageSizeParams, 'id' => 'equipmentPageSize'])->label("Records Per Page"); ?>
            </label>
            <input id="pageNumber" type="hidden" name="pageNumber" value="1"/>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
    <div id="equipmentGridViewContainer">
        <div id="equipmentGV" class="equipmentForm">
            <?php Pjax::begin(['id' => 'equipmentGridview', 'timeout' => false]) ?>
            <?= GridView::widget([
                'id' => 'GridViewForEquipment',
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'export' => false,
                'bootstrap' => false,
                'pjax' => true,
                'summary' => '',
                'columns' => $column
            ]); ?>
            <div id="equipmentPagination">
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
</div>
