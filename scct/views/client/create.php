<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\client */

$this->title = 'Create Client';
$this->params['breadcrumbs'][] = ['label' => 'Clients', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-create">

    <h1 class="title"><?= Html::encode($this->title) ?></h1>
	
	<p>
		<?= Html::a('Back', ['index'], ['class' => 'btn btn-primary']) ?>
	</p>

	<?php if($createFailed): ?>
		<div class="alert alert-warning">
			One or more of the fields below are invalid.
		</div>
	<?php endif; ?>

    <?= $this->render('_form', [
        'model' => $model,
		'flag' => $flag,
		'clientAccounts' => $clientAccounts,
		'states' => $states,
    ]) ?>

</div>
