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
use yii\helpers\Url;
use kartik\datetime\DateTimePicker;

?>

<div class="cgeExpand">

    <?= GridView::widget([
        'dataProvider' => $sectionDataProvider,
        'export' => false,
        'id' => 'cgeAssetsGV',
        'summary' => '',
        //'headerRowOptions' => ['style' => 'display: none'],
        'columns' => [
            [
                'label' => 'Inspector',
                'attribute' => 'Inspector',
                'headerOptions' => ['class' => 'text-center','style' => 'width: 15%;'],
                'contentOptions' => ['class' => 'text-center','style' => 'width: 15%;'],
            ],
             [
                'label' => 'Customer Info',
                'attribute' => 'CustomerInfo',
                'format' => 'raw',
                'headerOptions' => ['class' => 'text-center','style' => 'width: 17.5%;'],
                'contentOptions' => ['class' => 'text-center','style' => 'width: 17.5%;'],
            ],
            [
                'label' => 'Address',
                'attribute' => 'Address',
                'headerOptions' => ['class' => 'text-center','style' => 'width: 15%;'],
                'contentOptions' => ['class' => 'text-center','style' => 'width: 15%;'],
            ],
            [
                'label' => 'Last Inspection Date',
                'attribute' => 'InspectionDateTime',
                'headerOptions' => ['class' => 'text-center','style' => 'width: 15%;'],
                'contentOptions' => ['class' => 'text-center','style' => 'width: 15%;'],
            ],
            [
                'label' => 'Scheduled Date',
                'attribute' => 'ScheduledDate',
                'headerOptions' => ['class' => 'text-center','style' => 'width: 20%;'],
                'contentOptions' => ['class' => 'text-center ScheduledDate','style' => 'width: 20%;'],
                'value' => function($model){
                        $uniqueID = uniqid();
                    if ($model['ScheduleRequired'] == 1 ) {
                        return DateTimePicker::widget([
                            'name' => 'ScheduledDate',
                            'options' => [
                                'placeholder' => Yii::t('app', 'Enter the date ...'),
                                'id' => $uniqueID,
                            ],
                            'pluginOptions' => [
                                'autoclose' => true,
                                'format' => 'mm/dd/yyyy hh:ii:ss',
                                'todayHighlight' => true
                            ],
                            'pluginEvents' => [
                                //"changeDate" => "function(e) {  alert('date changed'); }",
                            ]
                        ]);
                    }else {
                        return DateTimePicker::widget([
                            'name' => 'ScheduledDate',
                            'disabled' => true
                        ]);
                    }
                },
                'format' => 'raw'
            ],
            [
                'label' => 'Inspection Type',
                'attribute' => 'SurveyType',
                'headerOptions' => ['class' => 'text-center','style' => 'width: 7.5%;'],
                'contentOptions' => ['class' => 'text-center','style' => 'width: 7.5%;'],
            ],
            [
                'label' => 'Billing Code',
                'attribute' => 'BillingCode',
                'headerOptions' => ['class' => 'text-center','style' => 'width: 7.5%;'],
                'contentOptions' => ['class' => 'text-center','style' => 'width: 7.5%;'],
            ],
            [
                'label' => 'Office Name',
                'attribute' => 'OfficeName',
                'headerOptions' => ['class' => 'text-center','style' => 'width: 7.5%;'],
                'contentOptions' => ['class' => 'text-center','style' => 'width: 7.5%;'],
            ],
            [
                'attribute' => 'Image',
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
                            return Html::a('', null, ['class' =>'glyphicon glyphicon-eye-open', 'onclick' => "viewAssetRowClicked('/dispatch/cge/view-history?workOrderID=".$model['WorkOrderID']."','".$modalViewHistoryDetailCGE ."','".$modalContentViewHistoryDetailCGE."')"]);
                    }
                ],
                'urlCreator' => function ($action, $model, $key, $index) {
                }
            ],
            [
                'header' => '',
                'class' => 'kartik\grid\CheckboxColumn',
                'headerOptions' => ['class' => 'text-center','style' => 'width: 5%;'],
                'contentOptions' => ['class' => 'text-center cgeDispatchAssetsCheckbox','style' => 'width: 5%;'],
                'checkboxOptions' => function ($model, $key, $index, $column) {
                    /*if ($model['CGE'] == 1)
                        return ['disabled' => true, 'checked' => true];
                    else
                        return ['disabled' => true, 'checked' => false];*/
                    return ['ScheduledDate' => 'ScheduledDate', 'disabled' => 'disabled', 'WorkOrderID' => $model['WorkOrderID'], 'SectionNumber' =>$model['SectionNumber']];
                }
            ]
        ],
    ]); ?>

</div>
