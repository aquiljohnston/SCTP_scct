<?php

use yii\helpers\Html;
use yii\grid\GridView;
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

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],

			'UserFirstName',
			'UserLastName',
			'TimeCardStartDate',
			'TimeCardEndDate',
			'TimeCardHoursWorked',
			'TimeCardApprovedFlag',

			['class' => 'yii\grid\ActionColumn',
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
				'class' => 'yii\grid\CheckboxColumn',
				'checkboxOptions' => function ($model, $key, $index, $column) {
					return ['timecardid' => $model["TimeCardID"], 'approved' =>$model["TimeCardApprovedFlag"], 'totalworkhours' => $model["TimeCardHoursWorked"] ];
				}
				/*'pageSummary' => true,
                'rowSelectedClass' => GridView::TYPE_SUCCESS,
                'contentOptions'=>['style'=>'width: 0.5%'],*/
			],
		],
	]); ?>

</div>
