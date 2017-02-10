<?php
$this->title = 'Dispatch';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="dispatch-dispatch">

    <?= \kartik\grid\GridView::widget([
        'id' => 'dispatchGV',
        'dataProvider' => $dataProvider, // Sent from DispatchController.php
        'export' => false,
        'caption' => 'Dispatch',
        'columns' => [
            [
                'label' => 'Division',
                'attribute' => 'division',
                'value' => function ($model) {
                    return $model['Division'];
                }
            ],
            [
                'label' => 'MapGrid',
                'attribute' => 'mapgrid',
                'value' => function ($model) {
                    return $model['MapGrid'];
                }
            ],
            [
                'label' => 'Compliance Date',
                'attribute' => 'complianceDate',
                'value' => function ($model) {
                    return $model['ComplianceStartDate'] . "<br/>" . $model['ComplianceEndDate'];
                }
            ]
        ]

    ]);

    ?>
</div>
