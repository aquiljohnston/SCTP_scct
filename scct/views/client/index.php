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
				'label' => 'Client City',
				'attribute' => 'ClientCity',
				'filter' => '<input class="form-control" name="filtercity" value="' . Html::encode($searchModel['ClientCity']) . '" type="text">'
			],
			[
				'label' => 'Client State',
				'attribute' => 'ClientState',
				'filter' => '<input class="form-control" name="filterstate" value="' . Html::encode($searchModel['ClientState']) . '" type="text">'
			],


            ['class' => 'kartik\grid\ActionColumn',
			'template' => '{view} {update}',
				'urlCreator' => function ($action, $model, $key, $index) {
        			if ($action === 'view') {
        			$url ='index.php?r=client%2Fview&id='.$model["ClientID"];
        			return $url;
        			}
        			if ($action === 'update') {
        			$url ='index.php?r=client%2Fupdate&id='.$model["ClientID"];
        			return $url;
        			}
        		},
			],
        ],
    ]); ?>

</div>
