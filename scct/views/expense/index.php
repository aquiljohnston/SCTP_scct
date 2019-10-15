<?php

use app\controllers\Expense;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\widgets\Pjax;
use kartik\form\ActiveForm;
use kartik\daterange\DateRangePicker;
use kartik\grid\GridView;
use kartik\grid\CheckboxColumn;
use app\assets\ExpenseAsset;

//register assets
ExpenseAsset::register($this);

$this->title = 'Expense';
$pageSize = ["50" => "50", "100" => "100", "200" => "200", "500" => "500", "750" => "750"];
if($isAccountant)
{
	$column = [
		[
			'class' => 'kartik\grid\ExpandRowColumn',
			'expandAllTitle' => 'Expand all',
			'collapseTitle' => 'Collapse all',
			'expandIcon' => '<span class="glyphicon glyphicon-expand"></span>',
			'value' => function ($model, $key, $index, $column) {
				return GridView::ROW_COLLAPSED;
			},
			'detailUrl' => Url::to(['expense/view-accountant-detail']),
			'detailAnimationDuration' => 'fast',
		],
		[
			'label' => 'Project Name',
			'attribute' => 'ProjectName',
			'headerOptions' => ['class' => 'text-center'],
			'contentOptions' => ['class' => 'text-center'],
		],
		[
			'label' => 'Start Date - End Date',
			'headerOptions' => ['class' => 'text-center'],
			'contentOptions' => ['class' => 'text-center'],
			'value' => function($model, $key, $index, $column) {
				return $model['StartDate'] . ' - ' . $model['EndDate'];
			},
		],
		[
			'label' => 'Submitted',
			'attribute' => 'IsSubmitted',
			'value' => function($model, $key, $index, $column) {
				return $model['IsSubmitted'] == 0 ? 'No' : 'Yes';
			},
			'headerOptions' => ['class' => 'text-center'],
			'contentOptions' => ['class' => 'text-center'],
		],
		[
			'class' => 'kartik\grid\CheckboxColumn',
			'header' => 'PM Reset',
			'headerOptions' => ['class' => 'text-center'],
			'checkboxOptions' => function ($model, $key, $index, $column){
				// Disable if already submitted
				$disabledBoolean = ($model['IsSubmitted'] != 0);
				$result = [];
				if ($disabledBoolean) {
					$result['disabled'] = true;
				}
				return $result;
			}
		],
		//may not need this field as it is the table key
		[
			'label' => 'Project ID',
			'attribute' => 'ProjectID',
			'visible' => false,
			'headerOptions' => ['class' => 'text-center'],
			'contentOptions' => ['class' => 'text-center'],
		],
	];
}
else
{
	$column = [
		[
			'label' => 'User',
			'attribute' => 'UserName',
			'headerOptions' => ['class' => 'text-center'],
			'contentOptions' => ['class' => 'text-center'],
		],
		[
			'label' => 'Project Name',
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
			'label' => 'Quantity',
			'attribute' => 'Quantity',
			'headerOptions' => ['class' => 'text-center'],
			'contentOptions' => ['class' => 'text-center']
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
		[
			'class' => 'kartik\grid\CheckboxColumn',
			'header' => Html::checkBox('selection_all', false, [
				'class' => 'select-on-check-all',
				'disabled' => ($unapprovedExpenseVisible)  ? false : true,
			]),
			'checkboxOptions' => function ($model, $key, $index, $column){
				// Disable if already approved or SumHours is 0
				$disabledBoolean = $model['IsApproved'] == 1;
				$result = [
					'expenseid' => $model['ID'],
					'approved' => $model['IsApproved'],
					'quantity' => $model['Quantity']
				];
				if ($disabledBoolean) {
					$result['disabled'] = true;
				}
				return $result;
			}
		],
	];
}
?>

<div class="expense-index">
    <div class="lightBlueBar" style="height: 110px; padding: 10px;">
        <h3 class="title"><?= Html::encode($this->title) ?></h3>
        <div id="expense_filter">
            <div id="expenseDropdownContainer">
                <?php $form = ActiveForm::begin([
                    'type' => ActiveForm::TYPE_HORIZONTAL,
                    'formConfig' => ['labelSpan' => 7, 'deviceSize' => ActiveForm::SIZE_SMALL],
                    'method' => 'get',
                    'options' => [
                        'id' => 'ExpenseForm',
                    ],
					'action' => Url::to(['expense/index'])
                ]); ?>
                <div class="row">
                    <div style="float: right;margin-top: -2%;width: 21%;">
                        <?= $form->field($model, 'pageSize', ['labelSpan' => 6])->dropDownList($pageSize, ['value' => $model->pageSize, 'id' => 'expensePageSize'])->label("Records Per Page", [
                            'class' => 'ExpenseRecordsPerPage'
                        ]); ?>
                    </div>
                </div>
				<?php Pjax::begin(['id' => 'expenseButtons', 'timeout' => false]) ?>
					<div class="row">
						<div id="multiple_expense_approve_btn">
							<?php 
								$approveButton = [
									'class' => 'btn btn-primary multiple_approve_btn',
									'id' => 'exp_multiple_approve_btn_id',
									'disabled' => true
								];
								if($isAccountant) {
									echo Html::button('Submit',
									[
										'class' => $accountingSubmitReady ? 'btn btn-primary multiple_submit_btn enable-btn' : 'btn btn-primary multiple_submit_btn off-btn',
										'id' => 'expense_submit_btn_id',
										'submitted' => $projectSubmitted ? 'true' : 'false'
									]);
									echo Html::button('PM Reset',
									[
										'class' => 'btn btn-primary pm_reset_btn',
										'id' => 'pm_expense_reset',
										'disabled' => true
									]);
								}elseif($isProjectManager && $canApprove){
									if($pmSubmitReady || $unapprovedExpenseInProject){
										echo Html::button('Approve', $approveButton);
									}else{									
										echo Html::button('Request Reset',
										[
											'class' => 'btn btn-primary exp_pm_reset_request_btn',
											'id' => 'exp_pm_reset_request_btn_id',
											'disabled' => false
										]);
									}
								}
							?>
						</div>
					</div>
                <?php Pjax::end() ?>
                <div class="col-md-3 ExpenseSearch">
                    <?= $form->field($model, 'filter', ['labelSpan' => 3])->textInput(['value' => $model->filter, 'placeholder' => 'Example: username, project', 'id' => 'expenseFilter'])->label("Search"); ?>
                </div>
                <?php echo Html::img('@web/logo/filter_clear_black.png', ['id' => 'expenseSearchCleanFilterButton']) ?>
                <div class="col-md-2 ExpenseDateRangeDropDown">
                    <?= $form->field($model, 'dateRangeValue', ['labelSpan' => 3])->dropDownList($dateRangeDD, ['value' => $model->dateRangeValue, 'id' => 'expenseDateRange'])->label("Week"); ?>
                </div> <!--show filter-->
				<?php Pjax::begin(['id' => 'expenseDropDownPjax', 'timeout' => false]) ?>
					<?php if($showFilter){ ?>
						<div class="col-md-2 expenseProjectFilterDD">
							<?=
								$form->field($model, 'projectID', ['labelSpan' => 3])->dropDownList($projectDropDown,
								['value' => $model->projectID, 'id'=>'expenseProjectFilterDD'])->label('Project'); 
							?>
						</div>
					<?php }else{
						echo "<input type='hidden' value=$model->projectID id='expenseProjectFilterDD'>";
					} ?>
					<?php if(!$isAccountant){ ?>
						<div class="col-md-2 expenseEmployeeFilterDD">
							<?=
								$form->field($model, 'employeeID', ['labelSpan' => 3])->dropDownList($employeeDropDown,
								['value' => $model->employeeID, 'id'=>'expenseEmployeeFilterDD'])->label('Employee'); 
							?>
						</div>
					<?php } ?>
					<?php echo Html::img('@web/logo/filter_clear_black.png', ['id' => 'expenseClearDropdownFilterButton']) ?>
				<?php Pjax::end() ?>
					<?php if($model->dateRangeValue == 'other'){ ?>
						<div id="expenseDatePickerContainer" style="float: left; width: auto; display: block;">
					<?php } else { ?>
						<div id="expenseDatePickerContainer" style="float: left; width: auto; display: none;">
					<?php } ?>
                    <?= $form->field($model, 'dateRangePicker', [
                        'showLabels' => false
                    ])->widget(DateRangePicker::classname(), [
						'name'=>'date_range_3',
						'hideInput'=>true,
						'initRangeExpr' => true,
                        'pluginOptions' => [
							'opens' => 'left',
							'ranges' => [
								"Last 30 Days" => ["moment().startOf('day').subtract(29, 'days')", "moment()"],
								"This Month" => ["moment().startOf('month')", "moment().endOf('month')"],
								"Last Month" => ["moment().subtract(1, 'month').startOf('month')", "moment().subtract(1, 'month').endOf('month')"],
							]
                        ],
                        'pluginEvents' => [
                            "apply.daterangepicker" => "function() {
                                "." var form = $('#expenseDropdownContainer').find('#ExpenseForm');
									if (form.find('.has-error').length){
										return false;
									}
									$('#loading').show();
									$.pjax.reload({
										type: 'GET',
										url: form.attr('action'),
										container: '#expenseGridview', // id to update content
										data: form.serialize(),
										timeout: 99999
									});
									$('#expenseGridview').off('pjax:success').on('pjax:success', function () {
										$.pjax.reload({
											container: '#expenseButtons',
											timeout:false
										});
										$('#expenseButtons').off('pjax:success').on('pjax:success', function () {
											expenseApproveMultiple();
											expenseAccountantSubmit();
											expensePMReset();
											expenseRequestPMReset();
											$('#loading').hide();
										});
										$('#expenseButtons').off('pjax:error').on('pjax:error', function () {
											location.reload();
										});
									});
									$('#expenseGridview').off('pjax:error').on('pjax:error', function () {
										location.reload();
									});
                                    $('#expenseDatePickerContainer').css(\"display\", \"block\"); "."
                            }"],
                    ]); ?>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
	
    <div id="expenseGridViewContainer">
        <div id="expenseGV" class="expenseForm">
            <?php Pjax::begin(['id' => 'expenseGridview', 'timeout' => false]) ?>
            <?= GridView::widget([
                'id' => 'GridViewForExpense',
                'dataProvider' => $dataProvider,
                'export' => false,
                'pjax' => true,
                'summary' => '',
                'showOnEmpty' => true,
                'emptyText' => 'No results found!',
                'columns' => $column
            ]); ?>
            <div id="EXPPagination">
                <?php
                // display pagination
                echo LinkPager::widget([
                    'pagination' => $pages,
                ]);
                ?>
            </div>
            <div class="GridviewTotalNumber">
                <?php echo "Showing " . ($pages->offset + 1) . "  to " . ($pages->offset + $pages->getPageSize()) . " of " . $pages->totalCount . " entries"; ?>
            </div>
            <?php Pjax::end() ?>
        </div>
    </div>
</div>
<!--ctGrowl-->
 <div id = "ctGrowlContainer"></div>
 <ul id = "ct-growl-clone">
	 <ul>
		 <li class = "title"></li>
		 <li class = "msg"></li>
		 <li class = "icon"><span class="close">X</span></li>
	 </ul>
 </ul>