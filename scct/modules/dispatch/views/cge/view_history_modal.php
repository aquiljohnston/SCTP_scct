<?php
/**
 * Created by PhpStorm.
 * User: tzhang
 * Date: 10/4/2017
 * Time: 11:31 AM
 */
use kartik\form\ActiveForm;
use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\widgets\LinkPager;
use kartik\grid\GridView;

?>
<div id="historyTable">
    <?= GridView::widget([
        'id' => 'historyGV',
        'dataProvider' => $historyDataProvider,
        'export' => false,
        'summary' => '',
        'columns' => [
            [
                'label' => 'Inspector',
                'attribute' => 'Inspector',
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
            ],
            [
                'label' => 'Address',
                'attribute' => 'Address',
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
            ],
            [
                'label' => 'Inspection DateTime',
                'attribute' => 'InspectionDateTime',
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
            ],
			[
			//image
			]
        ],
    ]); ?>
</div>

