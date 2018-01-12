<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\user */

$this->title = $model->UserLastName . ', ' .$model->UserFirstName ;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view">

    <h1 class="title"><?= Html::encode($this->title) ?></h1>

    <p>
		<?= Html::a('Back', ['index'], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Update', ['update', 'username' => $model->UserName], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Deactivate', ['deactivate', 'username' => $model->UserName], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to deactivate this user?',
                'method' => 'put',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'UserName',
            'UserFirstName',
            'UserLastName',
            'UserEmployeeType',
            'UserPhone',
            'UserCompanyName',
            'UserCompanyPhone',
            'UserAppRoleType',
            'UserComments',
            'UserActiveFlag',
        ],
    ]) ?>

</div>
