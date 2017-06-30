<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Home';
$this->params['breadcrumbs'][] = $this->title;
$notificationCol = [
    'Project',
    'Number of Items',
    ['class' => 'kartik\grid\ActionColumn',
        'header' => 'View',
        'template' => '{view}',
        'urlCreator' => function ($action, $model, $key, $index) {
            if ($action === 'view' && $model['Number of Items'] > 0) {
                $url = '/notification/index';
                return $url;
            } else {
                $url =  '';
                return $url;
            }
        }
    ],
];

$timeCardCol = [
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
];

$mileageCardCol = [
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
];
?>
<div class="home-index">
    <!-- Table for Unaccepted Equipment -->
    <?= GridView::widget([
        'id' => 'equipmentWidget',
        'dataProvider' => $notificationProvider,
        'layout' => "{items}\n{pager}",
        'bootstrap' => false,
        'export' => false,
        'caption' => 'Notifications',

        'columns' => $notificationCol
    ]); ?>

    <!-- Table for Unapproved Time Cards -->
    <?= GridView::widget([
        'id' => 'timeCardWidget',
        'dataProvider' => $timeCardProvider,
        'layout' => "{items}\n{pager}",
        'bootstrap' => false,
        'export' => false,
        'caption' => 'Unapproved Time Cards',

        'columns' => $timeCardCol
    ]); ?>

    <!-- Table for Unapproved Mileage Cards -->
    <?= GridView::widget([
        'id' => 'mileageCardWidget',
        'dataProvider' => $mileageCardProvider,
        'layout' => "{items}\n{pager}",
        'bootstrap' => false,
        'export' => false,
        'caption' => 'Unapproved Mileage Cards',

        'columns' => $mileageCardCol
    ]); ?>

</div>
