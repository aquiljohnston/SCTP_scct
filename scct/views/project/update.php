<?php

use yii\helpers\Html;
use app\assets\ProjectAsset;

//register assets
ProjectAsset::register($this);

/* @var $this yii\web\View */
/* @var $model app\models\project */

$this->title = 'Update Project: ' . ' ' . $projectName;
$this->params['breadcrumbs'][] = ['label' => 'Projects', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ProjectID, 'url' => ['view', 'id' => $model->ProjectID]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="project-update">

    <h1 class="title"><?= Html::encode($this->title) ?></h1>

	<p>
		<?= Html::a('Back', ['index'], ['class' => 'btn btn-primary']) ?>
	</p>
	
    <?= $this->render('_config_form', [
        'model' => $model,
		'projectName' => $projectName,
		'flag' => $flag,
    ]) ?>

</div>
