<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ClientSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Clients';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-index">

    <h3 class="title"><?= Html::encode($this->title) ?></h3>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Client', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'export' => false,
		'bootstrap' => false,
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],

            //'ClientID',
			[
				'label' => 'Name',
				'attribute' => 'ClientName',
				'filter' => '<input class="form-control" name="filterclientname" value="' . Html::encode($searchModel['ClientName']) . '" type="text">'
			],
			[
				'label' => 'Contact Title',
				'attribute' => 'ClientContactTitle',
				'filter' => '<input class="form-control" name="filtertitle" value="' . Html::encode($searchModel['ClientContactTitle']) . '" type="text">'
			],
			[
				'label' => 'Contact First Name',
				'attribute' => 'ClientContactFName',
				'filter' => '<input class="form-control" name="filterfname" value="' . Html::encode($searchModel['ClientContactFName']) . '" type="text">'
			],
			[
				'label' => 'Contact M.I.',
				'attribute' => 'ClientContactMI',
				'filter' => '<input class="form-control" name="filtermi" value="' . Html::encode($searchModel['ClientContactMI']) . '" type="text">'
			],
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

            ['class' => 'kartik\grid\ActionColumn',
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
