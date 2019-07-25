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
    [
		'header' => 'Project',
		'headerOptions' => ['class' => 'kv-align-middle'],
		'contentOptions' => ['class' => 'kv-align-middle'],
		'attribute' => 'ProjectName',
	],
	[
		'header' => 'Start Date -<br> End Date',
		'headerOptions' => ['class' => 'kv-align-center kv-align-middle'],
		'contentOptions' => ['class' => 'kv-align-center kv-align-middle'],
		'value' => function($model, $key, $index, $column) {
			if(array_key_exists('StartDate', $model) && array_key_exists('EndDate', $model)){
				$value = explode(' ', $model['StartDate'])[0] . ' - ' . explode(' ', $model['EndDate'])[0];
			}else{
				$value = '';
			}
			return $value;
		},
	],
	[
		'header' => 'Type',
		'attribute' => 'NotificationType',
		'headerOptions' => ['class' => 'kv-align-center kv-align-middle'],
		'contentOptions' => ['class' => 'kv-align-center kv-align-middle'],
	],
    [
		'header' => 'Count',
		'attribute' => 'Count',
		'headerOptions' => ['class' => 'kv-align-center kv-align-middle'],
		'contentOptions' => ['class' => 'kv-align-center kv-align-middle'],
	],
    [
		'class' => 'kartik\grid\ActionColumn',
        'header' => 'View',
        'template' => '{view}',
		//hide action column button for total row
        'visibleButtons' => [
			'view' => function ($model, $key, $index) {
				if ($model['ProjectName'] === 'Total') {
					return false;
				} else {
					return true;
				}
			}
        ],
        'urlCreator' => function ($action, $model, $key, $index) {
            if ($model['NotificationType'] === 'Time Card') {
                $url = '/time-card/index?';
            } elseif($model['NotificationType'] === 'Mileage Card') {
				$url = '/mileage-card/index?';
			} else {
                return '';
            }
			$url .= http_build_query([
				'projectID' => $model['ProjectID'],
				'dateRange' => explode(' ', $model['StartDate'])[0] . ' - ' . explode(' ', $model['EndDate'])[0],
			]);
			return $url;
        }
    ],
];

$timeCardCol = [
    [
		'header' => 'Project',
		'headerOptions' => ['class' => 'kv-align-middle'],
		'contentOptions' => ['class' => 'kv-align-middle'],
		'attribute' => 'ProjectName',
	],
    [
		'header' => 'Prior Week',
		'attribute' => 'PriorWeekCount',
		'headerOptions' => ['class' => 'kv-align-center kv-align-middle'],
		'contentOptions' => ['class' => 'kv-align-center kv-align-middle'],
	],
    [
		'class' => 'kartik\grid\ActionColumn',
        'header' => '',
        'template' => '{view}',
        'urlCreator' => function ($action, $model, $key, $index) {
            if ($action === 'view' && $model['ProjectName'] === 'Total') {
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
		'header' => 'Current Week',
		'attribute' => 'CurrentWeekCount',
		'headerOptions' => ['class' => 'kv-align-center kv-align-middle'],
		'contentOptions' => ['class' => 'kv-align-center kv-align-middle'],
	],
	[
		'class' => 'kartik\grid\ActionColumn',
		'header' => '',
        'template' => '{view}',
        'urlCreator' => function ($action, $model, $key, $index) {
            if ($action === 'view' && $model['ProjectName'] === 'Total') {
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
    [
		'header' => 'Project',
		'headerOptions' => ['class' => 'kv-align-middle'],
		'contentOptions' => ['class' => 'kv-align-middle'],
		'attribute' => 'ProjectName',
	],
    [
		'header' => 'Prior Week',
		'attribute' => 'PriorWeekCount',
		'headerOptions' => ['class' => 'kv-align-center kv-align-middle'],
		'contentOptions' => ['class' => 'kv-align-center kv-align-middle'],
	],
	[
		'class' => 'kartik\grid\ActionColumn',
		'header' => '',
        'template' => '{view}',
        'urlCreator' => function ($action, $model, $key, $index) {
            if ($action === 'view' && $model['ProjectName'] === 'Total') {
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
		'header' => 'Current Week',
		'attribute' => 'CurrentWeekCount',
		'headerOptions' => ['class' => 'kv-align-center kv-align-middle'],
		'contentOptions' => ['class' => 'kv-align-center kv-align-middle'],
	],
    [
		'class' => 'kartik\grid\ActionColumn',
		'header' => '',
        'template' => '{view}',
        'urlCreator' => function ($action, $model, $key, $index) {
            if ($action === 'view' && $model['ProjectName'] === 'Total') {
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
    <!-- Table for Notifications -->
    <?= GridView::widget([
        'id' => 'homeNotificationWidget',
        'dataProvider' => $notificationProvider,
		'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ''],
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
