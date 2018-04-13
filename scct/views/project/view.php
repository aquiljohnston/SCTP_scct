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
        <?= Html::a('Update', ['update', 'id' => $model->ProjectID], ['class' => 'btn btn-primary', 'style' => 'display: none']) ?>
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
            [
                'label' => 'Project Client',
                'value' => function($model, $widget) {
                    return Html::a($model->ClientName, ['client/view', 'id' => $model->ProjectClientID]);;
                },
                'format' => 'html'
            ],
			'ProjectState',
            'ProjectStartDate',
            'ProjectEndDate',	
			'ProjectCreateDate',
			[
                'label' => 'Project Created By',
                'value' => function($model, $widget) {
                    return Html::a($model->CreatedUserName, ['user/view', 'username' => $model->CreatedUserName]);;
                },
                'format' => 'html'
            ],
			'ProjectModifiedDate',
        ],
    ]) ?>

</div>
