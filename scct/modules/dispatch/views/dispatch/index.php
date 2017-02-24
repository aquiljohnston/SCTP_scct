<?php
use kartik\form\ActiveForm;
use yii\helpers\Html;

$this->title = 'Dispatch';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="dispatch">
    <?php
        $form = ActiveForm::begin([
            'type' => ActiveForm::TYPE_VERTICAL,
            'options' => ['id' => 'dispatchFilterForm', 'data-pjax' => true]
        ]);
    ?>
    <div id="surveyTypeDropdownWrapper" class="dropdowntitle">
        <?php
        // echo $form->field($filterModel, 'surveytype')->dropDownList(null, ['id' => 'dispatch-surveytype-id', 'onchange' => 'this.form.submit()', 'disabled' => false])->label('Survey Freq');
        ?>
    </div>

    <?php ActiveForm::end(); ?>


    <?= \kartik\grid\GridView::widget([
        'id' => 'dispatchGV',
        'dataProvider' => $dispatchDataProvider, // Sent from DispatchController.php
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
                        $url = '/dispatch/assets?id=' . $model['MapGrid']; //TODO: change to correct identifier.
                        return $url;
                    }
                    return "";
                }
            ],
            [
                'header' => 'Add Surveyor',
                'class' => 'kartik\grid\CheckboxColumn'
            ]
        ]

    ]);

    ?>

    <?= \kartik\grid\GridView::widget([
        'id' => 'surveyorsGV',
        'dataProvider' => $surveyorsDataProvider, // Sent from DispatchController.php
        'export' => false,
        'pjax' => true,
        'caption' => 'Surveyors',
        'columns' => [
            [
                'class' => 'kartik\grid\CheckboxColumn'
            ],
            [
                'label' => 'Name',
                'attribute' => 'name',
                'value' => function ($model) {
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
    <?php echo Html::button('DISPATCH', ['class' => 'btn btn-primary dispatch_btn', 'id' => 'dispatchButton']); ?>
</div>
