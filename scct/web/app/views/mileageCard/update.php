<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\MileageCard */

$this->title = 'Update Mileage Card: ' . ' ' . $model->MileageCardID;
$this->params['breadcrumbs'][] = ['label' => 'Mileage Cards', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->MileageCardID, 'url' => ['view', 'id' => $model->MileageCardID]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="mileage-card-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
