<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\controllers\User;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Users';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1 class="title"><?= Html::encode($this->title) ?></h3>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create User', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'UserID',
            'UserName',
            'UserFirstName',
            'UserLastName',
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
			
			['class' => 'yii\grid\ActionColumn',
                'urlCreator' => function ($action, $model, $key, $index) {
        			if ($action === 'view') {
        			$url ='index.php?r=user%2Fview&id='.$model["UserID"];
        			return $url;
        			}
        			if ($action === 'update') {
        			$url ='index.php?r=user%2Fupdate&id='.$model["UserID"];
        			return $url;
        			}
        			if ($action === 'delete') {
        			$url ='index.php?r=user%2Fdelete&id='.$model["UserID"];											
        			return $url;
        			}
        		},
                'buttons' => [
                    'delete' => function ($url, $model, $key) {
                        $url ='/index.php?r=user%2Fdelete&id='.$model["UserID"];       
                            $options = [
                            'title' => Yii::t('yii', 'Delete'),
                            'aria-label' => Yii::t('yii', 'Delete'),
                            'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                            'data-method' => 'Delete',
                            'data-pjax' => '0',
                            ];
                            return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, $options);
                    },
                ]						  
            ],
		],
    ]); ?>

</div>
