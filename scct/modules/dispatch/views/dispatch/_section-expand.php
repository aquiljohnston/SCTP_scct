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

<div class="allegato-index">

    <?= GridView::widget([
        'dataProvider' => $sectionDataProvider,
        'export' => false,
        'summary' => '',
        //'headerRowOptions' => ['style' => 'display: none'],
        'columns' => [
            [
                'label' => 'Map Grid',
                'attribute' => 'MapGrid',
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
                //'label' => false,
                /*'value' => function ($model) {
                    return "Office<br/>" . $model['Division'] . "<br/>" . $model['MapGrid'];
                }*/
            ],
            [
                'label' => 'Section Number',
                'attribute' => 'SectionNumber',
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
                //'label' => false,
                /*'value' => function ($model) {
                    return "Start: " . $model['ComplianceStartDate'] . "<br/>End: " . $model['ComplianceEndDate'];
                }*/
            ],
            [
                'label' => 'Available WorkOrder',
                'attribute' => 'AvailableWorkOrderCount',
                //'label' => false,
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
            ],
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
                /*'buttons' => [
                    'Images' => function($url, $model) {
                        return Html::a('', null, ['class' =>'glyphicon glyphicon-camera', 'onclick' => "ViewAssetClicked('/dispatch/view-asset?id=" . $model['MapGrid']."')"]);
                    }
                ],
                'urlCreator' => function ($action, $model, $key, $index) {
                    return '';
                }*/
            ],
            [
                'header' => 'Add Surveyor',
                'class' => 'kartik\grid\CheckboxColumn',
                'contentOptions' => ['class' => 'dispatchCheckbox'],
                'checkboxOptions' => function ($model, $key, $index, $column) {
                    /*if ($model['SectionNumber'] == null)
                        return ['SectionNumber' => '000', 'MapGrid' => $model['MapGrid'], 'disabled' => false];
                    else
                        return ['SectionNumber' => $model['SectionNumber'], 'MapGrid' => $model['MapGrid'], 'disabled' => false];*/
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
