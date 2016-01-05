<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\user */

$this->title = $model->UserID;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
		<?= Html::a('Back', ['index'], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Update', ['update', 'id' => $model->UserID], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->UserID], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'delete',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'UserID',
            'UserName',
            'UserFirstName',
            'UserLastName',
            'UserLoginID',
            'UserEmployeeType',
            'UserPhone',
            'UserCompanyName',
            'UserCompanyPhone',
            'UserAppRoleType',
            'UserComments',
            'UserKey',
            'UserActiveFlag',
            'UserCreatedDate',
            'UserModifiedDate',
            'UserCreatedBy',
            'UserModifiedBy',
            'UserCreateDTLTOffset',
            'UserModifiedDTLTOffset',
            'UserInactiveDTLTOffset',
        ],
    ]) ?>

</div>
