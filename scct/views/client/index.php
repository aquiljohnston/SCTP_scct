<?php

use kartik\form\ActiveForm;
use yii\helpers\Html;
use yii\widgets\LinkPager;
use kartik\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ClientSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Clients';
$this->params['breadcrumbs'][] = $this->title;
$column = [
    ['class' => 'kartik\grid\SerialColumn'],
    [
        'label' => 'AccountID',
        'attribute' => 'ClientAccountID',
        'headerOptions' => ['class' => 'text-center'],
        'contentOptions' => ['class' => 'text-center'],
        'filter' => '<input class="form-control" name="filterclientaccountID" value="' . Html::encode($searchModel['ClientName']) . '" type="text">'
    ],
    //'ClientID',
    [
        'label' => 'Name',
        'attribute' => 'ClientName',
        'headerOptions' => ['class' => 'text-center'],
        'contentOptions' => ['class' => 'text-center'],
        'filter' => '<input class="form-control" name="filterclientname" value="' . Html::encode($searchModel['ClientName']) . '" type="text">'
    ],
    [
        'label' => 'Client City',
        'attribute' => 'ClientCity',
        'headerOptions' => ['class' => 'text-center'],
        'contentOptions' => ['class' => 'text-center'],
        'filter' => '<input class="form-control" name="filtercity" value="' . Html::encode($searchModel['ClientCity']) . '" type="text">'
    ],
    [
        'label' => 'Client State',
        'attribute' => 'ClientState',
        'headerOptions' => ['class' => 'text-center'],
        'contentOptions' => ['class' => 'text-center'],
        'filter' => '<input class="form-control" name="filterstate" value="' . Html::encode($searchModel['ClientState']) . '" type="text">'
    ],
    ['class' => 'kartik\grid\ActionColumn',
        'template' => '{view} {update}',
        'urlCreator' => function ($action, $model, $key, $index) {
            if ($action === 'view') {
                $url = '/client/view?id=' . $model["ClientID"];
                return $url;
            }
            if ($action === 'update') {
                $url = '/client/update?id=' . $model["ClientID"];
                return $url;
            }
        },
    ],
];
?>
<div class="client-index">

    <h3 class="title"><?= Html::encode($this->title) ?></h3>

    <div class = 'col-sm-1' style='padding-left: 0px'>
        <?= Html::a('Create Client', ['create'], ['class' => 'btn btn-success']) ?>
    </div>
	
	<div class = 'col-sm-11' id="clientSearchContainer">
		<?php $form = ActiveForm::begin([
			'type' => ActiveForm::TYPE_HORIZONTAL,
			'formConfig' => ['labelSpan' => 3, 'deviceSize' => ActiveForm::SIZE_SMALL],
			'method' => 'get',
			'action' => Url::to(['client/index']),
			'options' => [
				'id' => 'ClientForm',
			]
		]); ?>
		<label id='clientFilter' class='col-sm-4'>
			<?= $form->field($model, 'filter')->textInput(['value' => $model->filter, 'id' => 'clientSearchField' ])->label("Search"); ?>
		</label>
		<?php echo Html::img('@web/logo/filter_clear_black.png', ['id' => 'clientSearchCleanFilterButton']) ?>
		<?= $form->field($model, 'page')->hiddenInput(['id' => 'clientIndexPageNumber', 'value' => $model->page])->label(false); ?>
		<?= $form->field($model, 'pagesize')->hiddenInput(['value' => $model->pagesize])->label(false); ?>
		<?php ActiveForm::end(); ?>
	</div>

    <?php Pjax::begin(['id' => 'clientIndexPjaxContainer', 'timeout' => false]) ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'export' => false,
        'bootstrap' => false,
		'pjax' => false,
		'summary' => '',
        'columns' => $column,
		'id' => 'clientIndexGridView'
    ]); ?>
    <div id="clientIndexPagination" class="clientIndexPagination">
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
