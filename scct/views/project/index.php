<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use app\controllers\ProjectController;
use kartik\form\ActiveForm;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ProjectSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Projects';
$this->params['breadcrumbs'][] = $this->title;
$column = [
    ['class' => 'kartik\grid\SerialColumn'],

    //'ProjectID',
    [
        'label' => 'Project Name',
        'attribute' => 'ProjectName',
        'headerOptions' => ['class' => 'text-center'],
        'contentOptions' => ['class' => 'text-center'],
    ],
    [
        'label' => 'Project Type',
        'attribute' => 'ProjectType',
        'headerOptions' => ['class' => 'text-center'],
        'contentOptions' => ['class' => 'text-center'],
    ],
    [
        'label' => 'Project State',
        'attribute' => 'ProjectState',
        'headerOptions' => ['class' => 'text-center'],
        'contentOptions' => ['class' => 'text-center'],
    ],
    [
        'label' => 'Start Date',
        'attribute' => 'ProjectStartDate',
        'headerOptions' => ['class' => 'text-center'],
        'contentOptions' => ['class' => 'text-center'],
        'value' => function ($model) {
            return date("m/d/Y", strtotime($model['ProjectStartDate']));
        }
    ],
    [
        'label' => 'End Date',
        'attribute' => 'ProjectEndDate',
        'headerOptions' => ['class' => 'text-center'],
        'contentOptions' => ['class' => 'text-center'],
        'value' => function ($model) {
            return date("m/d/Y", strtotime($model['ProjectEndDate']));
        }
    ],

    ['class' => 'kartik\grid\ActionColumn',
        'template' => '{view}',
        'urlCreator' => function ($action, $model, $key, $index) {
            if ($action === 'view') {
                $url = '/project/view?id=' . $model["ProjectID"];
                return $url;
            }
        },
        'buttons' => [
            'deactivate' => function ($url, $model, $key) {
                $url = '/project/deactivate?id=' . $model["ProjectID"];
                $options = [
                    'title' => Yii::t('yii', 'Deactivate'),
                    'aria-label' => Yii::t('yii', 'Deactivate'),
                    'data-confirm' => Yii::t('yii', 'Are you sure you want to deactivate this item?'),
                    'data-method' => 'Post',
                    'data-pjax' => '0',
                ];
                return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, $options);
            },
        ]
    ],
];
?>
<div class="project-index">

    <h3 class="title"><?= Html::encode($this->title) ?></h3>
    <p style="float: left;">
        <?php if ($canCreateProjects): ?>
            <?= Html::a('Create Project', ['create'], ['class' => 'btn btn-success']) ?>
        <?php else: ?>
            <?= Html::a('Create Project', null, ['class' => 'btn btn-success', 'disabled' => 'disabled']) ?>
        <?php endif; ?>
    </p>
    <div id="projectSearchContainer">
        <?php $form = ActiveForm::begin([
            'type' => ActiveForm::TYPE_HORIZONTAL,
            'formConfig' => ['labelSpan' => 5, 'deviceSize' => ActiveForm::SIZE_SMALL],
            'method' => 'get',
            'action' => Url::to(['project/index']),
            'options' => [
                'id' => 'projectForm',
            ]
        ]); ?>
        <label id="projectFilter" style="width: 40%">
            <?= $form->field($model, 'filter')->textInput(['id' => 'projectSearchField'])->label("Search"); ?>
            <?php echo Html::img('@web/logo/filter_clear_black.png', ['id' => 'projectSearchCleanFilterButton', 'class' => 'projectIndexClearFilterButton']) ?>
        </label>
        <?php ActiveForm::end(); ?>
    </div>

    <?php Pjax::begin(['id' => 'projectGridView', 'timeout' => false]) ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'export' => false,
        'bootstrap' => false,
        'columns' => $column
    ]); ?>
    <div id="UserPagination">
        <?php
        echo LinkPager::widget([
            'pagination' => $pages,
        ]);
        ?>
    </div>
    <div class="GridviewTotalNumber">
        <?php echo "Showing " . ($pages->offset + 1) . " to " . ($pages->offset + $pages->getPageSize()) . " of " . $pages->totalCount . " entries"; ?>
    </div>
    <?php Pjax::end() ?>
</div>
