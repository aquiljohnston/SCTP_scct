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
                'label' => 'User Full Name',
                'attribute' => 'UserFullName',
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
            ],
            [
                'label' => 'Start Date - End Date',
                'attribute' => 'MileageCardDates',
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
            ],
            [
                'label' => 'Sum Miles',
                'attribute' => 'SumMiles',
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
            ],
            [
                'label' => 'Approved',
                'attribute' => 'MileageCardApprovedFlag',
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
				'value' => function($model, $key, $index, $column) {
					return $model['MileageCardApprovedFlag'] == 0 ? 'No' : 'Yes';
				},
            ],
            [
                'header' => '',
                'class' => 'kartik\grid\ActionColumn',
				'template' => '{update}',
				'buttons' => [
					'update' => function ($url, $model, $key) {						
						$url = '/mileage-card/show-entries?id=' . $model['MileageCardID']
						.'&projectName='.$model['ProjectName']
						.'&fName='.$model['UserFirstName']
						.'&lName='.$model['UserLastName']
						.'&mileageCardProjectID='.$model['MileageCardProjectID'];
						
						$options =[
							'title' => Yii::t('yii', 'Update'),
							'aria-label' => Yii::t('yii', 'Update'),
							'data-confirm' => Yii::t('yii', 'Do you want to edit the mileage entries for ' . $model['UserFullName'] . '?'),
						];
						return html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, $options);
					}
				],
            ]
        ],
    ]); ?>
	
</div>
