<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\assets\ProjectAsset;

//register assets
ProjectAsset::register($this);

/* @var $this yii\web\View */
/* @var $model app\models\project */

$this->title = $model->ProjectName;
$this->params['breadcrumbs'][] = ['label' => 'Projects', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-view">

    <h1 class="title"><?= Html::encode($this->title) ?></h1>

    <p>
		<?= Html::a('Back', ['index'], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Update', ['update', 'id' => $model->ProjectID], ['class' => 'btn btn-primary', 'style' => 'display: none']) ?>
		<?= Html::a('Add/Remove Users', ['add-user', 'id' => $model->ProjectID], ['class' => 'btn btn-primary']) ?>
		<?= Html::a('Add/Remove Modules', ['add-module', 'id' => $model->ProjectID], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
				'label' => 'Name',
				'value' => $model->ProjectName,
			],[
				'label' => 'ReferenceID',
				'value' => $model->ProjectReferenceID,
			],[
				'label' => 'Type',
				'value' => $model->ProjectType,
			],[
				'label' => 'State',
				'value' => $model->ProjectState,
			],[
				'label' => 'End of Day Task Out',
				'value' => $model->IsEndOfDayTaskOut == 1 ? 'Yes' : 'No',
			],
        ],
    ]) ?>

</div>
