<?php
use yii\helpers\Html;
use kartik\grid\GridView;
use app\controllers\Assigned;
use kartik\widgets\DepDrop;
use kartik\form\ActiveForm;
use yii\widgets\LinkPager;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\bootstrap\Modal;

$this->title = 'Assigned';
$this->params['breadcrumbs'][] = $this->title;
$pageSize = ["50" => "50", "100" => "100", "200" => "200"];
?>
<style>
  .modal-xl {
    width: 90%;
   max-width:1200px;
}
</style>
<div class="dispatch-assigned">
    <div id="assignedDropdownContainer" style="height: 140px;">

        <h3 class="title"><?= Html::encode($this->title) ?></h3>

        <div id="Assigned-dropDownList-form">
            <?php $form = ActiveForm::begin([
                'type' => ActiveForm::TYPE_VERTICAL,
                //'method' => 'get',
                'options' => ['id' => 'AssignForm', 'data-pjax' => true],
				'action' => Url::to(['assigned/index'])
            ]); ?>
                <span id="AssignedPageSizeLabel">
                        <?= $form->field($model, 'pagesize')->dropDownList($pageSize,
                            ['value' => $assignedPageSizeParams, 'id' => 'assignPageSize'])
                            ->label('Records Per Page', [
                                'class' => ''
                            ]); ?>
                </span>
                <span class="col-xs-1 col-md-1 col-lg-1" id="assignedButtonContainer">
                    <label style="color: #0067a6;"></label>
                    <?php Pjax::begin(['id' => 'assignButtons', 'timeout' => false]) ?>

                    <?php if ($canUnassign != 0) { ?>
                        <div id="assiunassignedButton">
                            <?php echo Html::button('REMOVE SURVEYOR', ['class' => 'btn btn-primary',
                                'id' => 'UnassignedButton']); ?>
                        </div>
                    <?php } else {
                        echo "";
                    } ?>
                    <?php Pjax::end() ?>
                </span>

                <div id="assignedSearchContainer">
                    <div id="filtertitle" class="dropdowntitle">
                        <?= $form->field($model, 'assignedfilter')->textInput(['value' => $assignedFilterParams, 'id' => 'assignedFilter', 'placeholder' => 'Search'])->label(''); ?>
                    </div>
                    <?php echo Html::img('@web/logo/filter_clear_black.png', ['class'=>'fixAssignFilter','id' => 'assignedSearchCleanFilterButton']) ?>
                </div>
            <input id="assignedTableRecordsUpdate" type="hidden" name="assignedTableRecordsUpdate" value="no" />
            <input id="assignedPageNumber" type="hidden" name="assignedPageNumber" value="1" />
            <?php ActiveForm::end(); ?>
        </div>
    </div>

    <div id="assginedGridViewContainer">
        <div id="assignedTable">
            <?php Pjax::begin(['id' => 'assignedGridview', 'timeout' => false]) ?>
            <?= GridView::widget([
                'dataProvider' => $assignedDataProvider,
                'id' => 'assignedGV',
                'summary' => false,
                'pjax' => true,
                'pjaxSettings' => [
                    'options' => [
                        'enablePushState' => false,
                    ]
                ],
                'export' => false,
                'columns' => [
                    [
                        'class' => 'kartik\grid\ExpandRowColumn',
                        'headerOptions' => ['class' => 'text-center', 'style' => 'width:5.1%'],
                        'contentOptions' => ['class' => 'text-center', 'style' => 'width:5.1%'],
                        'expandAllTitle' => 'Expand all',
                        'collapseTitle' => 'Collapse all',
                        'expandIcon' => '<span class="glyphicon glyphicon-expand"></span>',
                        'value' => function ($model, $key, $index, $column) {
                            return GridView::ROW_COLLAPSED;
                        },

                        'detailUrl' => Url::to(['assigned/view-section']),
                        'detailAnimationDuration' => 'fast',
                    ],
                    [
                        'label' => 'Map Grid',
                        'attribute' => 'MapGrid',
                        'headerOptions' => ['class' => 'text-center', 'style' => 'width: 16%;'],
                        'contentOptions' => ['class' => 'text-center', 'style' => 'width: 16%'],
                        'format' => 'html',
                    ],
                    [
                        'label' => 'Assigned User(s)',
                        'attribute' => 'AssignedUser',
                        'headerOptions' => ['class' => 'text-center', 'style' => 'width: 16.3%;'],
                        'contentOptions' => ['class' => 'text-center', 'style' => 'width: 16.3%'],
                        'format' => 'html',
                    ],
                    [
                        'label' => 'Compliance Start',
                        'attribute' => 'ComplianceStart',
                        'headerOptions' => ['class' => 'text-center', 'style' => 'width: 20%;'],
                        'contentOptions' => ['class' => 'text-center', 'style' => 'width: 20%'],
                        'format' => 'html',
                    ],
                    [
                        'label' => 'Compliance End',
                        'attribute' => 'ComplianceEnd',
                        'headerOptions' => ['class' => 'text-center', 'style' => 'width: 20%;'],
                        'contentOptions' => ['class' => 'text-center', 'style' => 'width: 20%'],
                        'format' => 'html',
                    ],
                    [
                        'label' => 'Completed (%)',
                        'attribute' => 'Percent Completed',
                        'headerOptions' => ['class' => 'text-center', 'style' => 'width: 5%;'],
                        'contentOptions' => ['class' => 'text-center', 'style' => 'width: 5%'],
                        'format' => 'raw',
                        'value' => function ($model, $key, $index) {
                            return Html::a(
                                $model['Percent Completed']."<br> View Map",
                                ['/../tracker/view-map?mapgrid='.$model['MapGrid']],
                                ['target'=>'_blank', 'data-pjax'=>"0"]
                            );
                        }
                    ],
					[
                        'label' => 'Remaining/Total',
                        'attribute' => 'Counts',
                        'headerOptions' => ['class' => 'text-center', 'style' => 'width: 5%;'],
                        'contentOptions' => ['class' => 'text-center', 'style' => 'width: 5%'],
                        'format' => 'html',
                        'value' => function ($model) {
                            return $model['Remaining'] . "/" . $model['Total'];
                        }
                    ],
                    [
                        'label' => 'Inspection Type',
                        'attribute' => 'InspectionType',
                        'headerOptions' => ['class' => 'text-center', 'style' => 'width: 16.3%;'],
                        'contentOptions' => ['class' => 'text-center', 'style' => 'width: 16.3%'],
                        'format' => 'html',
                    ],
                    [
                        'label' => 'Billing Code',
                        'attribute' => 'BillingCode',
                        'headerOptions' => ['class' => 'text-center', 'style' => 'width: 16.3%;'],
                        'contentOptions' => ['class' => 'text-center', 'style' => 'width: 16.3%'],
                        'format' => 'html',
                    ],
                    [
                        'label' => 'Office Name',
                        'attribute' => 'OfficeName',
                        'headerOptions' => ['class' => 'text-center', 'style' => 'width: 16.3%;'],
                        'contentOptions' => ['class' => 'text-center', 'style' => 'width: 16.3%'],
                        'format' => 'html',
                    ],
                    [
                        'class' => 'kartik\grid\ActionColumn',
                        'template' => '{view}',
                        'header' => 'View<br/>Assets',
                        'headerOptions' => ['class' => 'text-center', 'style' => 'width: 5%'],
                        'contentOptions' => ['class' => 'text-center ViewAssetBtn_AssignedMapGrid', 'style' => 'width: 5%'],
                        'buttons' => [
                            'view' => function($url, $model) {
                                $modalViewAssetAssigned = "#modalViewAssetAssigned";
                                $modalContentViewAssetAssigned = "#modalContentViewAssetAssigned";
                                return Html::a('', null, ['class' =>'glyphicon glyphicon-eye-open', 'onclick' => "viewAssetRowClicked('/dispatch/assigned/view-asset?mapGridSelected=" . $model['MapGrid']."&inspectionType=".$model['InspectionType'] . "&billingCode=".$model['BillingCode']. "','".$modalViewAssetAssigned ."','".$modalContentViewAssetAssigned."','".$model['MapGrid']."')"]);
                            }
                        ],
                        'urlCreator' => function ($action, $model, $key, $index) {
                        }
                    ],
                    [
                        'header' => 'Remove Surveyor',
                        'class' => 'kartik\grid\CheckboxColumn',
                        'headerOptions' => ['class' => 'text-center', 'style' => 'width: 5%; word-wrap: break-word;'],
                        'contentOptions' => ['class' => 'text-center assignedCheckbox', 'style' => 'width: 5%'],
                        'checkboxOptions' => function ($model, $key, $index, $column) {
                            if ($model['InProgressFlag'] != "1")
								$disabled = false;
							else
								$disabled = 'disabled';
							return [
								'MapGrid' => $model['MapGrid'],
								'BillingCode' => $model['BillingCode'],
								'InspectionType' => $model['InspectionType'],
								'OfficeName' => $model['OfficeName'],
								'disabled' => $disabled,
								'UserName' => $model['AssignedUser'],
								'InProgressFlag' => $model['InProgressFlag'] 
							];
                        }
                    ]
                ]
            ]); ?>
            <div id="assignedPagination">
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

	<!-- Unassign Confirmation modal -->
	<?php
		Modal::begin([
			'header' => '<h4 id="unassignConfirmationModalTitle">PLEASE CONFIRM THE SURVEYORS YOU WISH TO REMOVE</h4>',
			'id' => 'unassignConfirmationModal',
		]);
	?>
    <div id='unassignConfirmationModalContent'>
        Loading...
    </div>
    <?php
		Modal::end();
    ?>
	
	<!--still in use by asset level unassign-->
    <div id="unassigned-message" class="modal">
		<div class="modal-dialog">
			<!-- Modal content -->
			<div class="modal-content">
				<div class="modal-header">
					<h3>PLEASE CONFIRM THE SURVEYORS YOU WISH TO REMOVE</h3>
				</div>
				<div class="modal-body">
					<p><span class="unassignedUserName"></span></p>
				</div>
				<div class="modal-footer">
					<div id="unassignConfirmButton" class="unassignAssetBtn">
						<?php echo Html::button('Confirm', ['class' => 'btn btn-primary', 'id' => 'unassignedConfirmBtn']); ?>
					</div>
					<div id="unassignCancelButton" class="unassignAssetBtn">
						<?php echo Html::button('Cancel', ['class' => 'btn btn-primary', 'id' => 'unassignedCancelBtn']); ?>
					</div>
				</div>	
			</div>
		</div>
    </div>

    <!--View Asset Modal-->
    <?php
    Modal::begin([
		'header' => '<h4 id="assetModalTitle"></h4>',
        'id' => 'modalViewAssetAssigned',
        'size' => 'modal-xl',
    ]);

    ?>
    <div id='modalContentViewAssetAssigned'>
        Loading...
    </div>
    <?php

    Modal::end();
    ?>
</div>


