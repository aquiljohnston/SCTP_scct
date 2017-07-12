<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\user */

$this->title = 'Reset Password';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="reset-index">

    <h1 class="title"><?= Html::encode($this->title) ?></h1>

    <?php if($failure): ?>
        There was a problem updating your password. Double check the username and old password.
    <?php endif; ?>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
