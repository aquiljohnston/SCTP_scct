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
<!--script type="text/javascript">

	// // BULK DELETE
	// $('#multiple_approve_btn_id').on('click',function() {
		// var model = $(this).attr('model');
		// var pks = $('#w0').yiiGridView('getSelectedRows');
		// if (!pks || 0 !== pks.length) {
			// yii.confirm = function(message, ok, cancel) {
				// bootbox.confirm(message, function(result) {
					// alert("before calling ajax");
					// if (result) {
						// $.ajax({
						   // url: 'bulk-delete',
						   // data: {id: pks},
						   // success: function(data) {
								// $.pjax.reload({container:'#w0'});
						   // }
						// });
					// } else { !cancel || cancel(); }
				// });
			// }
		// } else {
			// bootbox.alert("Aucune ligne sÃ©lectionnÃ©e<br/>Veuillez sÃ©lectionner au moins un enregistrement!");
			// return false;
		// }
	// });
    /*$('#multiple_approve_btn').click(function() {
		var keys = $('#w1').yiiGridView('getSelectedRows'); // returns an array of pkeys, and #grid is your grid element id
		alert('Total price is ');
		$.post({
		   url: '/time-card/approvem', // your controller action
		   dataType: 'json',
		   data: {keylist: keys},
		   success: function(data) {
			  if (data.status === 'success') {
				  alert('Total price is ');
			  }
		   },
		});
	});*/
</script-->

<div class="timecard-index">

	<h3><?= Html::encode($this->title) ?></h3>

	<?php
	//$approveUrl = urldecode(Url::to(['time-card/approve', 'id' => $model["TimeCardID"]]));
	$approveUrl = "";
	?>
	<p id="multiple_time_card_approve_btn">
		<?= Html::button('Approve', [
			'class' => 'btn btn-primary multiple_approve_btn',
			'id' => 'multiple_approve_btn_id',
			/*'data' => [
                       'confirm' => 'Are you sure you want to approve this item?']*/
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
			'TimeCardApproved',

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
				/*'pageSummary' => true,
                'rowSelectedClass' => GridView::TYPE_SUCCESS,
                'contentOptions'=>['style'=>'width: 0.5%'],*/
			],
		],
	]); ?>

</div>
