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
        //'headerRowOptions' => ['style' => 'display: none'],
        'columns' => [
           
            [
                'label' => 'User Full Name',
                'attribute' => 'UserFullName',
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
            ],
            [
                'label' => 'Start Date - End Date',
                'attribute' => 'TimeCardDates',
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
            ],
            [
                'label' => 'Sum Hours',
                'attribute' => 'SumHours',
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
            ],
            [
                'label' => 'Approved',
                'attribute' => 'TimeCardApprovedFlag',
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
				'value' => function($model, $key, $index, $column) {
					return $model['TimeCardApprovedFlag'] == 0 ? 'No' : 'Yes';
				},
            ],
            [
                'header' => '',
                'class' => 'kartik\grid\ActionColumn',
				'template' => '{update}',
				'buttons' => [
					'update' => function ($url, $model, $key) {						
						$url = '/time-card/show-entries?id=' . $model['TimeCardID']
						.'&projectName='.$model['ProjectName']
						.'&fName='.$model['UserFirstName']
						.'&lName='.$model['UserLastName']
						.'&timeCardProjectID='.$model['TimeCardProjectID'];
						
						$options =[
							'title' => Yii::t('yii', 'Update'),
							'aria-label' => Yii::t('yii', 'Update'),
							'data-confirm' => Yii::t('yii', 'Do you want to edit the time entries for ' . $model['UserFullName'] . '?'),
						];
						return html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, $options);
					}
				],
            ]
        ],
    ]); ?>
	
</div>
