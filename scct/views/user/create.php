<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\user */

$this->title = 'Create User';
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-create">

    <h1 class="title"><?= Html::encode($this->title) ?></h1>
	
	<p>
		<?= Html::a('Back', ['index'], ['class' => 'btn btn-primary']) ?>
	</p>

    <?= $this->render('_form', [
        'model' => $model,
		'roles' => $roles,
		'types' => $types,
		'flag' => $flag,
		'duplicateFlag' => $duplicateFlag,
    ]) ?>

</div>
