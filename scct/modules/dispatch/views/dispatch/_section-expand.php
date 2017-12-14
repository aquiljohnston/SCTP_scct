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
                'label' => 'Section Number',
                'attribute' => 'SectionNumber',
                'headerOptions' => ['class' => 'text-center', 'style' => 'width: 10.7%;'],
                'contentOptions' => ['class' => 'text-center', 'style' => 'width: 10.7%;'],
            ],
            [
                'label' => 'Location Type',
                'attribute' => 'LocationType',
                'headerOptions' => ['class' => 'text-center', 'style' => 'width: 35.7%;'],
                'contentOptions' => ['class' => 'text-center', 'style' => 'width: 35.7%;'],
            ],
            [
                'label' => 'Available Work Order Count',
                'attribute' => 'AvailableWorkOrderCount',
                'headerOptions' => ['class' => 'text-center', 'style' => 'width: 21.9%; opacity: 0;'],
                'contentOptions' => ['class' => 'text-center', 'style' => 'width: 21.9%;'],
            ],
            [   //PROJECT-498
                'label' => 'Inspection Type',
                'attribute' => 'InspectionType',
                'headerOptions' => ['class' => 'text-center', 'style' => 'width: 21.9%; opacity: 0;'],
                'contentOptions' => ['class' => 'text-center', 'style' => 'width: 21.9%;'],
            ],
            [   //PROJECT-501
                'label' => 'Billing Code',
                'attribute' => 'BillingCode',
                'headerOptions' => ['class' => 'text-center', 'style' => 'width: 21.9%; opacity: 0;'],
                'contentOptions' => ['class' => 'text-center', 'style' => 'width: 21.9%;'],
            ],
            [
                'header' => 'View Asset',
                'class' => 'kartik\grid\ActionColumn',
                'template' => '{view}',
                'headerOptions' => ['class' => 'text-center', 'style' => 'opacity: 0;'],
                'buttons' => [
                    'view' => function($url, $model) {
                        $modalViewAssetDispatch = "#modalViewAssetDispatch";
                        $modalContentViewAssetDispatch = "#modalContentViewAssetDispatch";
                        return Html::a('', null, ['class' =>'glyphicon glyphicon-eye-open', 'onclick' => "viewAssetRowClicked('/dispatch/dispatch/view-asset?mapGridSelected=" . $model['MapGrid']."&sectionNumberSelected=".$model['SectionNumber']."','".$modalViewAssetDispatch ."','".$modalContentViewAssetDispatch."','".$model['MapGrid']."')"]);
                    }
                ],
                'urlCreator' => function ($action, $model, $key, $index) {
                }
            ],
            [
                'header' => 'Add Surveyor',
                'class' => 'kartik\grid\CheckboxColumn',
                'headerOptions' => ['class' => 'text-center', 'style' => 'opacity: 0;'],
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
