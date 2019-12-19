<?php

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\grid\GridView;
use kartik\form\ActiveForm;
use yii\bootstrap\Modal;
use yii\widgets\Pjax;
use app\assets\ExpenseAsset;

/* @var $this yii\web\View */
/* @var $model app\models\expenses */

//register assets
ExpenseAsset::register($this);

$this->title = $projectName.' Week '.$startDate.' - '.$endDate.': '.$userName;
$isAccountant = Yii::$app->session['UserAppRoleType'] == 'Accountant';
$isProjectManager = Yii::$app->session['UserAppRoleType'] == 'ProjectManager';
$column = [
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
		'value' => function($model, $key, $index, $column) {
			return $model['IsApproved'] == 0 ? 'No' : 'Yes';
		},
		'headerOptions' => ['class' => 'text-center'],
		'contentOptions' => ['class' => 'text-center'],
	],
];
if($canApprove || $canDeactivate){
	$selectAllDisabled = ($isSubmitted || ($isApproved && !($isAccountant || $isProjectManager)));	
	$column[] = [
		'class' => 'kartik\grid\CheckboxColumn',
		'header' => Html::checkBox('selection_all', false, [
			'class' => 'select-on-check-all',
			'disabled' => $selectAllDisabled,
		]),
		'checkboxOptions' => function ($model, $key, $index, $column){
			//refetch role variables in scope
			$isAccountant = Yii::$app->session['UserAppRoleType'] == 'Accountant';
			$isProjectManager = Yii::$app->session['UserAppRoleType'] == 'ProjectManager';
			//Disable if already approved
			$disabledBoolean = ($model['IsSubmitted'] == 1 || ($model['IsApproved'] == 1 && !($isAccountant || $isProjectManager)));
			$result = [
				'expenseid' => $model['ID'],
				'approved' => $model['IsApproved'],
				'disabled' => $disabledBoolean
			];
			return $result;
		}
	];
}
?>

<div class="expense-card-entries">
	<div id="ExpenseEntriesForm">
		<input id="startDate" type="hidden" value=<?php echo $startDate; ?>>
		<input id="endDate" type="hidden" value=<?php echo $endDate; ?>>
		<input id="projectID" type="hidden" value=<?php echo $projectID; ?>>
		<input id="userID" type="hidden" value=<?php echo $userID; ?>>
	</div>
  
    <div class="lightBlueBar">
		<h3> <?= Html::encode($this->title) ?></h3>
			<p>
				<?= Html::a('Back', ['index'], ['class' => 'btn btn-primary']) ?>
				<?php
				echo Html::button('Add Expense', [
					'class' => 'btn btn-primary add_btn',
					'id' => 'exp_entries_add_btn_id',
					'disabled' => false
				]);
				if($canApprove){
					echo Html::button('Approve', [
						'class' => 'btn btn-primary',
						'disabled' => true,
						'id' => 'approve_expense_btn_id',
					]);
				}
				echo Html::button('Deactivate', [
					'class' => 'btn btn-primary add_btn',
					'id' => 'exp_entries_deactivate_btn_id',
					'disabled' => true
				]);
				?>
			</p>
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
			'columns' => $column,
		]);
		?>
		<?= Html::label('Total: '. $total,
			null, ['id' => 'entries_sum_expenses']) ?>
		<input type="hidden" value=<?php echo $isAccountant ?> id="isAccountant">
		<input type="hidden" value=<?php echo $isSubmitted ?> id="isSubmitted">
		<input type="hidden" value=<?php echo $isApproved ?> id="isApproved">
    <?php Pjax::end() ?>
</div>

<?php
Pjax::begin(['id' => 'addExpense', 'timeout' => false]);
	Modal::begin([
		'header' => '<h4>ADD EXPENSE</h4>',
		'id' => 'addExpenseModal',
		'size' => 'modal-lg',
	]);
	echo "<div id='modalAddExpense'><span id='modalContentSpan'></span></div>";
	Modal::end();
Pjax::end();
?>