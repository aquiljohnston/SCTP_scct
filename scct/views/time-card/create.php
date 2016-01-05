<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\TimeCard */

$this->title = 'Create Time Card';
$this->params['breadcrumbs'][] = ['label' => 'Time Cards', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="time-card-create">

    <h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a('Back', ['index'], ['class' => 'btn btn-primary']) ?>
	</p>
	
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
