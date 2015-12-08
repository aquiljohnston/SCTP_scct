<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Users';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create User', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'UserID',
            'UserName',
            'UserFirstName',
            'UserLastName',
            'UserLoginID',
            // 'UserEmployeeType',
            // 'UserPhone',
            // 'UserCompanyName',
            // 'UserCompanyPhone',
            // 'UserAppRoleType',
            // 'UserComments',
            // 'UserKey',
            // 'UserActiveFlag',
            // 'UserCreatedDate',
            // 'UserModifiedDate',
            // 'UserCreatedBy',
            // 'UserModifiedBy',
            // 'UserCreateDTLTOffset',
            // 'UserModifiedDTLTOffset',
            // 'UserInactiveDTLTOffset',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
