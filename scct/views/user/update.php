<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\user */

$this->title = 'Update User: ' . ' ' . $model->UserLastName . ', ' .$model->UserFirstName ;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->UserName, 'url' => ['view', 'username' => $model->UserName]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="user-update">

    <h1 class="titleInfo"><?= Html::encode($this->title) ?></h1>
	
	<p>
		<?= Html::a('Back', ['index'], ['class' => 'btn btn-primary']) ?>
	</p>
	
    <?= $this->render('_updateForm', [
        'model' => $model,
		'roles' => $roles,
		'types' => $types,
    ]) ?>

</div>
