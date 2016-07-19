<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use app\controllers\Equipment;
use app\controllers\BaseController;
/* @var $this yii\web\View */
/* @var $searchModel app\models\EquipmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Equipment Management';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="equipment-index">

	<h3 class="title"><?= Html::encode($this->title) ?></h3>
		<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

		<p>
			<?php if(BaseController::can('equipmentCreate')): ?>
				<?= Html::a('Create Equipment', ['create'], ['class' => 'btn btn-success']) ?>
			<?php endif; ?>
			
			<?= Html::button('Accept Equipment', [
				'class' => 'btn btn-primary multiple_approve_btn',
				'id' => 'multiple_approve_btn_id_equipment',
			])?>
		</p>

		<?= GridView::widget([
			'dataProvider' => $dataProvider,
			'filterModel' => $searchModel,
			'export' => false,
			'bootstrap' => false,
			'columns' => [
				['class' => 'kartik\grid\SerialColumn'],

				[
					'label' => 'Name',
					'attribute' => 'EquipmentName',
					'filter' => '<input class="form-control" name="filtername" value="' . Html::encode($searchModel['EquipmentName']) . '" type="text">'
				],
				[
					'label' => 'Serial Number',
					'attribute' => 'EquipmentSerialNumber',
					'filter' => '<input class="form-control" name="filterserialnumber" value="' . Html::encode($searchModel['EquipmentSerialNumber']) . '" type="text">'
				],
				//'SC Number',
				'EquipmentDetails',
				[
					'label' => 'Type',
					'attribute' => 'EquipmentType',
					'filter' => '<input class="form-control" name="filtertype" value="' . Html::encode($searchModel['EquipmentType']) . '" type="text">'
				],
				[
					'label' => 'Client Name',
					'attribute' => 'ClientName',
					'filter' => '<input class="form-control" name="filterclientname" value="' . Html::encode($searchModel['ClientName']) . '" type="text">'
				],
				[
					'label' => 'Project Name',
					'attribute' => 'ProjectName',
					'filter' => '<input class="form-control" name="filterprojectname" value="' . Html::encode($searchModel['ProjectName']) . '" type="text">'
				],
				[
					'label' => 'Accepted Flag',
					'attribute' => 'EquipmentAcceptedFlag',
					'filter' => $acceptedFilterInput
				],

				['class' => 'kartik\grid\ActionColumn',

					'template' => '{view} {update}',
					'urlCreator' => function ($action, $model, $key, $index) {
						if ($action === 'view') {
							$url ='index.php?r=equipment%2Fview&id='.$model["EquipmentID"];
							return $url;
						}
						if ($action === 'update') {
							$url ='index.php?r=equipment%2Fupdate&id='.$model["EquipmentID"];
							return $url;
						}
						if ($action === 'delete') {
							$url ='index.php?r=equipment%2Fdelete&id='.$model["EquipmentID"];
							return $url;
						}
					},
					'buttons' => [
						'delete' => function ($url, $model, $key) {
							$url ='/index.php?r=equipment%2Fdelete&id='.$model["EquipmentID"];
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
						return ['equipmentid' => $model["EquipmentID"], 'accepted' =>$model["EquipmentAcceptedFlag"] ];
					}
					/*'pageSummary' => true,
					'rowSelectedClass' => GridView::TYPE_SUCCESS,
					'contentOptions'=>['style'=>'width: 0.5%'],*/
				],
			],
		]); ?>

</div>
