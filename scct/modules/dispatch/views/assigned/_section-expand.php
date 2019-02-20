<?php
/**
 * Created by PhpStorm.
 * User: tzhang
 * Date: 6/6/2017
 * Time: 11:22 AM
 */

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\bootstrap\Modal;

?>

<div class="assignExpand">

    <?= GridView::widget([
        'dataProvider' => $sectionDataProvider,
        'export' => false,
        'id' => 'assignedSectionGV',
        'summary' => '',
        //'headerRowOptions' => ['style' => 'display: none'],
        'columns' => [
            [
                'label' => 'Section Number',
                'attribute' => 'SectionNumber',
                'headerOptions' => ['class' => 'text-center', 'style' => 'width: 12%'],
                'contentOptions' => ['class' => 'text-center', 'style' => 'width: 12%'],
            ],
            [
                'label' => '',
                'attribute' => 'SearchString',
                'headerOptions' => ['class' => 'text-center', 'style' => 'width:15%'],
                'contentOptions' => ['class' => 'text-center', 'style' => 'width: 15%'],
            ],
            [
                'label' => 'Location Type',
                'attribute' => 'LocationType',
                'headerOptions' => ['class' => 'text-center', 'style' => 'width: 27.5%'],
                'contentOptions' => ['class' => 'text-center', 'style' => 'width: 27.5%'],
            ],
			[
				'label' => '',
				'attribute' => 'Counts',
				'headerOptions' => ['class' => 'text-center', 'style' => 'width: 7.5%'],
				'contentOptions' => ['class' => 'text-center', 'style' => 'width: 7.5%'],
				'value' => function ($model) {
					return $model['Remaining'] . "/" . $model['Total'];
				}
			],
            [
                'label' => '',
                'attribute' => 'InspectionType',
                'headerOptions' => ['class' => 'text-center', 'style' => 'width: 10%;'],
                'contentOptions' => ['class' => 'text-center', 'style' => 'width: 10%'],
            ],
            [
                'label' => '',
                'attribute' => 'BillingCode',
                'headerOptions' => ['class' => 'text-center', 'style' => 'width: 10%;'],
                'contentOptions' => ['class' => 'text-center', 'style' => 'width: 10%'],
            ],
            [
                'label' => '',
                'attribute' => 'OfficeName',
                'headerOptions' => ['class' => 'text-center', 'style' => 'width: 10%'],
                'contentOptions' => ['class' => 'text-center', 'style' => 'width: 10%'],
            ],
            [
				'header' => '',
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'headerOptions' => ['class' => 'text-center', 'style' => 'width: 4%'],
                'contentOptions' => ['class' => 'text-center ViewAssetBtn_AssignedSection', 'style' => 'width: 4%'],
                'buttons' => [
                    'view' => function($url, $model) {
                        $modalViewAssetAssigned = "#modalViewAssetAssigned";
                        $modalContentViewAssetAssigned = "#modalContentViewAssetAssigned";
                        return Html::a('', null, [
							'class' =>'glyphicon glyphicon-eye-open',
							'onclick' => "viewAssetRowClicked('/dispatch/assigned/view-asset?billingCode=".$model['BillingCode']
							."&inspectionType=".$model['InspectionType']
							."&officeName=".$model['OfficeName']
							."&mapGridSelected=" . $model['MapGrid']
							."&sectionNumberSelected=" . $model['SectionNumber'] .
							"','".$modalViewAssetAssigned 
							."','".$modalContentViewAssetAssigned
							."','".$model['MapGrid']."')"]);
                    }
                ],
            ],
            [
                'header' => '',
                'class' => 'kartik\grid\CheckboxColumn',
                'headerOptions' => ['class' => 'text-center', 'style' => 'width: 4%'],
                'contentOptions' => ['class' => 'text-center assignedSectionCheckbox', 'style' => 'width: 4%'],
                'checkboxOptions' => function ($model, $key, $index, $column) {
                    return [
						'SectionNumber' => $model['SectionNumber'],
						'MapGrid' => $model['MapGrid'],
						'BillingCode' => $model['BillingCode'],
						'InspectionType' => $model['InspectionType'],
						'OfficeName' => $model['OfficeName'],
						'UserName' => $model['SearchString'],
						'disabled' => $model['InProgressFlag'] != "1" ? false : 'disabled'
					];
				}
            ]
        ],
    ]); ?>

    <?php
    Modal::begin([
        'id' => 'assetModal',
        'size' => 'modal-l',
    ]);

    ?>
    <div id='viewAssetModalContent'>
        Loading...
    </div>
    <?php

    Modal::end();
    ?>

</div>
