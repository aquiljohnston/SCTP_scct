<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\dialog\Dialog;
use app\assets\UserAsset;

//register assets
UserAsset::register($this);

/* @var $this yii\web\View */
/* @var $model app\models\user */

$this->title = $model['UserLastName'] . ', ' .$model['UserFirstName'];
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view">

    <h1 class="title"><?= Html::encode($this->title) ?></h1>

    <p>
		<?= Html::a('Back', ['index'], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Update', ['update', 'username' => $model['UserName']], ['class' => array_search($_SESSION['UserAppRoleType'], $userPermissionTable) > array_search($model['UserAppRoleType'], $userPermissionTable) ? 'btn btn-primary disabled' : 'btn btn-primary']) ?>
        <?php
            echo Dialog::widget(['overrideYiiConfirm' => true]);
            echo Html::a(
                        'Deactivate', 
                        ['deactivate', 'username' => $model['UserName']],
                            [
                                'data-confirm' => 'Are you sure you want to deactivate this user?',
                                'data-method' => 'post',
                                'aria-label' => Yii::t('yii', 'Deactivate'),
                                'class' =>array_search($_SESSION['UserAppRoleType'], $userPermissionTable) > array_search($model['UserAppRoleType'], $userPermissionTable) ? 'btn btn-danger disabled' : 'btn btn-danger'
                            ]);
        ?>
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
