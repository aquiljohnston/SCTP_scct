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
                'headerOptions' => ['class' => 'text-center', 'style' => 'width: 1.47%'],
                'contentOptions' => ['class' => 'text-center', 'style' => 'width: 1.47%'],
                'value' => function($model){
                    return "";
                }
            ],
            [
                'label' => 'Section Number',
                'attribute' => 'SectionNumber',
                'headerOptions' => ['class' => 'text-center', 'style' => 'width: 15.4%'],
                'contentOptions' => ['class' => 'text-center', 'style' => 'width: 15.4%'],
            ],
            [
                'label' => 'Assigned User(s)',
                'attribute' => 'SearchString',
                //'attribute' => 'AssignedUser',
                'headerOptions' => ['class' => 'text-center', 'style' => 'opacity: 0; width:15.9%'],
                'contentOptions' => ['class' => 'text-center', 'style' => 'width: 15.9%'],

                'format' => 'html',
            ],
            [
                'label' => 'Location Type',
                'attribute' => 'LocationType',
                'headerOptions' => ['class' => 'text-center', 'style' => 'width: 38.5%'],
                'contentOptions' => ['class' => 'text-center', 'style' => 'width: 38.5%'],
            ],
			[
				'label' => 'Remaining/Total',
				'attribute' => 'Counts',
				'headerOptions' => ['class' => 'text-center', 'style' => 'opacity: 0;width: 6.5%'],
				'contentOptions' => ['class' => 'text-center', 'style' => 'width: 6.5%'],
				'format' => 'html',
				'value' => function ($model) {
					return $model['Remaining'] . "/" . $model['Total'];
				}
			],
            [
                'label' => 'Inspection Type',
                'attribute' => 'InspectionType',
                'headerOptions' => ['class' => 'text-center', 'style' => 'width: 11.5%; opacity: 0;'],
                'contentOptions' => ['class' => 'text-center', 'style' => 'width: 11.5%'],
            ],
            [
                'label' => 'Billing Code',
                'attribute' => 'BillingCode',
                'headerOptions' => ['class' => 'text-center', 'style' => 'width: 5.5%; opacity: 0;'],
                'contentOptions' => ['class' => 'text-center', 'style' => 'width: 5.5%'],
            ],
            [
                'label' => '',
                'attribute' => 'OfficeName',
                'headerOptions' => ['class' => 'text-center', 'style' => 'width: 5.5%'],
                'contentOptions' => ['class' => 'text-center', 'style' => 'width: 5.5%'],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'header' => 'View<br/>Assets',
                'headerOptions' => ['class' => 'text-center', 'style' => 'opacity: 0; width:6%;'],
                'contentOptions' => ['class' => 'text-center ViewAssetBtn_AssignedSection','style'=>'padding-right:19px;'],
                'buttons' => [
                    'view' => function($url, $model) {
                        $modalViewAssetAssigned = "#modalViewAssetAssigned";
                        $modalContentViewAssetAssigned = "#modalContentViewAssetAssigned";
                        return Html::a('', null, ['class' =>'glyphicon glyphicon-eye-open', 'onclick' => "viewAssetRowClicked('/dispatch/assigned/view-asset?billingCode=".$model['BillingCode']."&inspectionType=".$model['InspectionType']."&mapGridSelected=" . $model['MapGrid'] ."&sectionNumberSelected=" . $model['SectionNumber'] . "','".$modalViewAssetAssigned ."','".$modalContentViewAssetAssigned."','".$model['MapGrid']."')"]);
                    }
                ],
            ],
            [
                'header' => 'Remove User',
                'class' => 'kartik\grid\CheckboxColumn',
                'headerOptions' => ['class' => 'text-center', 'style' => 'opacity: 0;'],
                'contentOptions' => ['class' => 'text-center assignedSectionCheckbox'],
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
