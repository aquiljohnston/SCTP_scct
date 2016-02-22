<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ProjectSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Projects';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-index">

    <h1 class="title"><?= Html::encode($this->title)?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Project', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'ProjectID',
            'ProjectName',
            'ProjectDescription',
            'ProjectNotes',
            'ProjectType',
            // 'ProjectStatus',
            // 'ProjectClientID',
            // 'ProjectStartDate',
            // 'ProjectEndDate',

            ['class' => 'yii\grid\ActionColumn',
				'urlCreator' => function ($action, $model, $key, $index) {
        			if ($action === 'view') {
        			$url ='index.php?r=project%2Fview&id='.$model["ProjectID"];
        			return $url;
        			}
        			if ($action === 'update') {
        			$url ='index.php?r=project%2Fupdate&id='.$model["ProjectID"];
        			return $url;
        			}
        			if ($action === 'delete') {
        			$url ='index.php?r=project%2Fdelete&id='.$model["ProjectID"];											
        			return $url;
        			}
        		},
                'buttons' => [
                    'delete' => function ($url, $model, $key) {
                        $url ='/index.php?r=project%2Fdelete&id='.$model["ProjectID"];       
                            $options = [
                            'title' => Yii::t('yii', 'Delete'),
                            'aria-label' => Yii::t('yii', 'Delete'),
                            'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                            'data-method' => 'Delete',
                            'data-pjax' => '0',
                            ];
                            return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, $options);
                    },
                ]
			],
        ],
    ]); ?>

</div>
