<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ClientSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Clients';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-index">

    <h1 class="title"><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Client', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'ClientID',
            'ClientName',
            'ClientContactTitle',
            'ClientContactFName',
            'ClientContactMI',
            // 'ClientContactLName',
            // 'ClientPhone',
            // 'ClientEmail:email',
            // 'ClientAddr1',
            // 'ClientAddr2',
            // 'ClientCity',
            // 'ClientState',
            // 'ClientZip4',
            // 'ClientTerritory',
            // 'ClientActiveFlag',
            // 'ClientDivisionsFlag',
            // 'ClientComment',
            // 'ClientCreateDate',
            // 'ClientCreatorUserID',
            // 'ClientModifiedDate',
            // 'ClientModifiedBy',

            ['class' => 'yii\grid\ActionColumn',
				'urlCreator' => function ($action, $model, $key, $index) {
        			if ($action === 'view') {
        			$url ='index.php?r=client%2Fview&id='.$model["ClientID"];
        			return $url;
        			}
        			if ($action === 'update') {
        			$url ='index.php?r=client%2Fupdate&id='.$model["ClientID"];
        			return $url;
        			}
        			if ($action === 'delete') {
        			$url ='index.php?r=client%2Fdelete&id='.$model["ClientID"];											
        			return $url;
        			}
        		},
                'buttons' => [
                    'delete' => function ($url, $model, $key) {
                        $url ='/index.php?r=client%2Fdelete&id='.$model["ClientID"];       
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
