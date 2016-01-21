<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\equipment */

$this->title = 'Create';
$this->params['breadcrumbs'][] = ['label' => 'Equipments', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="equipment-create">

    <h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a('Back', ['index'], ['class' => 'btn btn-primary']) ?>
	</p>
	
    <?= $this->render('_form', [
        'model' => $model,
		'clients' => $clients,
		'types' => $types,
		'conditions' => $conditions,
		'users' => $users,
    ]) ?>

</div>
