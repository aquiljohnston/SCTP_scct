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
                'headerOptions' => ['class' => 'text-center', 'style' => 'width: 3.1%'],
                'contentOptions' => ['class' => 'text-center', 'style' => 'width: 3.1%'],
                'value' => function($model){
                    return "";
                }
            ],
            [
                'label' => 'Map Grid',
                'attribute' => 'MapGrid',
                'headerOptions' => ['class' => 'text-center', 'style' => 'visibility: hidden; width: 16.3%'],
                'contentOptions' => ['class' => 'text-center', 'style' => 'width: 16.3%'],
                /*'value' => function ($model) {
                    return "Office<br/>" . $model['Division'] . "<br/>" . $model['MapGrid'];
                }*/
            ],
            [
                'label' => 'Assigned User(s)',
                'attribute' => 'SearchString',
                'headerOptions' => ['class' => 'text-center', 'style' => 'visibility: hidden; width: 16.5%'],
                'contentOptions' => ['class' => 'text-center', 'style' => 'width: 16.5%'],
                'format' => 'html',
                'value' => function ($model) {
                    if ($model['AssignedCount'] == "MANY")
                        return "MANY";
                    else
                        return $model['SearchString'];
                }
            ],
            [
                'label' => 'Section Number',
                'attribute' => 'SectionNumber',
                'headerOptions' => ['class' => 'text-center', 'style' => 'width: 48.5%'],
                'contentOptions' => ['class' => 'text-center', 'style' => 'width: 48.5%'],
                /*'value' => function ($model) {
                    return "Start: " . $model['ComplianceStartDate'] . "<br/>End: " . $model['ComplianceEndDate'];
                }*/
            ],
			[
				'label' => 'Remaining/Total',
				'attribute' => 'Counts',
				'headerOptions' => ['class' => 'text-center', 'style' => 'visibility: hidden;width: 5%'],
				'contentOptions' => ['class' => 'text-center', 'style' => 'width: 5%'],
				'format' => 'html',
				'value' => function ($model) {
					return $model['Remaining'] . "/" . $model['Total'];
				}
			],
            /*[
                'label' => 'Work Queue Count',
                'attribute' => 'AssignedWorkQueueCount',
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
            ],*/
            /*[
                'class' => 'kartik\grid\ActionColumn',
                'template' => '{view}',
                'header' => 'View<br/>Assets',
                'urlCreator' => function ($action, $model, $key, $index) {
                    if ($action === 'view') {
                        $url = '/dispatch/view-asset?id=' . $model['MapGrid']; //TODO: change to correct identifier.
                        return $url;
                    }
                    return "";
                }
            ],*/
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'header' => 'View<br/>Assets',
                'headerOptions' => ['class' => 'text-center', 'style' => 'visibility: hidden; width: 6%'],
                'contentOptions' => ['class' => 'text-center ViewAssetBtn_AssignedSection',],
                'buttons' => [
                    'view' => function($url, $model) {
                        $modalViewAssetAssigned = "#modalViewAssetAssigned";
                        $modalContentViewAssetAssigned = "#modalContentViewAssetAssigned";
                        return Html::a('', null, ['class' =>'glyphicon glyphicon-eye-open', 'onclick' => "viewAssetRowClicked('/dispatch/assigned/view-asset?mapGridSelected=" . $model['MapGrid'] ."&sectionNumberSelected=" . $model['SectionNumber'] . "','".$modalViewAssetAssigned ."','".$modalContentViewAssetAssigned."')"]);
                    }
                ],
            ],
            [
                'header' => 'Remove User',
                'class' => 'kartik\grid\CheckboxColumn',
                'headerOptions' => ['class' => 'text-center', 'style' => 'visibility: hidden'],
                'contentOptions' => ['class' => 'text-center assignedSectionCheckbox'],
                'checkboxOptions' => function ($model, $key, $index, $column) {
                    if (!empty($model))
                        return ['AssignedToID' => $model['UIDList'],'SectionNumber' => $key, 'MapGrid' => $model['MapGrid'], 'UserName' => $model['SearchString']];
                    else
                        return "";
                }
            ]
        ],
    ]); ?>

    <?php
    Modal::begin([
        'id' => 'assetModal',
        'size' => 'modal-m',
    ]);

    ?>
    <div id='viewAssetModalContent'>
        Loading...
    </div>
    <?php

    Modal::end();
    ?>

</div>
