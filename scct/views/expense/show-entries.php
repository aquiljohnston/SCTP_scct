<?php

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\grid\GridView;
use kartik\form\ActiveForm;
use yii\bootstrap\Modal;
use yii\widgets\Pjax;
use app\assets\ExpenseAsset;

//register assets
ExpenseAsset::register($this);

/* @var $this yii\web\View */
/* @var $model app\models\expenses */
?>

<div class="expense-card-entries">

	<div id="ExpenseEntriesForm">
		<input id="startDate" type="hidden" value=<?php echo $startDate; ?>>
		<input id="endDate" type="hidden" value=<?php echo $endDate; ?>>
		<input id="projectID" type="hidden" value=<?php echo $projectID; ?>>
		<input id="userID" type="hidden" value=<?php echo $userID; ?>>
	</div>
  
    <div class="lightBlueBar">
		<h3> <?= $projectName.' Week '.$startDate.' - '.$endDate.': '.$userName; ?></h3>

		<?php
			$isAccountant = Yii::$app->session['UserAppRoleType'] == 'Accountant';
		?>
		<?php Pjax::begin(['id' => 'expenseShowEntriesButtons', 'timeout' => false]) ?>
			<p>
				<?= Html::a('Back', ['index'], ['class' => 'btn btn-primary']) ?>
				<?php
				if($canApprove){
					echo Html::button('Approve', [
						'class' => 'btn btn-primary',
						'disabled' => true,
						'id' => 'approve_expense_btn_id',
					]);
				}
				?>
			</p>
		<?php Pjax::end() ?>
		<br>
    </div>
    <?php Pjax::begin(['id' => 'ShowExpenseEntriesView', 'timeout' => false]) ?>
		<?= \kartik\grid\GridView::widget([
			'id' => 'allExpenseEntries',
			'dataProvider' => $entries,
			'export' => false,
			'pjax' => true,
			'summary' => '',
			'showOnEmpty' => true,
            'emptyText' => 'No results found!',
			'columns' => [
				[
					'label' => 'User',
					'attribute' => 'UserName',
					'headerOptions' => ['class' => 'text-center'],
					'contentOptions' => ['class' => 'text-center'],
				],
				[
					'label' => 'Project',
					'attribute' => 'ProjectName',
					'headerOptions' => ['class' => 'text-center'],
					'contentOptions' => ['class' => 'text-center'],
				],
				[
					'label' => 'Date',
					'attribute' => 'CreatedDate',
					'headerOptions' => ['class' => 'text-center'],
					'contentOptions' => ['class' => 'text-center'],
				],
				[
					'label' => 'COA',
					'attribute' => 'ChargeAccount',
					'headerOptions' => ['class' => 'text-center'],
					'contentOptions' => ['class' => 'text-center'],
				],
				[
					'label' => 'Quantity',
					'attribute' => 'Quantity',
					'headerOptions' => ['class' => 'text-center'],
					'contentOptions' => ['class' => 'text-center'],
				],
				[
					'label' => 'Approved',
					'attribute' => 'IsApproved',
					'headerOptions' => ['class' => 'text-center'],
					'contentOptions' => ['class' => 'text-center'],
				],
				[
					'class' => 'kartik\grid\CheckboxColumn',
					'header' => Html::checkBox('selection_all', false, [
						'class' => 'select-on-check-all',
						'disabled' => ($isApproved)  ? true : false,
					]),
					'checkboxOptions' => function ($model, $key, $index, $column){
						// Disable if already approved
						$disabledBoolean = $model['IsApproved'] == 1;
						$result = [
							'expenseid' => $model['ID'],
							'approved' => $model['IsApproved']
						];
						if ($disabledBoolean) {
							$result['disabled'] = true;
						}
						return $result;
					}
				],
			]
		]);
		?>
		<?= Html::label('Total: '. $total,
			null, ['id' => 'entries_sum_expenses']) ?>
		<input type="hidden" value=<?php echo $isAccountant ?> id="isAccountant">
		<input type="hidden" value=<?php echo $isSubmitted ?> id="isSubmitted">
		<input type="hidden" value=<?php echo $isApproved ?> id="isApproved">
    <?php Pjax::end() ?>
</div>
