<?php

use yii\helpers\Html;
use app\assets\ClientAsset;

//register assets
ClientAsset::register($this);

/* @var $this yii\web\View */
/* @var $model app\models\client */

$this->title = 'Update Client: ' . ' ' . $model->ClientName;
$this->params['breadcrumbs'][] = ['label' => 'Clients', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ClientID, 'url' => ['view', 'id' => $model->ClientID]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="client-update">

    <h1 class="title"><?= Html::encode($this->title) ?></h1>
	
	<p>
		<?= Html::a('Back', ['index'], ['class' => 'btn btn-primary']) ?>
	</p>

    <?= $this->render('_form', [
        'model' => $model,
		'flag' => $flag,
		'clientAccounts' => $clientAccounts,
		'states' => $states,
    ]) ?>

</div>
