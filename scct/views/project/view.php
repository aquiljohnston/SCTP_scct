<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

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
        <?= Html::a('Update', ['update', 'id' => $model->ProjectID], ['class' => 'btn btn-primary']) ?>
		<?= Html::a('Add/Remove Users', ['add-user', 'id' => $model->ProjectID], ['class' => 'btn btn-primary']) ?>
		<?= Html::a('Add/Remove Modules', ['add-module', 'id' => $model->ProjectID], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            //'ProjectID',
            'ProjectName',
            'ProjectDescription',
            'ProjectNotes',
            'ProjectType',
            'ProjectStatus',
            'ProjectUrlPrefix',
            'ProjectClientID',
			'ProjectState',
            'ProjectStartDate',
            'ProjectEndDate',	
			'ProjectCreateDate',
			'ProjectCreatedBy',
			'ProjectModifiedDate',
			'ProjectModifiedBy',
			
        ],
    ]) ?>

</div>
