<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\widgets\Pjax;
use kartik\form\ActiveForm;
use kartik\daterange\DateRangePicker;
use kartik\grid\CheckboxColumn;
use app\assets\ReportSummaryAsset;
use app\controllers\BaseController;

//register assets
ReportSummaryAsset::register($this);

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Report Summary';
//columns for user data
$userColumns = [
	[
		'label' => 'Row Labels',
		'attribute' => 'RowLabels',
		'headerOptions' => ['class' => 'text-center'],
		'contentOptions' => ['class' => 'text-center'],
	]
];
//add dynamic date columns
foreach($dateHeaders as $header){
	$userColumns[] = [
		'label' => $header,
		'attribute' => $header,
		'headerOptions' => ['class' => 'text-center'],
		'contentOptions' => ['class' => 'text-center'],
	];
}
$userColumns = array_merge(
	$userColumns,
	[
		[
			'label' => 'Total',
			'attribute' => 'Total',
			'headerOptions' => ['class' => 'text-center'],
			'contentOptions' => ['class' => 'text-center'],
		],
		[
			'label' => 'Paid Time Off',
			'attribute' => 'PaidTimeOff',
			'headerOptions' => ['class' => 'text-center'],
			'contentOptions' => ['class' => 'text-center'],
		],
		[
			'label' => 'Regular',
			'attribute' => 'Regular',
			'headerOptions' => ['class' => 'text-center'],
			'contentOptions' => ['class' => 'text-center'],
		],
		[
			'label' => 'Overtime',
			'attribute' => 'Overtime',
			'headerOptions' => ['class' => 'text-center'],
			'contentOptions' => ['class' => 'text-center'],
		],
		[
			'label' => 'Mileage To Approve',
			'attribute' => 'MileageToApprove',
			'headerOptions' => ['class' => 'text-center'],
			'contentOptions' => ['class' => 'text-center'],
		],
		[
			'label' => 'Supervisor Approved',
			'attribute' => 'SupervisorApproved',
			'headerOptions' => ['class' => 'text-center'],
			'contentOptions' => ['class' => 'text-center'],
		],
		[
			'label' => 'PM Submitted',
			'attribute' => 'PMSubmitted',
			'headerOptions' => ['class' => 'text-center'],
			'contentOptions' => ['class' => 'text-center'],
		],
		[
			'class' => 'kartik\grid\CheckboxColumn',
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
					//TODO look to provide userid
					'user' => $model['UserID'],
					'approved' => $model['SupervisorApproved']
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
//columns for project data
$projColumns = [
	[
		'label' => 'Projects',
		'attribute' => 'Projects',
		'headerOptions' => ['class' => 'text-center'],
		'contentOptions' => ['class' => 'text-center'],
	]
];
//add dynamic date columns
foreach($dateHeaders as $header){
	$projColumns[] = [
		'label' => $header,
		'attribute' => $header,
		'headerOptions' => ['class' => 'text-center'],
		'contentOptions' => ['class' => 'text-center'],
	];
}
$projColumns = array_merge(
	$projColumns,
	[
		[
			'label' => 'Total',
			'attribute' => 'Total',
			'headerOptions' => ['class' => 'text-center'],
			'contentOptions' => ['class' => 'text-center'],
		],
		[
			'label' => 'Paid Time Off',
			'attribute' => 'PaidTimeOff',
			'headerOptions' => ['class' => 'text-center'],
			'contentOptions' => ['class' => 'text-center'],
		],
		[
			'label' => 'Regular',
			'attribute' => 'Regular',
			'headerOptions' => ['class' => 'text-center'],
			'contentOptions' => ['class' => 'text-center'],
		],
		[
			'label' => 'Overtime',
			'attribute' => 'Overtime',
			'headerOptions' => ['class' => 'text-center'],
			'contentOptions' => ['class' => 'text-center'],
		],
		[
			'label' => 'Mileage',
			'attribute' => 'Mileage',
			'headerOptions' => ['class' => 'text-center'],
			'contentOptions' => ['class' => 'text-center'],
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

<div class="report-summary-index index-div">
    <div class="lightBlueBar" style="height: 140px; padding: 10px;">
        <h3 class="title"><?= Html::encode($this->title) ?></h3>
        <div id="report_summary_filter">
            <div id="reportSummaryDropdownContainer">
				<?php $form = ActiveForm::begin([
                    'type' => ActiveForm::TYPE_HORIZONTAL,
                    'formConfig' => ['labelSpan' => 7, 'deviceSize' => ActiveForm::SIZE_SMALL],
                    'method' => 'get',
                    'options' => [
                        'id' => 'ReportSummaryForm',
                    ],
					'action' => Url::to(['report-summary/index'])
                ]); ?>
				<div class="col-md-2 ReportSummaryDateRangeDropDown">
					<?= $form->field($model, 'dateRangeValue', ['labelSpan' => 3])->dropDownList($dateRangeDD, ['value' => $model->dateRangeValue, 'id' => 'reportSummaryDateRange'])->label("Week"); ?>
				</div>
				<?php if($model->dateRangeValue == 'other'){ ?>
					<div id="reportSummaryDatePickerContainer" style="float: left; width: auto; display: block;">
				<?php } else { ?>
					<div id="reportSummaryDatePickerContainer" style="float: left; width: auto; display: none;">
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
                                "." var form = $('#reportSummaryDropdownContainer').find('#ReportSummaryForm');
									if (form.find('.has-error').length){
										return false;
									}
									$('#loading').show();
									$.pjax.reload({
										type: 'GET',
										url: form.attr('action'),
										container: '#reportSummaryGridview', // id to update content
										data: form.serialize(),
										timeout: 99999
									});
									$('#reportSummaryGridview').off('pjax:success').on('pjax:success', function () {
										applyReportSummaryListeners();
										validateTaskToolTip();
										reportSummaryApproveMultiple();
										$('#loading').hide();
										//TODO add button reloads if neccessary
									});
									$('#reportSummaryGridview').off('pjax:error').on('pjax:error', function () {
										location.reload();
									});
                                    $('#reportSummaryDatePickerContainer').css(\"display\", \"block\"); "."
                            }"],
                    ]); ?>
				</div>
				<?php 
					echo Html::button('Approve', 
					[
						'class' => 'btn btn-primary multiple_approve_btn',
						'id' => 'rs_multiple_approve_btn_id',
						'disabled' => true
					]);
				?>
				<?php ActiveForm::end(); ?>
			</div>
		</div>
    </div>
	
    <div id="reportSummaryGridViewContainer">
		<?php Pjax::begin(['id' => 'reportSummaryGridview', 'timeout' => false]) ?>
		<!--user data table-->
        <div id="reportSummaryUserGV" class="reportSummaryUserForm">
            <?= GridView::widget([
                'id' => 'GridViewForReportSummaryUser',
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
		<div id="reportSummaryProjGV" class="reportSummaryProjForm">
            <?= GridView::widget([
                'id' => 'GridViewForReportSummaryProj',
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
		<!--<div id="reportSummaryStatusGV" class="reportSummaryStatusForm">
            <?= GridView::widget([
                'id' => 'GridViewForReportSummaryStatus',
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