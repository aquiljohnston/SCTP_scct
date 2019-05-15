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
        'columns' => [
            [
                'label' => 'Inspector',
                'attribute' => 'Inspector',
                'headerOptions' => ['class' => 'text-center','style' => 'width: 9%;'],
                'contentOptions' => ['class' => 'text-center','style' => 'width: 9%;'],
            ],
            [
                'label' => 'Customer Info',
                'attribute' => 'CustomerInfo',
                'format' => 'raw',
                'headerOptions' => ['class' => 'text-center','style' => 'width: 12%;'],
                'contentOptions' => ['class' => 'text-center','style' => 'width: 12%;'],
            ],
            [
                'label' => 'Address',
                'attribute' => 'Address',
                'headerOptions' => ['class' => 'text-center','style' => 'width: 10%;'],
                'contentOptions' => ['class' => 'text-center','style' => 'width: 10%;'],
            ],
            [
                'label' => 'Last Inspection Date',
                'attribute' => 'InspectionDateTime',
                'headerOptions' => ['class' => 'text-center','style' => 'width: 9%;'],
                'contentOptions' => ['class' => 'text-center','style' => 'width: 9%;'],
            ],
			[
                'label' => 'CGE Reason',
                'attribute' => 'CGEReason',
                'headerOptions' => ['class' => 'text-center','style' => 'width: 18.5%;'],
                'contentOptions' => ['class' => 'text-center','style' => 'width: 18.5%;'],
            ],
            [
                'label' => 'Scheduled Date',
                'attribute' => 'ScheduledDate',
                'headerOptions' => ['class' => 'text-center','style' => 'width: 15.5%;'],
                'contentOptions' => ['class' => 'text-center ScheduledDate','style' => 'width: 15.5%;'],
                'value' => function($model){
                        $uniqueID = uniqid();
                        return DateTimePicker::widget([
                            'name' => 'ScheduledDate',
                            'options' => [
                                'placeholder' => Yii::t('app', 'Enter scheduled date ...'),
                                'id' => $uniqueID,
                            ],
                            'pluginOptions' => [
                                'autoclose' => true,
                                'format' => 'mm/dd/yyyy hh:ii:ss',
                                'todayHighlight' => true
                            ]
                        ]);
                },
                'format' => 'raw'
            ],
            [
                'label' => 'Inspection Type',
                'attribute' => 'InspectionType',
                'headerOptions' => ['class' => 'text-center','style' => 'width: 5%;'],
                'contentOptions' => ['class' => 'text-center','style' => 'width: 5%;'],
            ],
            [
                'label' => 'Billing Code',
                'attribute' => 'BillingCode',
                'headerOptions' => ['class' => 'text-center','style' => 'width: 5%;'],
                'contentOptions' => ['class' => 'text-center','style' => 'width: 5%;'],
            ],
            [
                'label' => 'Office Name',
                'attribute' => 'OfficeName',
                'headerOptions' => ['class' => 'text-center','style' => 'width: 5%;'],
                'contentOptions' => ['class' => 'text-center','style' => 'width: 5%;'],
            ],
            [
                'attribute' => 'Image',
                'format' => 'raw',
                'label' => 'Image',
                'headerOptions' => ['class' => 'text-center','style' => 'width: 3.5%;'],
                'contentOptions' => ['class' => 'text-center','style' => 'width: 3.5%;'],
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
                'headerOptions' => ['class' => 'text-center','style' => 'width: 2.5%;'],
                'contentOptions' => ['class' => 'text-center cgeDispatchAssetsCheckbox','style' => 'width: 5%;'],
                'checkboxOptions' => function ($model, $key, $index, $column) {
                    return [
						'ScheduledDate' => '',
						'ScheduleRequired' => $model['ScheduleRequired'],
						'WorkOrderID' => $model['WorkOrderID'],
						'SectionNumber' =>$model['SectionNumber'],
						'disabled' => ($model['ScheduleRequired'] == 1 ? 'disabled' : false)
					];
                }
            ]
        ],
    ]); ?>

</div>
