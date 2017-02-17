<?php
$this->title = 'Dispatch';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="dispatch-dispatch">

    <?= \kartik\grid\GridView::widget([
        'id' => 'dispatchGV',
        'dataProvider' => $dispatchDataProvider, // Sent from DispatchController.php
        'export' => false,
        'caption' => 'Dispatch',
        'columns' => [
            [
                'label' => 'Division',
                'attribute' => 'division',
                'format' => 'html',
                'value' => function ($model) {
                    return "Office<br/>" . $model['Division'] . "<br/>" . $model['MapGrid'];
                }
            ],
            [
                'label' => 'Dispatch to',
                'attribute' => 'inspector',
                'format' => 'html',
                'value' => function($model) {
                    return "Dispatch dropdown here";
                }
            ],
            [
                'label' => 'Compliance Date',
                'attribute' => 'complianceDate',
                'format' => 'html',
                'value' => function ($model) {
                    return "Start: " . $model['ComplianceStartDate'] . "<br/>End: " . $model['ComplianceEndDate'];
                }
            ],
            [
                'label' => 'View Assets',
                'attribute' => 'viewassets',


            ],
            [
                'class' => 'kartik\grid\ActionColumn',
                'template' => '{view}',
                'urlCreator' => function ($action, $model, $key, $index) {
                    if ($action === 'view') {
                        $url ='/dispatch/assets?id='.$model['MapGrid']; //TODO: change to correct identifier.
                        return $url;
                    }
                    return "";
                }
            ]
        ]

    ]);

    ?>

    <?= \kartik\grid\GridView::widget([
        'id' => 'surveyorsGV',
        'dataProvider' => $surveyorsDataProvider, // Sent from DispatchController.php
        'export' => false,
        'caption' => 'Surveyors',
        'columns' => [
                [
                    'label' => 'Name',
                    'attribute' => 'name',
                    'value' => function($model) {
                        return $model['Name'];
                    }
                ],
                [
                    'label' => 'Division',
                    'format' => 'html',
                    'value' => function ($model) {
                        return $model['Division'];
                    }
                ]
        ]

    ]);

    ?>
</div>
