<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\widgets\Pjax;
use app\controllers\MileageCard;
use kartik\form\ActiveForm;
use kartik\daterange\DateRangePicker;
use kartik\grid\CheckboxColumn;
use app\assets\MileageCardAsset;

//register assets
MileageCardAsset::register($this);

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Mileage Cards';
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
			'detailUrl' => Url::to(['mileage-card/view-accountant-detail']),
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
				return $model['Approved Mileage Cards'] . '/' . $model['Total Mileage Cards'];
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
			'class' => 'kartik\grid\CheckboxColumn',
			'header' => 'PM Reset',
			'headerOptions' => ['class' => 'text-center'],
			'checkboxOptions' => function ($model, $key, $index, $column){
				// Disable if already approved or SumHours is 0
				$disabledBoolean = ($model['Approved Mileage Cards'] == 0 || $model['OasisSubmitted'] != 'No' || $model['MSDynamicsSubmitted'] != 'No');
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
}else{
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
			'attribute' => 'MileageCardDates',
			'headerOptions' => ['class' => 'text-center'],
			'contentOptions' => ['class' => 'text-center'],
		],
		[
			'label' => 'Total Miles',
			'attribute' => 'SumMiles',
			'headerOptions' => ['class' => 'text-center'],
			'contentOptions' => ['class' => 'text-center'],
		],
		[
			'label' => 'Supervisor Approved',
			'attribute' => 'MileageCardApprovedFlag',
			'value' => function($model, $key, $index, $column) {
				return $model['MileageCardApprovedFlag'] == 0 ? 'No' : 'Yes';
			},
			'headerOptions' => ['class' => 'text-center'],
			'contentOptions' => ['class' => 'text-center'],
		],
		[
			'label' => 'PM Approved',
			'attribute' => 'MileageCardPMApprovedFlag',
			'value' => function($model, $key, $index, $column) {
				return $model['MileageCardPMApprovedFlag'] == 0 ? 'No' : 'Yes';
			},
			'headerOptions' => ['class' => 'text-center'],
			'contentOptions' => ['class' => 'text-center'],
		],
		['class' => 'kartik\grid\ActionColumn',
			'template' => '{view}', // does not include delete
			'urlCreator' => function ($action, $model, $key, $index) {
				if ($action === 'view') {
					$url = '/mileage-card/show-entries?id=' . $model["MileageCardID"].'&projectName='.$model['ProjectName']
					.'&fName='.$model['UserFirstName']
					.'&lName='.$model['UserLastName']
					.'&mileageCardProjectID='.$model['MileageCardProjectID'];
					return $url;
				}
			},
		],
		[
			'class' => 'kartik\grid\CheckboxColumn',
			'header' => Html::checkBox('selection_all', false, [
				'class' => 'select-on-check-all',
				'disabled' => ($unapprovedMileageCardVisible)  ? false : true,
			]),
			'checkboxOptions' => function ($model, $key, $index, $column) {
				// Disable if already approved or SumHours is 0
				$disabledBoolean = $model["MileageCardApprovedFlag"] == 1;
				$result = [
					'mileageCardId' => $model["MileageCardID"],
					'approved' => $model["MileageCardApprovedFlag"],
					'totalmileage' => $model["SumMiles"]
				];
				if ($disabledBoolean) {
					$result['disabled'] = 'true';
				}
				return $result;
			}
		],
	];
}
?>

<div class="mileagecard-index">
    <div class="lightBlueBar" style="height: 110px; padding: 10px;">
        <h3 class="title"><?= Html::encode($this->title) ?></h3>
        <div id="mileage_card_filter">
            <div id="mileageCardDropdownContainer">
                <?php $form = ActiveForm::begin([
                    'type' => ActiveForm::TYPE_HORIZONTAL,
                    'formConfig' => ['labelSpan' => 7, 'deviceSize' => ActiveForm::SIZE_SMALL],
                    'method' => 'get',
                    'options' => [
                        'id' => 'MileageCardForm',
                    ],
					'action' => Url::to(['mileage-card/index'])
                ]); ?>
				<div class="row">
                    <div style="float: right;margin-top: -2%;width: 21%;">
                        <?= $form->field($model, 'pageSize', ['labelSpan' => 6])->dropDownList($pageSize, ['value' => $model->pageSize, 'id' => 'mileageCardPageSize'])->label("Records Per Page", [
                            'class' => 'MileageCardRecordsPerPage'
                        ]); ?>
                    </div>
                </div>
				<?php Pjax::begin(['id' => 'mileageSubmitApproveButtons', 'timeout' => false]) ?>
					<div class="row">
						<div id="multiple_mileage_card_approve_btn">
							<?php 
								$approveButton = [
									'class' => 'btn btn-primary multiple_approve_btn',
									'id' => 'mc_multiple_approve_btn_id',
									'disabled' => true
								];
								if($isAccountant) {
									echo Html::button('Submit',
									[
										'class' => $accountingSubmitReady ? 'btn btn-primary multiple_submit_btn enable-btn' : 'btn btn-primary multiple_submit_btn off-btn',
										'id' => 'mileage_acc_submit_btn_id',
										'submitted' => $projectSubmitted ? 'true' : 'false'
									]);
									echo Html::button('PM Reset',
                                    [
                                        'class' => 'btn btn-primary pm_reset_btn',
                                        'id' => 'pm_mileage_card_reset',
                                        'disabled' => true
                                    ]);

								}elseif($isProjectManager){
									if($pmSubmitReady || $unapprovedMileageCardInProject){
                                        echo Html::button('Submit',
                                        [
                                            'class' => $pmSubmitReady ? 'btn btn-primary multiple_submit_btn enable-btn' : 'btn btn-primary multiple_submit_btn off-btn disabled',
                                            'id' => 'mileage_pm_submit_btn_id'
                                        ]);
                                        echo Html::button('Approve', $approveButton);
                                    }else{                                    
                                        echo Html::button('Request Reset',
                                        [
                                            'class' => 'btn btn-primary mc_pm_reset_request_btn',
                                            'id' => 'mc_pm_reset_request_btn_id',
                                            'disabled' => false
                                        ]);
                                    }
								}elseif($canApprove)
									echo Html::button('Approve',$approveButton);
							?>
						</div>
					</div>
                <?php Pjax::end() ?>
				<div class="col-md-3 MileageCardSearch">
                    <?= $form->field($model, 'filter', ['labelSpan' => 3])->textInput(['value' => $model->filter, 'placeholder' => 'Example: username, project', 'id' => 'mileageCardFilter'])->label("Search"); ?>
                </div>
				<?php echo Html::img('@web/logo/filter_clear_black.png', ['id' => 'mileageCardSearchCleanFilterButton']) ?>
				<div class="col-md-2 MileageCardDateRangeDropDown">
					<?= $form->field($model, 'dateRangeValue', ['labelSpan' => 3])->dropDownList($dateRangeDD, ['value' => $model->dateRangeValue, 'id' => 'mileageCardDateRange'])->label("Week"); ?>
				</div>
				<?php Pjax::begin(['id' => 'mileageCardDropDownPjax', 'timeout' => false]) ?>
					<?php if($showFilter){ ?>
						<div class="col-md-2 mileageProjectFilterDD">
							<?=
								$form->field($model, 'projectID', ['labelSpan' => 3])->dropDownList($projectDropDown,
								['value' => $model->projectID, 'id'=>'mileageProjectFilterDD'])->label('Project'); 
							?>
						</div>
					<?php }else{
						echo "<input type='hidden' value=$model->projectID id='mileageProjectFilterDD'>";
					} ?>
					<?php if(!$isAccountant){ ?>
						<div class="col-md-2 mileageEmployeeFilterDD">
							<?=
								$form->field($model, 'employeeID', ['labelSpan' => 3])->dropDownList($employeeDropDown,
								['value' => $model->employeeID, 'id'=>'mileageEmployeeFilterDD'])->label('Employee'); 
							?>
						</div>
					<?php } ?>
					<?php echo Html::img('@web/logo/filter_clear_black.png', ['id' => 'mileageCardClearDropdownFilterButton']) ?>
				<?php Pjax::end() ?>
				<?php if($model->dateRangeValue == 'other'){ ?>
					<div id="mileageDatePickerContainer" style="float: left; width: auto; display: block;">
				<?php } else { ?>
					<div id="mileageDatePickerContainer" style="float: left; width: auto; display: none;">
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
							"." var form = $('#mileageCardDropdownContainer').find('#MileageCardForm');
							if (form.find('.has-error').length){
								return false;
							}
							$('#loading').show();
							$.pjax.reload({
								type: 'GET',
								url: form.attr('action'),
								container: '#mileageCardGridview', // id to update content
								data: form.serialize(),
								timeout: 99999
							});
							$('#mileageCardGridview').off('pjax:success').on('pjax:success', function () {
								$.pjax.reload({
									container: '#mileageSubmitApproveButtons',
									timeout:false
								});
								$('#mileageSubmitApproveButtons').off('pjax:success').on('pjax:success', function () {
									mileageCardApproveMultiple();
									mileageCardPmSubmit();
									mileageCardAccountantSubmit();
									mileageCardPMReset();
									$('#loading').hide();
								});
								$('#mileageSubmitApproveButtons').off('pjax:error').on('pjax:error', function () {
									location.reload();
								});
							});
							$('#mileageCardGridview').off('pjax:error').on('pjax:error', function () {
								location.reload();
							});
							$('#mileageDatePickerContainer').css(\"display\", \"block\"); "."
						}"],
					]); ?>
				</div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
    <!-- General Table Layout for displaying Mileage Card Information -->
    <div id="mileageCardGridViewContainer">
        <div id="mileageCardGV" class="mileageCardForm">
            <?php Pjax::begin(['id' => 'mileageCardGridview', 'timeout' => false]) ?>
            <?= GridView::widget([
				'id' => 'GridViewForMileageCard',
                'dataProvider' => $dataProvider,
                'export' => false,
                'pjax' => true,
                'summary' => '',
				'showOnEmpty' => true,
                'emptyText' => 'No results found!',
                'columns' => $column
            ]); ?>
            <div id="MCPagination">
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
<!--submission toast messages-->
<div id = "ctGrowlContainer"></div>
<ul id = "ct-growl-clone">
	<ul>
		<li class = "title"></li>
		<li class = "msg"></li>
		<li class = "icon"><span class="close">X</span></li>
	</ul>
</ul>