<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Project Landing';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="projectlanding-index">

    <h3><?= Html::encode($this->title) ?></h3>

    <!-- Table for Projects -->
    <?= GridView::widget([
        'id' => 'equipmentWidget',
        'dataProvider' => $projectLandingProvider,
        'layout' => "{items}\n{pager}",
        'caption' => 'All Projects',

        'columns' => [
            'ProjectName',
            //'Number of Items',

            ['class' => 'yii\grid\ActionColumn',
                'header' => 'View',
                'template' => '{view}',
                'urlCreator' => function ($action, $model, $key, $index) {
                    if ($action === 'view') {
                        //$url ='index.php?r=project-landing%2Fview&id='.$model["ProjectID"];
						$url ='index.php?r=project%2Fview&id='.$model["ProjectID"];
                        return $url;
                    }
                }
            ],

        ],
    ]); ?>

</div>
