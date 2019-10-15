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
                'label' => 'Date',
                'attribute' => 'CreatedDate',
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
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
            ]
        ],
    ]); ?>
	
</div>
