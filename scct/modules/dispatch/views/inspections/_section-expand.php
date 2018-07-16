<?php
/**
 * Created by PhpStorm.
 * User: tzhang
 * Date: 6/6/2017
 * Time: 11:22 AM
 */

use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\grid\CheckboxColumn;
use yii\bootstrap\Modal;

?>

<div class="allegato-index">

    <?= GridView::widget([
        'dataProvider' => $sectionDataProvider,
        'export' => false,
        'id' => 'inspectionEventGV',
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
                'label' => '',
                'attribute' => 'MapGrid',
                'headerOptions' => ['class' => 'text-center', 'style' => 'width: 10.7%;'],
                'contentOptions' => ['class' => 'text-center', 'style' => 'width: 10.7%;'],
            ],
            [
                'label' => 'Section Number',
                'attribute' => 'SectionNumber',
                'headerOptions' => ['class' => 'text-center', 'style' => 'width: 35.7%;'],
                'contentOptions' => ['class' => 'text-center', 'style' => 'width: 35.7%;'],
            ],
            [
                'label' => '',
                'attribute' => 'TotalInspections',
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
                        $modalViewSectionDetailInspection = "#modalViewSectionDetailInspection";
                        $modalContentViewSectionDetailInspection = "#modalContentViewSectionDetailInspection";
                        return Html::a('', null, ['class' =>'glyphicon glyphicon-eye-open', 'onclick' => "viewAssetRowClicked('/dispatch/inspections/view-section-detail-modal?mapGridSelected=" . $model['MapGrid']."&sectionNumberSelected=".$model['SectionNumber']."','".$modalViewSectionDetailInspection ."','".$modalContentViewSectionDetailInspection."')"]);
                    }
                ],
                'urlCreator' => function ($action, $model, $key, $index) {
                }
            ],
        ],
    ]); ?>
</div>
