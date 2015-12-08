<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\MileageCard */

$this->title = 'Create Mileage Card';
$this->params['breadcrumbs'][] = ['label' => 'Mileage Cards', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mileage-card-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
