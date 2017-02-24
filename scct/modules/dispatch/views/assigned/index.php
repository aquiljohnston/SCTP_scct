<?php
use yii\bootstrap\Html;

$this->title = 'Dispatch';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="dispatch-assigned">

    <?= \kartik\grid\GridView::widget([
        'id' => 'assignedGV',
        'dataProvider' => $assignedDataProvider, // Sent from DispatchController.php
        'export' => false,
        'pjax' => true,
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
                'label' => 'Compliance Date',
                'attribute' => 'complianceDate',
                'format' => 'html',
                'value' => function ($model) {
                    return "Start: " . $model['ComplianceStartDate'] . "<br/>End: " . $model['ComplianceEndDate'];
                }
            ],
            [
                'class' => 'kartik\grid\ActionColumn',
                'template' => '{view}',
                'header' => 'View<br/>Assets',
                'urlCreator' => function ($action, $model, $key, $index) {
                    if ($action === 'view') {
                        $url ='/dispatch/assets?id='.$model['MapGrid']; //TODO: change to correct identifier.
                        return $url;
                    }
                    return "";
                }
            ],
            [
                'class' => 'kartik\grid\CheckboxColumn'
            ]
        ]

    ]);

    ?>
    <?= Html::a('Add Surveyors', null, ['class' => 'btn btn-primary', 'id' => 'addSurveyorsButton']) ?>
    <?= Html::a('Un-Assign', null, ['class' => 'btn btn-primary', 'id' => 'unAssignButton']) ?>
</div>
