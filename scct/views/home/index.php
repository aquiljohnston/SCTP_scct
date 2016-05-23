<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Home';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="home-index">

    <h3><?= Html::encode($this->title) ?></h3>
    <h3>Hello, <?=$firstName; $lastName;?></h3>

    <!-- Table for Unaccepted Equipment -->
    <?= GridView::widget([
        'id' => 'equipmentWidget',
        'dataProvider' => $equipmentProvider,
        'layout' => "{items}\n{pager}",
        'caption' => 'Unaccepted Equipment',

        'columns' => [
            'Project',
            'Number of Items',

            ['class' => 'yii\grid\ActionColumn',
                'header' => 'View',
                'template' => '{view}',
                'urlCreator' => function ($action, $model, $key, $index) {
                    if ($action === 'view') {
                        $url = 'index.php?filterprojectname='
                            . $this->context->trimString($model["Project"]) .
                            "&filterapproved=No&r=equipment%2Findex";
                        return $url;
                    }
                }
            ],

        ],
    ]); ?>

    <!-- Table for Unapproved Time Cards -->
    <?= GridView::widget([
        'id'=> 'timeCardWidget',
        'dataProvider' => $timeCardProvider,
        'layout' => "{items}\n{pager}",
        'caption' => 'Unapproved Time ',

        'columns' => [
            'Project',
            'Number of Items',

            ['class' => 'yii\grid\ActionColumn',
                'header' => 'View',
                'template' => '{view}',
                'urlCreator' => function ($action, $model, $key, $index) {
                    if ($action === 'view') {
						$url = 'index.php?filterprojectname='
                            . $this->context->trimString($model["Project"]) .
                            "&filterapproved=No&r=time-card%2Findex";
						return $url;
                    }
                }
            ],

        ],
    ]); ?>

    <!-- Table for Unapproved Mileage Cards -->
    <?= GridView::widget([
        'id'=> 'mileageCardWidget',
        'dataProvider' => $mileageCardProvider,
        'layout' => "{items}\n{pager}",
        'caption' => 'Unapproved Mileage Cards',

        'columns' => [
            'Project',
            'Number of Items',

            ['class' => 'yii\grid\ActionColumn',
                'header' => 'View',
                'template' => '{view}',
                'urlCreator' => function ($action, $model, $key, $index) {
                    if ($action === 'view') {
                        $url = 'index.php?filterprojectname='
                            . $this->context->trimString($model["Project"]) .
                            "&filterapproved=No&r=mileage-card%2Findex";
                        return $url;
                    }
                }
            ],

        ],
    ]); ?>

</div>
