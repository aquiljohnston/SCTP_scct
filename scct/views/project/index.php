<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use app\controllers\ProjectController;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ProjectSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Projects';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-index">

    <h3 class="title"><?= Html::encode($this->title)?></h3>
		<p>
			<?php if($canCreateProjects): ?>
				<?= Html::a('Create Project', ['create'], ['class' => 'btn btn-success']) ?>
			<?php else: ?>
				<?= Html::a('Create Project', null, ['class' => 'btn btn-success', 'disabled' => 'disabled']) ?>
			<?php endif; ?>
		</p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
		'export' => false,
		'bootstrap' => false,
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],

            //'ProjectID',
            [
				'label' => 'Project Name',
				'attribute' => 'ProjectName',
				'filter' => '<input class="form-control" name="filtername" value="' . Html::encode($searchModel['ProjectName']) . '" type="text">'
			],
			[
				'label' => 'Project Type',
				'attribute' => 'ProjectType',
				'filter' => '<input class="form-control" name="filtertype" value="' . Html::encode($searchModel['ProjectType']) . '" type="text">'
			],
			[
				'label' => 'Project State',
				'attribute' => 'ProjectState',
				'filter' => '<input class="form-control" name="filterstate" value="' . Html::encode($searchModel['ProjectState']) . '" type="text">'
			],
            [
                'label' => 'Start Date',
                'attribute' => 'ProjectStartDate',
                'value' => function($model) {
                    return date("m/d/Y", strtotime($model['ProjectStartDate']));
                }
            ],
            [
                'label' => 'End Date',
                'attribute' => 'ProjectEndDate',
                'value' => function($model) {
                    return date("m/d/Y", strtotime($model['ProjectEndDate']));
                }
            ],

            ['class' => 'kartik\grid\ActionColumn',
                'template'=>'{view} {update}',
				'urlCreator' => function ($action, $model, $key, $index) {
        			if ($action === 'view') {
        			$url ='/project/view?id='.$model["ProjectID"];
        			return $url;
        			}
        			if ($action === 'update') {
        			$url ='/project/update?id='.$model["ProjectID"];
        			return $url;
        			}
        			if ($action === 'deactivate') {
        			$url ='/project/deactivate?id='.$model["ProjectID"];
        			return $url;
        			}
        		},
                'buttons' => [
                    'deactivate' => function ($url, $model, $key) {
                        $url ='/project/deactivate?id='.$model["ProjectID"];
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
        ],
    ]); ?>

</div>
