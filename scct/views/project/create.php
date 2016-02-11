<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\project */

$this->title = 'Create';
$this->params['breadcrumbs'][] = ['label' => 'Projects', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-create">

    <h1 class="title"><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a('Back', ['index'], ['class' => 'btn btn-primary']) ?>
	</p>
	
    <?= $this->render('_form', [
        'model' => $model,
		'clients' => $clients,
		'flag' => $flag,
    ]) ?>

</div>
