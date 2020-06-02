<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use app\controllers\ProjectController;
use kartik\form\ActiveForm;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\widgets\Pjax;
use app\assets\ProjectAsset;

//register assets
ProjectAsset::register($this);

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
        'label' => 'Project ID',
        'attribute' => 'ProjectReferenceID',
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
    ['class' => 'kartik\grid\ActionColumn',
		'template' => '{view} {update}',
        'urlCreator' => function ($action, $model, $key, $index) {
            if ($action === 'view') {
                $url = '/project/view?id=' . $model["ProjectID"];
                return $url;
            }
			if ($action === 'update') {
                $url = '/project/update?id=' . $model["ProjectID"] . 
					'&refid=' . $model["ProjectReferenceID"] . 
					'&projectName=' . $model["ProjectName"];
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
    <div class = 'col-sm-1' style='padding-left: 0px'>
        <?php if ($canCreateProjects): ?>
            <?= Html::a('Create Project', ['create'], ['class' => 'btn btn-success']) ?>
        <?php endif; ?>
    </div>
    <div class = 'col-sm-11' id="projectSearchContainer">
        <?php $form = ActiveForm::begin([
            'type' => ActiveForm::TYPE_HORIZONTAL,
            'formConfig' => ['labelSpan' => 3, 'deviceSize' => ActiveForm::SIZE_SMALL],
            'method' => 'get',
            'action' => Url::to(['project/index']),
            'options' => [
                'id' => 'projectIndexForm',
            ]
        ]); ?>
        <label id="projectFilter" class = 'col-sm-4'>
            <?= $form->field($model, 'filter')->textInput(['id' => 'projectIndexSearchField'])->label("Search"); ?>
        </label>
		<?php echo Html::img('@web/logo/filter_clear_black.png', ['id' => 'projectIndexClearFilterButton', 'class' => 'projectIndexClearFilterButton']) ?>
		<?= $form->field($model, 'page')->hiddenInput(['id' => 'projectIndexPageNumber', 'value' => $model->page])->label(false); ?>
		<?= $form->field($model, 'pagesize')->hiddenInput(['value' => $model->pagesize])->label(false); ?>
        <?php ActiveForm::end(); ?>
    </div>

    <?php Pjax::begin(['id' => 'projectIndexPjaxContainer', 'timeout' => false]) ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'export' => false,
		'pjax' => false,
		'summary' => '',
		'id' => 'projectIndexGridView',
        'columns' => $column
    ]); ?>
    <div id="projectIndexPagination" class="projectIndexPagination">
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
