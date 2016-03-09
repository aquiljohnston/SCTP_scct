<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\TimeCard */

$this->title = 'Create Mileage Entry';
$this->params['breadcrumbs'][] = ['label' => 'Mileage Entry', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="time-entry-create">
	
    <?= $this->render('_formte', [
        'model' => $model,
    ]) ?>

</div>
