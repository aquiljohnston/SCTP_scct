<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Home';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="home-index">
    
    <h3 id="homeHeader">Hello, <?=$firstName; $lastName;?></h3>

    <!-- Table for Unaccepted Equipment -->
    <?= GridView::widget([
        'id' => 'equipmentWidget',
        'dataProvider' => $equipmentProvider,
        'layout' => "{items}\n{pager}",
        'bootstrap' => false,
        'export' => false,
        'caption' => 'Unaccepted Equipment',

        'columns' => [
            'Project',
            'Number of Items',

            ['class' => 'kartik\grid\ActionColumn',
                'header' => 'View',
                'template' => '{view}',
                'urlCreator' => function ($action, $model, $key, $index) {
                    if ($action === 'view' && $model["Project"] === 'Total') {
                        $url = '/equipment/index?filterprojectname='
                            . $this->context->getAllProjects() .
                            "&filteraccepted=Pending|No";
                        return $url;
                    } else {
                        $url = '/equipment/index?filterprojectname='
                            . $this->context->trimString($model["Project"]) .
                            "&filteraccepted=Pending|No";
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
        'bootstrap' => false,
        'export' => false,
        'caption' => 'Unapproved Time Cards',

        'columns' => [
            'Project',
            'Number of Items',

            ['class' => 'kartik\grid\ActionColumn',
                'header' => 'View',
                'template' => '{view}',
                'urlCreator' => function ($action, $model, $key, $index) {
                    if ($action === 'view' && $model["Project"] === 'Total') {
                        $url = '/time-card/index?filterprojectname='
                            . $this->context->getAllProjects() .
                            "&filterapproved=No&week=prior";
                        return $url;
                    } else {
                        $url = '/time-card/index?filterprojectname='
                            . $this->context->trimString($model["Project"]) .
                            "&filterapproved=No&week=prior";
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
        'bootstrap' => false,
        'export' => false,
        'caption' => 'Unapproved Mileage Cards',

        'columns' => [
            'Project',
            'Number of Items',

            ['class' => 'kartik\grid\ActionColumn',
                'header' => 'View',
                'template' => '{view}',
                'urlCreator' => function ($action, $model, $key, $index) {
                    if ($action === 'view' && $model["Project"] === 'Total') {
                        $url = '/mileage-card/index?filterprojectname='
                            . $this->context->getAllProjects() .
                            "&filterapproved=No&week=prior";
                        return $url;
                    } else {
                        $url = '/mileage-card/index?filterprojectname='
                            . $this->context->trimString($model["Project"]) .
                            "&filterapproved=No&week=prior";
                        return $url;
                    }
                }
            ],

        ],
    ]); ?>

</div>
