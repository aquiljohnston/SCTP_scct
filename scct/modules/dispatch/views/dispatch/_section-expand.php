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
        'id' => 'dispatchSectionGV',
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
            [
                'class' => 'kartik\grid\ActionColumn',
                'template' => '{view}',
                'header' => 'View<br/>Assets',
                'contentOptions' => [
                    'class' => 'ViewAssetBtn_DispatchMapGrid'
                ],
                'buttons' => [
                    'view' => function($url, $model) {
                        return Html::a('', null, ['class' =>'glyphicon glyphicon-eye-open', 'onclick' => "viewAssetRowClicked('/dispatch/dispatch/view-asset?mapGridSelected=" . $model['MapGrid'] . "&sectionNumberSelected=" . $model['SectionNumber']."')"]);
                    }
                ],
                'urlCreator' => function ($action, $model, $key, $index) {
                }
            ],
            [
                'header' => 'Add Surveyor',
                'class' => 'kartik\grid\CheckboxColumn',
                'contentOptions' => ['class' => 'dispatchSectionCheckbox'],
                'checkboxOptions' => function ($model, $key, $index, $column) {
                    if (!empty($model))
                        return ['SectionNumber' => $key, 'MapGrid' => $model['MapGrid']];
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
