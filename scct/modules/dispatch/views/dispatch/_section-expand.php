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
                //'label' => ' ',
                'headerOptions' => ['class' => 'text-center', 'style' => 'width: 6.3%;'],
                'contentOptions' => ['class' => 'text-center', 'style' => 'width: 6.3%;'],
                'value' => function($model){
                    return "";
                }
            ],
            [
                'label' => 'Section Number',
                'attribute' => 'SectionNumber',
                'headerOptions' => ['class' => 'text-center', 'style' => 'width: 10.7%;'],
                'contentOptions' => ['class' => 'text-center', 'style' => 'width: 10.7%;'],
                //'label' => false,
                /*'value' => function ($model) {
                    return "Office<br/>" . $model['Division'] . "<br/>" . $model['MapGrid'];
                }*/
            ],
            [
                'label' => 'Location Type',
                'attribute' => 'LocationType',
                'headerOptions' => ['class' => 'text-center', 'style' => 'width: 35.7%;'],
                'contentOptions' => ['class' => 'text-center', 'style' => 'width: 35.7%;'],
                //'label' => false,
                /*'value' => function ($model) {
                    return "Start: " . $model['ComplianceStartDate'] . "<br/>End: " . $model['ComplianceEndDate'];
                }*/
            ],
            [
                'label' => '',
                'attribute' => 'AvailableWorkOrderCount',
                //'label' => false,
                'headerOptions' => ['class' => 'text-center', 'style' => 'width: 21.9%;'],
                'contentOptions' => ['class' => 'text-center', 'style' => 'width: 21.9%;'],
            ],
            [
                'header' => 'View Asset',
                'class' => 'kartik\grid\ActionColumn',
                'template' => '{view}',
                'headerOptions' => ['class' => 'text-center', 'style' => 'visibility: hidden;'],
                'buttons' => [
                    'view' => function($url, $model) {
                        $modalViewAssetDispatch = "#modalViewAssetDispatch";
                        $modalContentViewAssetDispatch = "#modalContentViewAssetDispatch";
                        return Html::a('', null, ['class' =>'glyphicon glyphicon-eye-open', 'onclick' => "viewAssetRowClicked('/dispatch/dispatch/view-asset?mapGridSelected=" . $model['MapGrid']."&sectionNumberSelected=".$model['SectionNumber']."','".$modalViewAssetDispatch ."','".$modalContentViewAssetDispatch."')"]);
                    }
                ],
                'urlCreator' => function ($action, $model, $key, $index) {
                }
            ],
            [
                'header' => 'Add Surveyor',
                'class' => 'kartik\grid\CheckboxColumn',
                'headerOptions' => ['class' => 'text-center', 'style' => 'visibility: hidden;'],
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
