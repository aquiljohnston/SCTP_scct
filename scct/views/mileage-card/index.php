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
	<div id="mileage_card_approve_btn" style="margin-bottom: 2%; margin-top: 1%;">
		<?php
		echo Html::button('Approve',
			[
				'class' => 'btn btn-primary multiple_approve_btn',
				'id' => 'multiple_mileage_card_approve_btn',
			]);
		if($week=="prior") {
			$priorSelected = "selected";
			$currentSelected = "";
		} else {
			$priorSelected = "";
			$currentSelected = "selected";
		}
		?>
		<form method="GET" style="display: inline;">
			<select name="week" onchange="this.form.submit()">
				<option value="prior" <?= $priorSelected ?>>Prior Week</option>
				<option value="current" <?= $currentSelected ?>>Current Week</option>
			</select>
			<input type="hidden" name="r" value="mileage-card/index" />
		</form>
	</div>
	<!-- General Table Layout for displaying Mileage Card Information -->
	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'export' => false,
		'bootstrap' => false,
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
			[
				'label' => 'Project Name',
				'attribute' => 'ProjectName',
				'filter' => '<input class="form-control" name="filterprojectname" value="' . Html::encode($searchModel['ProjectName']) . '" type="text">'
			],
			'MileageStartDate',
			'MileageEndDate',
			'SumMiles',
			[
				'label' => 'Approved',
				'attribute' => 'MileageCardApprovedFlag',
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
					return ['mileageCardId' => $model["MileageCardID"], 'approved'=>$model["MileageCardApprovedFlag"], 'totalmileage'=>$model["SumMiles"]];
				}
			],
		],
	]); ?>

</div>
