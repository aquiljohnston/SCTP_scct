<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Project Landing';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="projectlanding-index">

    <h3 class="title"><?= Html::encode($this->title) ?></h3>
	
    <!-- Table for Projects -->
    <?= GridView::widget([
        'id' => 'equipmentWidget',
        'dataProvider' => $projectLandingProvider,
        'bootstrap' => false,
        'export' => false,
        'layout' => "{items}\n{pager}",
        'caption' => 'My Projects',

        'columns' => [
            'ProjectName',
            //'Number of Items',

            ['class' => 'kartik\grid\ActionColumn',
                'header' => 'View',
                'template' => '{view}',
                'urlCreator' => function ($action, $model, $key, $index) {
                    if ($action === 'view') {
                        $url ='index.php?r=project-landing%2Fview&id='.$model["ProjectID"];
						//$url ='index.php?r=project%2Fview&id='.$model["ProjectID"];
                        return $url;
                    }
                }
            ],

        ],
    ]); ?>

</div>
