<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\project */

$this->title = $model->ProjectName;
$this->params['breadcrumbs'][] = ['label' => 'Project Landing', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="projectlanding-view">

    <h1 class="title"><?= Html::encode($this->title) ?></h3>
	
	<p>
		<?= Html::a('Back', ['index'], ['class' => 'btn btn-primary']) ?>
		<?= Html::a('Add/Remove Users', ['add-user', 'id' => $model->ProjectID], ['class' => 'btn btn-primary']) ?>
	</p>
	
	<?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'ProjectName',
            'ProjectDescription',
            'ProjectNotes',
            'ProjectType',
            'ProjectStatus',
            'ProjectClientID',
            'ProjectStartDate',
            'ProjectEndDate',	
			'ProjectCreateDate',
			'ProjectCreatedBy',
			'ProjectModifiedDate',
			'ProjectModifiedBy',
        ],
    ]) ?>

</div>
