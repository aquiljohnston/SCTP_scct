<?php
use yii\bootstrap\Html;

$this->title = 'Assets';
$this->params['breadcrumbs'][] = $this->title;
?>

    This is the page for viewing assets with id <?= $id ?>.

<?= Html::a('Back', ['/dispatch/dispatch'], ['class' => 'btn btn-primary']) ?>

<?= \kartik\grid\GridView::widget([
    'id' => 'assetsGV',
    'dataProvider' => $assetsDataProvider, // Sent from DispatchController.php
    'export' => false,
    'pjax' => true,
    'caption' => 'Surveyors',
    'columns' => [
        [
            'label' => 'Lat/Long',
            'attribute' => 'name',
            'value' => function ($model) {
                return $model['LatLong'];
            }
        ],
        [
            'label' => 'Instrument Name',
            'attribute' => 'name',
            'value' => function ($model) {
                return $model['InstrumentName'];
            }
        ],
        [
            'label' => 'Survey Type',
            'attribute' => 'name',
            'value' => function ($model) {
                return $model['SurveyType'];
            }
        ]
    ]
]);
?>