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
		//hiding for now until notification screen is fully implemented and we can redirect
		'hidden' => true,
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
            if ($action === 'view' && $model['Project'] === 'Total') {
                $url = '/time-card/index?projectFilterString='
				. $this->context->getAllProjects();
                return $url;
            } else {
                $url = '/time-card/index?projectID=' . $model['ProjectID'];
                return $url;
            }
        }
    ],
]; ?>

<div class="home-index">
    <!-- Table for Unaccepted Equipment -->
    <?= GridView::widget([
        'id' => 'homeEquipmentWidget',
        'dataProvider' => $notificationProvider,
        'layout' => "{items}\n{pager}",
        'bootstrap' => false,
        'export' => false,
        'caption' => 'Notifications',

        'columns' => $notificationCol
    ]); ?>

    <!-- Table for Unapproved Time Cards -->
    <?= GridView::widget([
        'id' => 'homeTimeCardWidget',
        'dataProvider' => $timeCardProvider,
        'layout' => "{items}\n{pager}",
        'bootstrap' => false,
        'export' => false,
        'caption' => 'Unapproved Time Cards',

        'columns' => $timeCardCol
    ]); ?>
</div>
