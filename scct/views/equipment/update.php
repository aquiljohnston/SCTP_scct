<?php

use yii\helpers\Html;
use app\assets\EquipmentAsset;

//register assets
EquipmentAsset::register($this);

/* @var $this yii\web\View */
/* @var $model app\models\equipment */

$this->title = 'Update Equipment: ' . ' ' . $model->EquipmentName;
$this->params['breadcrumbs'][] = ['label' => 'Equipments', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->EquipmentID, 'url' => ['view', 'id' => $model->EquipmentID]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="equipment-update">

    <h1 class="title"><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a('Back', ['index'], ['class' => 'btn btn-primary']) ?>
	</p>
	
    <?= $this->render('_updateForm', [
        'model' => $model,
		'clients' => $clients,
		'types' => $types,
		'conditions' => $conditions,
		'statuses' => $statuses,
		'users' => $users,
		'projects' => $projects,
    ]) ?>

</div>
