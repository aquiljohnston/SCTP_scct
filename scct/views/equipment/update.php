<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\equipment */

$this->title = 'Update Equipment: ' . ' ' . $model->EquipmentID;
$this->params['breadcrumbs'][] = ['label' => 'Equipments', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->EquipmentID, 'url' => ['view', 'id' => $model->EquipmentID]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="equipment-update">

    <h1 class="title"><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a('Back', ['index'], ['class' => 'btn btn-primary']) ?>
	</p>
	
    <?= $this->render('_form', [
        'model' => $model,
		'clients' => $clients,
    ]) ?>

</div>
