<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use app\assets\HomeAsset;

use app\constants\Constants;

//register assets
HomeAsset::register($this);
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
    [
		'label' => 'Prior Week',
		'attribute' => 'PriorWeekCount',
		'headerOptions' => ['class' => 'text-center'],
		'contentOptions' => ['class' => 'text-center'],
	],
    ['class' => 'kartik\grid\ActionColumn',
        'header' => '',
        'template' => '{view}',
        'urlCreator' => function ($action, $model, $key, $index) {
            if ($action === 'view' && $model['Project'] === 'Total') {
				$url = '/time-card/index?' . http_build_query([
					'projectFilterString' => $this->context->getAllProjects(),
					'activeWeek' => Constants::PRIOR_WEEK,
				]);
                return $url;
            } else {
				$url = '/time-card/index?' . http_build_query([
					'projectID' => $model['ProjectID'],
					'activeWeek' => Constants::PRIOR_WEEK,
				]);
                return $url;
            }
        }
    ],
	[
		'label' => 'Current Week',
		'attribute' => 'CurrentWeekCount',
		'headerOptions' => ['class' => 'text-center'],
		'contentOptions' => ['class' => 'text-center'],
	],
	[
		'class' => 'kartik\grid\ActionColumn',
		'header' => '',
        'template' => '{view}',
        'urlCreator' => function ($action, $model, $key, $index) {
            if ($action === 'view' && $model['Project'] === 'Total') {
				$url = '/time-card/index?' . http_build_query([
					'projectFilterString' => $this->context->getAllProjects(),
					'activeWeek' => Constants::CURRENT_WEEK,
				]);
                return $url;
            } else {
				$url = '/time-card/index?' . http_build_query([
					'projectID' => $model['ProjectID'],
					'activeWeek' => Constants::CURRENT_WEEK,
				]);
                return $url;
            }
        }
    ],
];

$mileageCardCol = [
    'Project',
    [
		'label' => 'Prior Week',
		'attribute' => 'PriorWeekCount',
		'headerOptions' => ['class' => 'text-center'],
		'contentOptions' => ['class' => 'text-center'],
	],
	[
		'class' => 'kartik\grid\ActionColumn',
		'header' => '',
        'template' => '{view}',
        'urlCreator' => function ($action, $model, $key, $index) {
            if ($action === 'view' && $model['Project'] === 'Total') {
				yii::trace('Project String: ' . $this->context->getAllProjects());
				$url = '/mileage-card/index?' . http_build_query([
					'projectFilterString' => $this->context->getAllProjects(),
					'activeWeek' => Constants::PRIOR_WEEK,
				]);
                return $url;
            } else {
				$url = '/mileage-card/index?' . http_build_query([
					'projectID' => $model['ProjectID'],
					'activeWeek' => Constants::PRIOR_WEEK,
				]);
                return $url;
            }
        }
    ],
	[
		'label' => 'Current Week',
		'attribute' => 'CurrentWeekCount',
		'headerOptions' => ['class' => 'text-center'],
		'contentOptions' => ['class' => 'text-center'],
	],
    [
		'class' => 'kartik\grid\ActionColumn',
		'header' => '',
        'template' => '{view}',
        'urlCreator' => function ($action, $model, $key, $index) {
            if ($action === 'view' && $model['Project'] === 'Total') {
				$url = '/mileage-card/index?' . http_build_query([
					'projectFilterString' => $this->context->getAllProjects(),
					'activeWeek' => Constants::CURRENT_WEEK,
				]);
                return $url;
            } else {
				$url = '/mileage-card/index?' . http_build_query([
					'projectID' => $model['ProjectID'],
					'activeWeek' => Constants::CURRENT_WEEK,
				]);
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
	
	<!-- Table for Unapproved Mileage Cards -->
    <?= GridView::widget([
        'id' => 'homeMileageCardWidget',
        'dataProvider' => $mileageCardProvider,
        'layout' => "{items}\n{pager}",
        'bootstrap' => false,
        'export' => false,
        'caption' => 'Unapproved Mileage Cards',
        'columns' => $mileageCardCol
    ]); ?>
</div>
