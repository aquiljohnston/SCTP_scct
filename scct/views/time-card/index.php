<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;
use app\controllers\TimeCard;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Time Cards';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="timecard-index">

	<h3 class="title"><?= Html::encode($this->title) ?></h3>

	<p id="multiple_time_card_approve_btn">
		<?= Html::button('Approve',
		[
			'class' => 'btn btn-primary multiple_approve_btn',
			'id' => 'multiple_approve_btn_id',
			'data' => [
                       /*'confirm' => 'Are you sure you want to approve this item?'*/
					  ]
		])?>
	</p>
	<?php
	if($week=="prior") {
		$priorSelected = "selected";
		$currentSelected = "";
	} else {
		$priorSelected = "";
		$currentSelected = "selected";
	}
	?>
	<form method="GET">
		<select name="week" onchange="this.form.submit()">
			<option value="prior" <?= $priorSelected ?>>Prior Week</option>
			<option value="current" <?= $currentSelected ?>>Current Week</option>
		</select>
		<input type="hidden" name="r" value="time-card/index" />
	</form>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'export' => false,
		'filterModel' => $searchModel,
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
			'TimeCardStartDate',
			'TimeCardEndDate',
			'SumHours',
			[
				'label' => 'Approved',
				'attribute' => 'TimeCardApprovedFlag',
				'filter' => $approvedInput
			],
			['class' => 'kartik\grid\ActionColumn',
				'template' => '{view}',
				'urlCreator' => function ($action, $model, $key, $index) {
					if ($action === 'view') {
						$url ='index.php?r=time-card%2Fview&id='.$model["TimeCardID"];
						return $url;
					}
				},
				'buttons' => [
					'delete' => function ($url, $model, $key) {
						$url ='/index.php?r=time-card%2Fdelete&id='.$model["TimeCardID"];
						$options = [
							'title' => Yii::t('yii', 'Delete'),
							'aria-label' => Yii::t('yii', 'Delete'),
							'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
							'data-method' => 'Delete',
							'data-pjax' => '0',
						];
						return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, $options);
					},
				],
			],
			[
				'class' => 'kartik\grid\CheckboxColumn',
				'checkboxOptions' => function ($model, $key, $index, $column) {
					return ['timecardid' => $model["TimeCardID"], 'approved' =>$model["TimeCardApprovedFlag"], 'totalworkhours' => $model["SumHours"] ];
				}
				/*'pageSummary' => true,
                'rowSelectedClass' => GridView::TYPE_SUCCESS,
                'contentOptions'=>['style'=>'width: 0.5%'],*/
			],
		],
	]); ?>

</div>
