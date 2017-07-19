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
use yii\helpers\Url;
?>

<div class="allegato-index">

    <?= GridView::widget([
        'dataProvider' => $eventDataProvider,
        'export' => false,
        'id' => 'inspectionSectionGV',
        'summary' => '',
        //'headerRowOptions' => ['style' => 'display: none'],
        'columns' => [
            [
                'label' => 'Event Type',
                'attribute' => 'EventType',
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
                //'label' => false,
                /*'value' => function ($model) {
                    return "Office<br/>" . $model['Division'] . "<br/>" . $model['MapGrid'];
                }*/
            ],
            [
                'label' => 'Reason',
                'attribute' => 'Reason',
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
                //'label' => false,
                /*'value' => function ($model) {
                    return "Start: " . $model['ComplianceStartDate'] . "<br/>End: " . $model['ComplianceEndDate'];
                }*/
            ],
            [
                'label' => 'Comments',
                'attribute' => 'Comments',
                //'label' => false,
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
            ],
            [
                'attribute' => 'img',
                'format' => 'raw',
                'label' => 'Image',
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
                'value' => function ($model) {
                    if ($model['Photo'] != null)
                        return Html::a(Html::img(Yii::getAlias('@web/logo/linkIcon.png'), ['width' => '20px']),[Url::to('/../images/'.$model['Photo'])], ['target'=>'_blank', 'data-pjax'=>"0"]);
                    else
                        return '';
                    //return Html::a('@web/logo/linkIcon.png'/* . $model['img']*/,
                    //return Html::img('/pathToImage/'/* . $model['img']*/,
                    //['width' => '20px']);
                },
            ],
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
