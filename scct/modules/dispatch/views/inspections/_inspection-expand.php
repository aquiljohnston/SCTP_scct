<?php
/**
 * Created by PhpStorm.
 * User: tzhang
 * Date: 6/6/2017
 * Time: 1:23 PM
 */
use kartik\form\ActiveForm;
use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\widgets\LinkPager;
use kartik\grid\GridView;
use yii\helpers\Url;

$ImageUrl = 'images/';
?>

<div id="sectionDetailTable">
    <?php Pjax::begin([
        'id' => 'sectionDetailTablePjax',
        'timeout' => 10000,
        'enablePushState' => false]) ?>

    <?= GridView::widget([
        'id' => 'sectionDetailGV',
        'dataProvider' => $sectionDetailDataProvider,
        'export' => false,
        'pjax' => true,
        'summary' => '',
        'columns' => [
            [
                'label' => 'Inspector',
                'attribute' => 'Inspector',
                'headerOptions' => ['class' => 'text-center','style' => 'width: 8%;'],
                'contentOptions' => ['class' => 'text-center','style' => 'width: 8%;'],
            ],
            [
                'label' => 'Address',
                'attribute' => 'Address',
                'headerOptions' => ['class' => 'text-center','style' => 'width: 38.6%;'],
                'contentOptions' => ['class' => 'text-center','style' => 'width: 38.6%;'],
            ],
            [
                'label' => 'InspectionDateTime',
                'attribute' => 'InspectionDateTime',
                'headerOptions' => ['class' => 'text-center','style' => 'width: 22.9%;'],
                'contentOptions' => ['class' => 'text-center','style' => 'width: 22.9%;'],
            ],
            [
                'header' => 'Adhoc',
                'class' => 'kartik\grid\CheckboxColumn',
                'contentOptions' => ['class' => 'Adhoc'],
                'checkboxOptions' => function ($model, $key, $index, $column) {
                    if ($model['Adhoc'] == 1)
                        return ['disabled' => true, 'checked' => true];
                    else
                        return ['disabled' => true, 'checked' => false];
                }
            ],
            [
                'header' => 'AOC',
                'class' => 'kartik\grid\CheckboxColumn',
                'contentOptions' => ['class' => 'AOC'],
                'checkboxOptions' => function ($model, $key, $index, $column) {
                    if ($model['AOC'] == 1)
                        return ['disabled' => true, 'checked' => true];
                    else
                        return ['disabled' => true, 'checked' => false];
                }
            ],
            [
                'header' => 'CGE',
                'class' => 'kartik\grid\CheckboxColumn',
                'contentOptions' => ['class' => 'CGE'],
                'checkboxOptions' => function ($model, $key, $index, $column) {
                    if ($model['CGE'] == 1)
                        return ['disabled' => true, 'checked' => true];
                    else
                        return ['disabled' => true, 'checked' => false];
                }
            ],
            [
                'header' => 'Indication',
                'class' => 'kartik\grid\CheckboxColumn',
                'contentOptions' => ['class' => 'Indication'],
                'checkboxOptions' => function ($model, $key, $index, $column) {
                    if ($model['IsIndicationFlag'] == 1)
                        return ['disabled' => true, 'checked' => true];
                    else
                        return ['disabled' => true, 'checked' => false];
                }
            ],
            [
                'attribute' => 'img',
                'format' => 'raw',
                'label' => 'Image',
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
                'value' => function ($model) {
                    if ($model['Photo1Path'] != null)
                        return Html::a(Html::img(Yii::getAlias('@web/logo/linkIcon.png'), ['width' => '20px']),['/dispatch/inspections/view-image?Photo1Path='.$model['Photo1Path']],['target'=>'_blank', 'data-pjax'=>"0"]);
                        //return Html::a(Html::img(Yii::getAlias('@web/logo/linkIcon.png'), ['width' => '20px']),[Url::to('/../images/'.$model['Photo1Path'])], ['target'=>'_blank', 'data-pjax'=>"0"]);
                    else
                        return '';
                },
            ],
            [
                'header' => 'View Asset',
                'class' => 'kartik\grid\ActionColumn',
                'template' => '{view}',
                'headerOptions' => ['class' => 'text-center'],
                'buttons' => [
                    'view' => function($url, $model) {
                        $modalViewEventDetailInspection = "#modalViewEventDetailInspection";
                        $modalContentViewEventDetailInspection = "#modalContentViewEventDetailInspection";
                        if ($model['HasEvents'] > 0)
                            return Html::a('', null, ['class' =>'glyphicon glyphicon-eye-open', 'onclick' => "viewAssetRowClicked('/dispatch/inspections/view-event?inspectionID=".$model['InspectionID']."','".$modalViewEventDetailInspection ."','".$modalContentViewEventDetailInspection."')"]);
                        else
                            return '';
                    }
                ],
                'urlCreator' => function ($action, $model, $key, $index) {
                }
            ],
        ],
    ]); ?>

    <?php Pjax::end() ?>
</div>


