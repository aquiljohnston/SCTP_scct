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
            ],
            [
                'label' => 'Reason',
                'attribute' => 'Reason',
                'headerOptions' => ['class' => 'text-center'],
                'format' => 'html',
                'value' => function($model){
                    if (strpos($model['Reason'], '|') !== false) {
                        list($LeakNumber, $Grade) = explode('|', $model['Reason'], 2);
                        return "<span style='margin-left: 10%'>" . $LeakNumber . "</span><br/><span style='margin-left: 10%'>". $Grade."</span>";
                    }else{
                        return $model['Reason'];
                    }
                }
            ],
            [
                'label' => 'Comments',
                'attribute' => 'Comments',
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
                        return Html::a(Html::img(Yii::getAlias('@web/logo/linkIcon.png'), ['width' => '20px']),['/dispatch/inspections/view-image?Photo1Path='.$model['Photo']],['target'=>'_blank', 'data-pjax'=>"0"]);
                    else
                        return '';
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
