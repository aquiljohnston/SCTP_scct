<?php
/**
 * Created by PhpStorm.
 * User: tzhang
 * Date: 10/2/2017
 * Time: 3:54 PM
 */

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\bootstrap\Modal;

?>

<div class="cgeExpand">

    <?= GridView::widget([
        'dataProvider' => $sectionDataProvider,
        'export' => false,
        'id' => 'cgeSectionGV',
        'summary' => '',
        //'headerRowOptions' => ['style' => 'display: none'],
        'columns' => [
            [
                'label' => 'Inspector',
                'attribute' => 'Inspector',
                'headerOptions' => ['class' => 'text-center','style' => 'width: 25%;'],
                'contentOptions' => ['class' => 'text-center','style' => 'width: 25%;'],
            ],
            [
                'label' => 'Address',
                'attribute' => 'Address',
                'headerOptions' => ['class' => 'text-center','style' => 'width: 25%;'],
                'contentOptions' => ['class' => 'text-center','style' => 'width: 25%;'],
            ],
            [
                'label' => 'InspectionDateTime',
                'attribute' => 'InspectionDateTime',
                'headerOptions' => ['class' => 'text-center','style' => 'width: 25%;'],
                'contentOptions' => ['class' => 'text-center','style' => 'width: 25%;'],
            ],
            [
                'header' => 'CGE',
                'class' => 'kartik\grid\CheckboxColumn',
                'headerOptions' => ['class' => 'text-center CGE','style' => 'width: 5%;'],
                'contentOptions' => ['class' => 'text-center','style' => 'width: 5%;'],
                'checkboxOptions' => function ($model, $key, $index, $column) {
                    /*if ($model['CGE'] == 1)
                        return ['disabled' => true, 'checked' => true];
                    else
                        return ['disabled' => true, 'checked' => false];*/
                }
            ],
            [
                'attribute' => 'img',
                'format' => 'raw',
                'label' => 'Image',
                'headerOptions' => ['class' => 'text-center','style' => 'width: 5%;'],
                'contentOptions' => ['class' => 'text-center','style' => 'width: 5%;'],
                'value' => function ($model) {
                    if ($model['Image'] != null)
                        return Html::a(Html::img(Yii::getAlias('@web/logo/linkIcon.png'), ['width' => '20px']),[Url::to('/../images/'.$model['Image'])], ['target'=>'_blank', 'data-pjax'=>"0"]);
                    else
                        return '';
                },
            ],
            [
                'header' => 'View History',
                'class' => 'kartik\grid\ActionColumn',
                'template' => '{view}',
                'headerOptions' => ['class' => 'text-center','style' => 'width: 5%;'],
                'contentOptions' => ['class' => 'text-center','style' => 'width: 5%;'],
                'buttons' => [
                    'view' => function($url, $model) {
                        $modalViewHistoryDetailCGE = "#modalViewHistoryDetailCGE";
                        $modalContentViewHistoryDetailCGE = "#modalContentViewHistoryDetailCGE";
                            return Html::a('', null, ['class' =>'glyphicon glyphicon-eye-open', 'onclick' => "viewAssetRowClicked('/dispatch/cge/view-asset?workOrderID=".$model['ID']."','".$modalViewHistoryDetailCGE ."','".$modalContentViewHistoryDetailCGE."')"]);
                    }
                ],
                'urlCreator' => function ($action, $model, $key, $index) {
                }
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