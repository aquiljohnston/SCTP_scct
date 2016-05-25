<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use app\controllers\Equipment;

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
			<?php $userRole = Yii::$app->authManager->getRolesByUser(Yii::$app->session['userID']);?>
			<?php $role = current($userRole);?>
			<?php if((($role->name) == "Admin") || (($role->name) == "Engineer")){?>
				<?= Html::a('Create Equipment', ['create'], ['class' => 'btn btn-success']) ?>
			<?php } ?>
			
			<?= Html::button('Accept Equipment', [
				'class' => 'btn btn-primary multiple_approve_btn',
				'id' => 'multiple_approve_btn_id_equipment',
				'data' => [
                           'confirm' => 'Are you sure you want to accept this item?']
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
					'attribute' => 'Name',
					'filter' => '<input class="form-control" name="filtername" value="' . Html::encode($searchModel['Name']) . '" type="text">'
				],
				[
					'label' => 'Serial Number',
					'attribute' => 'Serial Number',
					'filter' => '<input class="form-control" name="filterserialnumber" value="' . Html::encode($searchModel['Serial Number']) . '" type="text">'
				],
				//'SC Number',
				'Details',
				[
					'label' => 'Type',
					'attribute' => 'Type',
					'filter' => '<input class="form-control" name="filtertype" value="' . Html::encode($searchModel['Type']) . '" type="text">'
				],
				[
					'label' => 'Client Name',
					'attribute' => 'Client Name',
					'filter' => '<input class="form-control" name="filterclientname" value="' . Html::encode($searchModel['Client Name']) . '" type="text">'
				],
				[
					'label' => 'Project Name',
					'attribute' => 'Project Name',
					'filter' => '<input class="form-control" name="filterprojectname" value="' . Html::encode($searchModel['Project Name']) . '" type="text">'
				],
				[
					'label' => 'Accepted Flag',
					'attribute' => 'Accepted Flag',
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
						return ['equipmentid' => $model["EquipmentID"], 'accepted' =>$model["Accepted Flag"] ];
					}
					/*'pageSummary' => true,
					'rowSelectedClass' => GridView::TYPE_SUCCESS,
					'contentOptions'=>['style'=>'width: 0.5%'],*/
				],
			],
		]); ?>

</div>
