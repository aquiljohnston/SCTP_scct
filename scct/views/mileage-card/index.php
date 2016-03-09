<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\controllers\MileageCard;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'MileageCard';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

	<h3><?= Html::encode($this->title) ?></h3>
	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	<p class="white_space">

	</p>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],

			'UserFirstName',
			'UserLastName',
			'MileageStartDate',
			'MileageEndDate',
			'MileageCardBusinessMiles',
			'MileageCardApprove',

			['class' => 'yii\grid\ActionColumn',
				'template' => '{view}',
				'urlCreator' => function ($action, $model, $key, $index) {
					if ($action === 'view') {
						$url ='index.php?r=mileage-card%2Fview&id='.$model["MileageCardID"];
						return $url;
					}
				},
			],
		],
	]); ?>

</div>
