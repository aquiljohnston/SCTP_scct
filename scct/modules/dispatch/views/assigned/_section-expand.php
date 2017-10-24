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
                'headerOptions' => ['class' => 'text-center', 'style' => 'width: 3.1%'],
                'contentOptions' => ['class' => 'text-center', 'style' => 'width: 3.1%'],
                'value' => function($model){
                    return "";
                }
            ],
            [
                'label' => 'Section Number',
                'attribute' => 'SectionNumber',
                'headerOptions' => ['class' => 'text-center', 'style' => 'width: 13.8%'],
                'contentOptions' => ['class' => 'text-center', 'style' => 'width: 13.8%'],
            ],
            [
                'label' => 'Assigned User(s)',
                'attribute' => 'SearchString',
                //'attribute' => 'AssignedUser',
                'headerOptions' => ['class' => 'text-center', 'style' => 'visibility: hidden; width: 16.5%'],
                'contentOptions' => ['class' => 'text-center', 'style' => 'width: 16.5%'],
                'format' => 'html',
            ],
            [
                'label' => 'Location Type',
                'attribute' => 'LocationType',
                'headerOptions' => ['class' => 'text-center', 'style' => 'width: 48.5%'],
                'contentOptions' => ['class' => 'text-center', 'style' => 'width: 48.5%'],
            ],
			[
				'label' => 'Remaining/Total',
				'attribute' => 'Counts',
				'headerOptions' => ['class' => 'text-center', 'style' => 'visibility: hidden;width: 5%'],
				'contentOptions' => ['class' => 'text-center', 'style' => 'width: 5%'],
				'format' => 'html',
				'value' => function ($model) {
					return $model['Remaining'] . "/" . $model['Total'];
				}
			],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'header' => 'View<br/>Assets',
                'headerOptions' => ['class' => 'text-center', 'style' => 'visibility: hidden; width: 6%'],
                'contentOptions' => ['class' => 'text-center ViewAssetBtn_AssignedSection',],
                'buttons' => [
                    'view' => function($url, $model) {
                        $modalViewAssetAssigned = "#modalViewAssetAssigned";
                        $modalContentViewAssetAssigned = "#modalContentViewAssetAssigned";
                        return Html::a('', null, ['class' =>'glyphicon glyphicon-eye-open', 'onclick' => "viewAssetRowClicked('/dispatch/assigned/view-asset?mapGridSelected=" . $model['MapGrid'] ."&sectionNumberSelected=" . $model['SectionNumber'] . "','".$modalViewAssetAssigned ."','".$modalContentViewAssetAssigned."','".$model['MapGrid']."')"]);
                    }
                ],
            ],
            [
                'header' => 'Remove User',
                'class' => 'kartik\grid\CheckboxColumn',
                'headerOptions' => ['class' => 'text-center', 'style' => 'visibility: hidden'],
                'contentOptions' => ['class' => 'text-center assignedSectionCheckbox'],
                'checkboxOptions' => function ($model, $key, $index, $column) {
                    if (!empty($model))
                        return ['SectionNumber' => $key, 'MapGrid' => $model['MapGrid'], 'UserName' => $model['SearchString']/*['AssignedUser']*/];
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
