<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use app\controllers\User;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'User Management';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h3 class="title"><?= Html::encode($this->title) ?></h3>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create User', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'bootstrap' => false,
		'export' => false,
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],

            //'UserID',
			[
				'label' => 'Username',
				'attribute' => 'UserName',
				'filter' => '<input class="form-control" name="filterusername" value="' . Html::encode($searchModel['UserName']) . '" type="text">'
			],
			[
            	'label' => 'First Name',
				'attribute' => 'UserFirstName',
				'filter' => '<input class="form-control" name="filterfirstname" value="' . Html::encode($searchModel['UserFirstName']) . '" type="text">'
			],
			[
				'label' => 'Last Name',
				'attribute' => 'UserLastName',
				'filter' => '<input class="form-control" name="filterlastname" value="' . Html::encode($searchModel['UserLastName']) . '" type="text">'
			],
			[
				'label' => 'Role Type',
				'attribute' => 'UserAppRoleType',
				'filter' => '<input class="form-control" name="filterroletype" value="' . Html::encode($searchModel['UserAppRoleType']) . '" type="text">'
			],
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
			
			['class' => 'kartik\grid\ActionColumn',
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
        			$url ='index.php?r=user%2FDeactivate&id='.$model["UserID"];											
        			return $url;
        			}
        		},
                'buttons' => [
                    'delete' => function ($url, $model, $key) {
                        $url ='/index.php?r=user%2Fdeactivate&id='.$model["UserID"];       
                            $options = [
                            'title' => Yii::t('yii', 'Deactivate'),
                            'aria-label' => Yii::t('yii', 'Deactivate'),
                            'data-confirm' => Yii::t('yii', 'Are you sure you want to deactivate this user?'),
                            'data-method' => 'Put',
                            'data-pjax' => '0',
                            ];
                            return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, $options);
                    },
                ]						  
            ],
		],
    ]); ?>

</div>
