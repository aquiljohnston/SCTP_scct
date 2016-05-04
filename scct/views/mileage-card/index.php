  <?php

use yii\helpers\Html;
use kartik\grid\GridView;
use app\controllers\MileageCard;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Mileage Cards';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

	<h3 class="title"><?= Html::encode($this->title) ?></h3>
	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	<!-- Approve Multiple Mileage Card button -->
	<p id="mileage_card_approve_btn">
		<?= Html::button('Approve',
			[
				'class' => 'btn btn-primary multiple_approve_btn',
				'id' => 'multiple_mileage_card_approve_btn',
				'data' => []
			])?>
	</p>

	<!-- General Table Layout for displaying Mileage Card Information -->
	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'export' => false,
		'columns' => [
			['class' => 'kartik\grid\SerialColumn'],

			[
				'label' => 'User First Name',
				'attribute' => 'UserFirstName',
				'filter' => '<input class="form-control" name="filterfirstname" value="' . Html::encode($searchModel['UserFirstName']) . '" type="text">'
			],
			[
				'label' => 'User Last Name',
				'attribute' => 'UserLastName',
				'filter' => '<input class="form-control" name="filterlastname" value="' . Html::encode($searchModel['UserLastName']) . '" type="text">'
			],
			'MileageStartDate',
			'MileageEndDate',
			'ProjectName',
			'SumBusinessMiles',
			[
				'label' => 'Approved',
				'attribute' => 'MileageCardApproved',
				'filter' => $approvedInput
			],

			['class' => 'kartik\grid\ActionColumn',
				'template' => '{view}',
				'urlCreator' => function ($action, $model, $key, $index) {
					if ($action === 'view') {
						$url ='index.php?r=mileage-card%2Fview&id='.$model["MileageCardID"];
						return $url;
					}
				},
			],
			[
				'class' => 'kartik\grid\CheckboxColumn',
				'checkboxOptions' => function ($model, $key, $index, $column) {
					return ['mileageCardId' => $model["MileageCardID"], 'approved'=>$model["MileageCardApproved"], 'totalmileage'=>$model["SumBusinessMiles"]];
				}
			],
		],
	]); ?>

</div>
