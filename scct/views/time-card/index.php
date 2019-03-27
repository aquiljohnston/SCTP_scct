<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\widgets\Pjax;
use app\controllers\TimeCard;
use kartik\form\ActiveForm;
use kartik\daterange\DateRangePicker;
use kartik\grid\CheckboxColumn;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Time Cards';
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
			'detailUrl' => Url::to(['time-card/view-accountant-detail']),
			'detailAnimationDuration' => 'fast',
		],
		[
			'label' => 'Project Name',
			'attribute' => 'ProjectName',
			'headerOptions' => ['class' => 'text-center'],
			'contentOptions' => ['class' => 'text-center'],
		],
		[
			'label' => 'Project Manager',
			'attribute' => 'ProjectManager',
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
			'label' => 'Submitted/Total',
			'headerOptions' => ['class' => 'text-center'],
			'contentOptions' => ['class' => 'text-center'],
			'value' => function($model, $key, $index, $column) {
				return $model['Approved Time Cards'] . '/' . $model['Total Time Cards'];
			},
		],
		[
			'label' => 'Submitted By',
			'attribute' => 'ApprovedBy',
			'headerOptions' => ['class' => 'text-center'],
			'contentOptions' => ['class' => 'text-center'],
		],
		[
			'label' => 'Oasis Submitted',
			'attribute' => 'OasisSubmitted',
			'headerOptions' => ['class' => 'text-center'],
			'contentOptions' => ['class' => 'text-center'],
		],
		[
			'label' => 'MSD Submitted',
			'attribute' => 'MSDynamicsSubmitted',
			'headerOptions' => ['class' => 'text-center'],
			'contentOptions' => ['class' => 'text-center'],
		],
		[
			'label' => 'ADP Submitted',
			'attribute' => 'ADPSubmitted',
			'headerOptions' => ['class' => 'text-center'],
			'contentOptions' => ['class' => 'text-center'],
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
			'label' => 'User Full Name',
			'attribute' => 'UserFullName',
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
			'label' => 'Start Date - End Date',
			'attribute' => 'TimeCardDates',
			'headerOptions' => ['class' => 'text-center'],
			'contentOptions' => ['class' => 'text-center'],
		],
		[
			'label' => 'Sum Hours',
			'attribute' => 'SumHours',
			'headerOptions' => ['class' => 'text-center'],
			'contentOptions' => ['class' => 'text-center']
		],
		[
			'label' => 'Supervisor Approved',
			'attribute' => 'TimeCardApprovedFlag',
			'value' => function($model, $key, $index, $column) {
				return $model['TimeCardApprovedFlag'] == 0 ? 'No' : 'Yes';
			},
			'headerOptions' => ['class' => 'text-center'],
			'contentOptions' => ['class' => 'text-center'],
		],
		[
			'label' => 'PM Approved',
			'attribute' => 'TimeCardPMApprovedFlag',
			'value' => function($model, $key, $index, $column) {
				return $model['TimeCardPMApprovedFlag'] == 0 ? 'No' : 'Yes';
			},
			'headerOptions' => ['class' => 'text-center'],
			'contentOptions' => ['class' => 'text-center'],
		],
		['class' => 'kartik\grid\ActionColumn',
			'template' => '{view}', // does not include delete
			'urlCreator' => function ($action, $model, $key, $index) {
				if ($action === 'view') {
					$url = '/time-card/show-entries?id=' . $model["TimeCardID"].'&projectName='.$model['ProjectName']
					.'&fName='.$model['UserFirstName']
					.'&lName='.$model['UserLastName']
					.'&timeCardProjectID='.$model['TimeCardProjectID'];
					return $url;
				}
			},
		],
		[
			'class' => 'kartik\grid\CheckboxColumn',
			'header' => Html::checkBox('selection_all', false, [
				'class' => 'select-on-check-all',
				'disabled' => ($unapprovedTimeCardExist)  ? false : true,
			]),
			'checkboxOptions' => function ($model, $key, $index, $column){
				// Disable if already approved or SumHours is 0
				$disabledBoolean = $model["TimeCardApprovedFlag"] == 1;
				$result = [
					'timecardid' => $model["TimeCardID"],
					'approved' => $model["TimeCardApprovedFlag"],
					'totalworkhours' => $model["SumHours"]
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

<div class="timecard-index">
    <div class="lightBlueBar" style="height: 100px;">
        <h3 class="title"><?= Html::encode($this->title) ?></h3>
        <div id="timecard_filter">
            <div id="timeCardDropdownContainer">
                <?php $form = ActiveForm::begin([
                    'type' => ActiveForm::TYPE_HORIZONTAL,
                    'formConfig' => ['labelSpan' => 7, 'deviceSize' => ActiveForm::SIZE_SMALL],
                    'method' => 'get',
                    'options' => [
                        'id' => 'TimeCardForm',
                    ],
					'action' => Url::to(['time-card/index'])
                ]); ?>
                <div class="row">
                    <div style="float: right;margin-top: -2%;width: 21%;">
                        <?= $form->field($model, 'pageSize', ['labelSpan' => 6])->dropDownList($pageSize, ['value' => $model->pageSize, 'id' => 'timeCardPageSize'])->label("Records Per Page", [
                            'class' => 'TimeCardRecordsPerPage'
                        ]); ?>
                        <input id="timeCardPageNumber" type="hidden" name="timeCardPageNumber" value="1"/>
                    </div>
                </div>
				<?php Pjax::begin(['id' => 'timeCardSubmitApproveButtons', 'timeout' => false]) ?>
					<div class="row">
						<div id="multiple_time_card_approve_btn">
							<?php 
								$approveButton = [
									'class' => 'btn btn-primary multiple_approve_btn',
									'id' => 'tc_multiple_approve_btn_id',
									'disabled' => true
								];
								if($isAccountant) {
									echo Html::button('Submit',
									[
										'class' => $accountingSubmitReady ? 'btn btn-primary multiple_submit_btn enable-btn' : 'btn btn-primary multiple_submit_btn off-btn',
										'id' => 'time_card_submit_btn_id',
										'submitted' => $projectSubmitted ? 'true' : 'false'
									]);
								} elseif($isProjectManager){
									echo Html::button('Submit',
									[
										'class' => $pmSubmitReady ? 'btn btn-primary multiple_submit_btn enable-btn' : 'btn btn-primary multiple_submit_btn off-btn disabled',
										'id' => 'time_card_pm_submit_btn_id',
										'submitted' => $projectSubmitted ? 'true' : 'false'
									]);
									echo Html::button('Approve', $approveButton);
								} else
									echo Html::button('Approve',$approveButton);
							?>
						</div>
					</div>
                <?php Pjax::end() ?>
                <div class="col-md-3 TimeCardSearch">
                    <?= $form->field($model, 'filter', ['labelSpan' => 3])->textInput(['value' => $model->filter, 'placeholder' => 'Example: username, project', 'id' => 'timeCardFilter'])->label("Search"); ?>
                </div>
                <?php echo Html::img('@web/logo/filter_clear_black.png', ['id' => 'timeCardSearchCleanFilterButton']) ?>
                <div class="col-md-2 TimeCardDateRangeDropDown">
                    <?= $form->field($model, 'dateRangeValue', ['labelSpan' => 3])->dropDownList($dateRangeDD, ['value' => $model->dateRangeValue, 'id' => 'timeCardDateRange'])->label("Week"); ?>
                </div> <!--show filter-->
				<?php Pjax::begin(['id' => 'timeCardDropDownPjax', 'timeout' => false]) ?>
					<?php if($showFilter){ ?>
						<div class="col-md-2 timeCardProjectFilterDD">
							<?=
								$form->field($model, 'projectID', ['labelSpan' => 3])->dropDownList($projectDropDown,
								['value' => $model->projectID, 'id'=>'timeCardProjectFilterDD'])->label('Project'); 
							?>
						</div>
					<?php }else{
						echo "<input type='hidden' value=$model->projectID id='timeCardProjectFilterDD'>";
					} ?>
					<?php if(!$isAccountant){ ?>
						<div class="col-md-2 timeCardEmployeeFilterDD">
							<?=
								$form->field($model, 'employeeID', ['labelSpan' => 3])->dropDownList($employeeDropDown,
								['value' => $model->employeeID, 'id'=>'timeCardEmployeeFilterDD'])->label('Employee'); 
							?>
						</div>
					<?php } ?>
					<?php echo Html::img('@web/logo/filter_clear_black.png', ['id' => 'timeCardClearDropdownFilterButton']) ?>
				<?php Pjax::end() ?>
					<?php if($model->dateRangeValue == 'other'){ ?>
						<div id="timeCardDatePickerContainer" style="float: left; width: auto; display: block;">
					<?php } else { ?>
						<div id="timeCardDatePickerContainer" style="float: left; width: auto; display: none;">
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
                                "." var form = $('#timeCardDropdownContainer').find('#TimeCardForm');
									if (form.find('.has-error').length){
										return false;
									}
									$('#loading').show();
									$.pjax.reload({
										type: 'GET',
										url: form.attr('action'),
										container: '#timeCardGridview', // id to update content
										data: form.serialize(),
										timeout: 99999
									});
									$('#timeCardGridview').off('pjax:success').on('pjax:success', function () {
										$.pjax.reload({
											container: '#timeCardSubmitApproveButtons',
											timeout:false
										});
										$('#timeCardSubmitApproveButtons').off('pjax:success').on('pjax:success', function () {
											applyTimeCardOnClickListeners();
											timeCardAccountantSubmit();
											timeCardPmSubmit();
											$('#loading').hide();
										});
										$('#timeCardSubmitApproveButtons').off('pjax:error').on('pjax:error', function () {
											location.reload();
										});
									});
									$('#timeCardGridview').off('pjax:error').on('pjax:error', function () {
										location.reload();
									});
                                    $('#timeCardDatePickerContainer').css(\"display\", \"block\"); "."
                            }"],
                    ]); ?>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
	
    <div id="timeCardGridViewContainer">
        <div id="timeCardGV" class="timeCardForm">
            <?php Pjax::begin(['id' => 'timeCardGridview', 'timeout' => false]) ?>
            <?= GridView::widget([
                'id' => 'GridViewForTimeCard',
                'dataProvider' => $dataProvider,
                'export' => false,
                'bootstrap' => false,
                'pjax' => true,
                'summary' => '',
                'showOnEmpty' => true,
                'emptyText' => 'No results found!',
                'columns' => $column
            ]); ?>
            <div id="TCPagination">
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