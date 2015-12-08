<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\TimeCard */

$this->title = 'Update Time Card: ' . ' ' . $model->TimeCardID;
$this->params['breadcrumbs'][] = ['label' => 'Time Cards', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->TimeCardID, 'url' => ['view', 'id' => $model->TimeCardID]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="time-card-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
