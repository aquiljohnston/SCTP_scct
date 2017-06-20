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
                'label' => 'Assigned User(s)',
                'attribute' => 'SearchString',
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
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
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
                //'label' => false,
                /*'value' => function ($model) {
                    return "Start: " . $model['ComplianceStartDate'] . "<br/>End: " . $model['ComplianceEndDate'];
                }*/
            ],
            [
                'label' => 'Work Queue Count',
                'attribute' => 'AssignedWorkQueueCount',
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
                'header' => 'Remove User',
                'class' => 'kartik\grid\CheckboxColumn',
                'contentOptions' => ['class' => 'assignedSectionCheckbox'],
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
