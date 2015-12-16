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

    <!--h1><?//= Html::encode($this->title) ?></h1-->
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create User', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
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

            //['class' => 'yii\grid\ActionColumn'],
			
			['class' => 'yii\grid\ActionColumn',
                             /* 'buttons'=>[
                              'View' => function ($url, $model) { 
                                return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [
                                        'title' => Yii::t('yii', 'View'),
                                ]);                                
            
                              }
                          ], */
							  'urlCreator' => function ($action, $model, $key, $index) {
								  //var_dump($model["UserID"]);
											if ($action === 'view') {
											$url ='index.php?r=user%2Fview&id='.$model["UserID"];
											return $url;
											}
											if ($action === 'update') {
											$url ='index.php?r=user%2Fupdate&id='.$model["UserID"];
											return $url;
											}
										}						  
                            ],
			],
    ]); ?>

</div>
