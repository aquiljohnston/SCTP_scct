<?php

use yii\helpers\Html;
use app\assets\EquipmentAsset;

//register assets
EquipmentAsset::register($this);


/* @var $this yii\web\View */
/* @var $model app\models\equipment */

$this->title = 'Create Equipment';
$this->params['breadcrumbs'][] = ['label' => 'Equipment', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="equipment-create">

    <h1 class="title"><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a('Back', ['index'], ['class' => 'btn btn-primary']) ?>
	</p>
	
    <?= $this->render('_createForm', [
        'model' => $model,
		'clients' => $clients,
		'types' => $types,
		'conditions' => $conditions,
		'statuses' => $statuses,
    ]) ?>

</div>
