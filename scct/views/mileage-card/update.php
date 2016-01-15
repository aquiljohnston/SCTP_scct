<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\mileagecard */

$this->title = 'Update MileageCard: ' . ' ' . $model->MileageCardID;
$this->params['breadcrumbs'][] = ['label' => 'MileageCard', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->MileageCardID, 'url' => ['view', 'id' => $model->MileageCardID]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="user-update">

    <h1 class="title"><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a('Back', ['index'], ['class' => 'btn btn-primary']) ?>
	</p>
	
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
