<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\widgets\Pjax;
use kartik\form\ActiveForm;
use kartik\daterange\DateRangePicker;
use kartik\grid\CheckboxColumn;
use app\assets\EmployeeApprovalAsset;
use app\controllers\BaseController;

//register assets
EmployeeApprovalAsset::register($this);

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Employee Approval';
//array to build days of the week for table headers
$dayOfWeek = [
	'Sun',
	'Mon',
	'Tue',
	'Wed',
	'Thurs',
	'Fri',
	'Sat',
];
			
//columns for user data
$userColumns = [
	[
		'label' => 'Row Labels',
		'attribute' => 'RowLabels',
		'headerOptions' => ['class' => 'text-center', 'style' => 'width: 5.71%; white-space: pre-wrap;'],
		'contentOptions' => ['class' => 'text-center', 'style' => 'width: 5.71%;'],
	]
];
//add dynamic date columns
$i = 0;
foreach($dateHeaders as $header){
	$userColumns[] = [
		'label' => $dayOfWeek[$i] . ' ' . $header,
		'attribute' => $header,
		'headerOptions' => ['class' => 'text-center', 'style' => 'width: 5.71%; white-space: pre-wrap;'],
		'contentOptions' => ['class' => 'text-center', 'style' => 'width: 5.71%;'],
	];
	$i++;
}
$userColumns = array_merge(
	$userColumns,
	[
		[
			'label' => 'Total',
			'attribute' => 'Total',
			'headerOptions' => ['class' => 'text-center', 'style' => 'width: 5.71%;'],
			'contentOptions' => ['class' => 'text-center', 'style' => 'width: 5.71%;'],
		],
		[
			'label' => 'Paid Time Off',
			'attribute' => 'PaidTimeOff',
			'headerOptions' => ['class' => 'text-center', 'style' => 'width: 5.71%; white-space: pre-wrap;'],
			'contentOptions' => ['class' => 'text-center', 'style' => 'width: 5.71%;'],
		],
		[
			'label' => 'Regular',
			'attribute' => 'Regular',
			'headerOptions' => ['class' => 'text-center', 'style' => 'width: 5.71%;'],
			'contentOptions' => ['class' => 'text-center', 'style' => 'width: 5.71%;'],
		],
		[
			'label' => 'Overtime',
			'attribute' => 'Overtime',
			'headerOptions' => ['class' => 'text-center', 'style' => 'width: 5.71%;'],
			'contentOptions' => ['class' => 'text-center', 'style' => 'width: 5.71%;'],
		],
		[
			'label' => 'Expense',
			'attribute' => 'Expense',
			'headerOptions' => ['class' => 'text-center', 'style' => 'width: 5.71%;'],
			'contentOptions' => ['class' => 'text-center', 'style' => 'width: 5.71%;'],
		],
		[
			'label' => 'Mileage To Approve',
			'attribute' => 'MileageToApprove',
			'headerOptions' => ['class' => 'text-center', 'style' => 'width: 5.71%; white-space: pre-wrap;'],
			'contentOptions' => ['class' => 'text-center', 'style' => 'width: 5.71%;'],
		],
		[
			'label' => 'Supervisor Approved',
			'attribute' => 'SupervisorApproved',
			'headerOptions' => ['class' => 'text-center', 'style' => 'width: 7.5%; white-space: pre-wrap;'],
			'contentOptions' => ['class' => 'text-center', 'style' => 'width: 7.5%;'],
		],
		[
			'label' => 'PM Submitted',
			'attribute' => 'PMSubmitted',
			'headerOptions' => ['class' => 'text-center', 'style' => 'width: 7.5%; white-space: pre-wrap;'],
			'contentOptions' => ['class' => 'text-center', 'style' => 'width: 7.5%;'],
		],
	]
);
//set up checkbox disabled status based on role type
if($isProjectManager){
	$userColumns = array_merge(
		$userColumns,
		[
			[
				'class' => 'kartik\grid\CheckboxColumn',
				'headerOptions' => ['class' => 'text-center', 'style' => 'width: 5%;'],
				'header' => Html::checkBox('selection_all', false, [
					'class' => 'select-on-check-all',
					//TODO supply bool for disabled check
					//'disabled' => ($unapprovedTimeCardVisible)  ? false : true,
					'disabled' => false,
				]),
				'checkboxOptions' => function ($model, $key, $index, $column){
					// Disable if already approved
					$isDisabled = $model['PMSubmitted'] === 'Yes' || $model['UserID'] === Null;
					$isHidden = $model['UserID'] === Null;
					$result = [
						'user' => $model['UserID']
					];
					if ($isDisabled) {
						$result['disabled'] = true;
					}
					if ($isHidden) {
						$result['hidden'] = true;
					}
					return $result;
				}
			],
		]
	);
}else{
	$userColumns = array_merge(
		$userColumns,
		[
			[
				'class' => 'kartik\grid\CheckboxColumn',
				'headerOptions' => ['class' => 'text-center', 'style' => 'width: 5%;'],
				'header' => Html::checkBox('selection_all', false, [
					'class' => 'select-on-check-all',
					//TODO supply bool for disabled check
					//'disabled' => ($unapprovedTimeCardVisible)  ? false : true,
					'disabled' => false,
				]),
				'checkboxOptions' => function ($model, $key, $index, $column){
					// Disable if already approved
					$isDisabled = $model['SupervisorApproved'] === 'Yes' || $model['UserID'] === Null;
					$isHidden = $model['UserID'] === Null;
					$result = [
						'user' => $model['UserID']
					];
					if ($isDisabled) {
						$result['disabled'] = true;
					}
					if ($isHidden) {
						$result['hidden'] = true;
					}
					return $result;
				}
			],
		]
);
}
//columns for project data
$projColumns = [
	[
		'label' => 'Projects',
		'attribute' => 'Projects',
		'headerOptions' => ['class' => 'text-center', 'style' => 'width: 7.14%;'],
		'contentOptions' => ['class' => 'text-center', 'style' => 'width: 7.14%;'],
	]
];
//add dynamic date columns
foreach($dateHeaders as $header){
	$projColumns[] = [
		'label' => $header,
		'attribute' => $header,
		'headerOptions' => ['class' => 'text-center', 'style' => 'width: 7.14%;'],
		'contentOptions' => ['class' => 'text-center', 'style' => 'width: 7.14%;'],
	];
}
$projColumns = array_merge(
	$projColumns,
	[
		[
			'label' => 'Total',
			'attribute' => 'Total',
			'headerOptions' => ['class' => 'text-center', 'style' => 'width: 7.14%;'],
			'contentOptions' => ['class' => 'text-center', 'style' => 'width: 7.14%;'],
		],
		[
			'label' => 'Paid Time Off',
			'attribute' => 'PaidTimeOff',
			'headerOptions' => ['class' => 'text-center', 'style' => 'width: 7.14%; white-space: pre-wrap;'],
			'contentOptions' => ['class' => 'text-center', 'style' => 'width: 7.14%;'],
		],
		[
			'label' => 'Regular',
			'attribute' => 'Regular',
			'headerOptions' => ['class' => 'text-center', 'style' => 'width: 7.14%;'],
			'contentOptions' => ['class' => 'text-center', 'style' => 'width: 7.14%;'],
		],
		[
			'label' => 'Overtime',
			'attribute' => 'Overtime',
			'headerOptions' => ['class' => 'text-center', 'style' => 'width: 7.14%;'],
			'contentOptions' => ['class' => 'text-center', 'style' => 'width: 7.14%;'],
		],
		[
			'label' => 'Expense',
			'attribute' => 'Expense',
			'headerOptions' => ['class' => 'text-center', 'style' => 'width: 7.14%;'],
			'contentOptions' => ['class' => 'text-center', 'style' => 'width: 7.14%;'],
		],
		[
			'label' => 'Mileage',
			'attribute' => 'Mileage',
			'headerOptions' => ['class' => 'text-center', 'style' => 'width: 7.14%;'],
			'contentOptions' => ['class' => 'text-center', 'style' => 'width: 7.14%;'],
		]
	]
);
//columns for status data
$statusColumns = [
	[
		'label' => 'Validations',
		'attribute' => 'Validations',
		'headerOptions' => ['class' => 'text-center'],
		'contentOptions' => ['class' => 'text-center'],
	],
	[
		'label' => 'Status',
		'attribute' => 'Status',
		'headerOptions' => ['class' => 'text-center'],
		'contentOptions' => ['class' => 'text-center'],
	],
];
?>

<div class="employee-approval-index index-div">
    <div class="lightBlueBar" style="height: 100px; padding: 10px;">
        <h3 class="title"><?= Html::encode($this->title) ?></h3>
        <div id="employee_approval_filter">
            <div id="employeeApprovalDropdownContainer">
				<?php $form = ActiveForm::begin([
                    'type' => ActiveForm::TYPE_HORIZONTAL,
                    'formConfig' => ['labelSpan' => 7, 'deviceSize' => ActiveForm::SIZE_SMALL],
                    'method' => 'get',
                    'options' => [
                        'id' => 'EmployeeApprovalForm',
                    ],
					'action' => Url::to(['employee-approval/index'])
                ]); ?>
				<div class="col-md-2 EmployeeApprovalDateRangeDropDown">
					<?= $form->field($model, 'dateRangeValue', ['labelSpan' => 3])->dropDownList($dateRangeDD, ['value' => $model->dateRangeValue, 'id' => 'employeeApprovalDateRange'])->label("Week"); ?>
				</div>
				<?php if($model->dateRangeValue == 'other'){ ?>
					<div id="employeeApprovalDatePickerContainer" style="float: left; width: auto; display: block;">
				<?php } else { ?>
					<div id="employeeApprovalDatePickerContainer" style="float: left; width: auto; display: none;">
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
                                "." var form = $('#employeeApprovalDropdownContainer').find('#employeeApprovalForm');
									if (form.find('.has-error').length){
										return false;
									}
									$('#loading').show();
									$.pjax.reload({
										type: 'GET',
										url: form.attr('action'),
										container: '#employeeApprovalGridview', // id to update content
										data: form.serialize(),
										timeout: 99999
									});
									$('#employeeApprovalGridview').off('pjax:success').on('pjax:success', function () {
										applyEmployeeApprovalListeners();
										employeeDetailToolTip();
										employeeApprovalApproveMultiple();
										$('#loading').hide();
										//TODO add button reloads if neccessary
									});
									$('#employeeApprovalGridview').off('pjax:error').on('pjax:error', function () {
										location.reload();
									});
                                    $('#employeeApprovalDatePickerContainer').css(\"display\", \"block\"); "."
                            }"],
                    ]); ?>
				</div>
				<?php Pjax::begin(['id' => 'employeeApprovalDropDownPjax', 'timeout' => false]) ?>
					<div class="col-md-2 employeeApprovalProjectFilterDD">
						<?=
							$form->field($model, 'projectID', ['labelSpan' => 3])->dropDownList($projectDropDown,
							['value' => $model->projectID, 'id'=>'employeeApprovalProjectFilterDD'])->label('Project'); 
						?>
					</div>
				<?php Pjax::end() ?>
                                            
				<?php 
                                
                               $approveButton = [
                                                    'class' => 'btn btn-primary multiple_approve_btn',
                                                    'id' => 'tc_multiple_approve_btn_id',
                                                    'disabled' => true
                                                ];
                               
					if($isProjectManager){
					
                                                echo Html::button('Approve', 
						[
							'class' => 'btn btn-primary multiple_approve_btn',
							'id' => 'ea_multiple_approve_btn_id',
							'disabled' => true
						]);
					}elseif($canApprove){
                                                        echo Html::button('Approve',$approveButton);
                                        }
				?>
				<?php ActiveForm::end(); ?>
			</div>
		</div>
    </div>
	
    <div id="employeeApprovalyGridViewContainer">
		<?php Pjax::begin(['id' => 'employeeApprovalGridview', 'timeout' => false]) ?>
		<!--user data table-->
        <div id="employeeApprovalUserGV" class="employeeApprovalUserForm">
            <?= GridView::widget([
                'id' => 'GridViewForEmployeeApprovalUser',
                'dataProvider' => $userDataProvider,
                'export' => false,
                'pjax' => true,
                'summary' => '',
                'showOnEmpty' => true,
                'emptyText' => 'No results found!',
                'columns' => $userColumns
            ]); ?>
        </div>
		<!--proj data table-->
		<div id="employeeApprovalProjGV" class="employeeApprovalProjForm">
            <?= GridView::widget([
                'id' => 'GridViewForEmployeeApprovalProj',
                'dataProvider' => $projDataProvider,
                'export' => false,
                'pjax' => true,
                'summary' => '',
                'showOnEmpty' => true,
                'emptyText' => 'No results found!',
                'columns' => $projColumns
            ]); ?>
        </div>
		<!--status data table-->
		<!--<div id="employeeApprovalStatusGV" class="employeeApprovalStatusForm">
            <?= GridView::widget([
                'id' => 'GridViewForEmployeeApprovalStatus',
                'dataProvider' => $statusDataProvider,
                'export' => false,
                'pjax' => true,
                'summary' => '',
                'showOnEmpty' => true,
                'emptyText' => 'No results found!',
                'columns' => $statusColumns
            ]); ?>
        </div>-->
		<?php Pjax::end() ?>
    </div>
</div>
<!--ctGrowl used for on screen notifications-->
 <!--<div id = "ctGrowlContainer"></div>
 <ul id = "ct-growl-clone">
	 <ul>
		 <li class = "title"></li>
		 <li class = "msg"></li>
		 <li class = "icon"><span class="close">X</span></li>
	 </ul>
 </ul>-->