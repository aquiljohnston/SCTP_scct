<?php
/**
 * Created by PhpStorm.
 * User: tzhang
 * Date: 6/6/2017
 * Time: 11:22 AM
 */

use yii\helpers\Html;
use kartik\grid\GridView;
?>

<div class="allegato-index">

    <?= GridView::widget([
        'dataProvider' => $dispatchDataProvider,
        'export' => false,
        'summary' => '',
        //'headerRowOptions' => ['style' => 'display: none'],
        'columns' => [
            [
                'label' => 'ClientWorkOrderID',
                'attribute' => 'ClientWorkOrderID',
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
                //'label' => false,
                /*'value' => function ($model) {
                    return "Office<br/>" . $model['Division'] . "<br/>" . $model['MapGrid'];
                }*/
            ],
            [
                'label' => 'CreatedBy',
                'attribute' => 'CreatedBy',
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
                //'label' => false,
                /*'value' => function ($model) {
                    return "Start: " . $model['ComplianceStartDate'] . "<br/>End: " . $model['ComplianceEndDate'];
                }*/
            ],
            [
                'label' => 'CreatedDateTime',
                'attribute' => 'CreatedDateTime',
                //'label' => false,
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
            ],
            [
                'class' => 'kartik\grid\ActionColumn',
                'template' => '{view}',
                'header' => 'View<br/>Assets',
                /*'urlCreator' => function ($action, $model, $key, $index) {
                    if ($action === 'view') {
                        $url = '/dispatch/assets?id=' . $model['MapGrid']; //TODO: change to correct identifier.
                        return $url;
                    }
                    return "";
                }*/
            ],
            [
                'header' => 'Add Surveyor',
                'class' => 'kartik\grid\CheckboxColumn',
                'contentOptions' => ['class' => 'dispatchCheckbox'],
                'checkboxOptions' => function ($model, $key, $index, $column) {
                    if ($model['SectionNumber'] == null)
                        return ['SectionNumber' => '000', 'MapGrid' => $model['MapGrid'], 'disabled' => false];
                    else
                        return ['SectionNumber' => $model['SectionNumber'], 'MapGrid' => $model['MapGrid'], 'disabled' => false];
                }
            ]
        ],
    ]); ?>

</div>
