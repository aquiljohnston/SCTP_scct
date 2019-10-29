<?php

use yii\helpers\Html;
use kartik\grid\GridView;

?>

<div class="allegato-index">

    <?= GridView::widget([
        'dataProvider' => $accountantDetialsDataProvider,
        'export' => false,
        'id' => 'accountantDetailGV',
        'summary' => '',
        'columns' => [
           
            [
                'label' => 'User',
                'attribute' => 'UserName',
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
            ],
            [
				'label' => 'Start Date - End Date',
				'headerOptions' => ['class' => 'text-center'],
				'contentOptions' => ['class' => 'text-center'],
				'value' => function($model, $key, $index, $column) {
					return $model['StartDate'] . ' - ' . $model['EndDate'];
				},
			],
            [
                'label' => 'Quantity',
                'attribute' => 'Quantity',
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
            ],
            [
                'label' => 'Approved',
                'attribute' => 'IsApproved',
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
				'value' => function($model, $key, $index, $column) {
					return $model['IsApproved'] == 0 ? 'No' : 'Yes';
				},
            ],
			[
				'class' => 'kartik\grid\ActionColumn',
				'template' => '{view}', // does not include delete
				'urlCreator' => function ($action, $model, $key, $index) {
					if ($action === 'view') {
						$url = '/expense/show-entries?userID=' . $model['UserID']
						.'&projectID='.$model['ProjectID']
						.'&startDate='.$model['StartDate']
						.'&endDate='.$model['EndDate'];
						return $url;
					}
				},
			],
        ],
    ]); ?>
	
</div>
