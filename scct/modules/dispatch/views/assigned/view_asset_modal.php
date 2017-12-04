<?php
/**
 * Created by PhpStorm.
 * User: tzhang
 * Date: 6/6/2017
 * Time: 1:23 PM
 */
use kartik\form\ActiveForm;
use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\widgets\LinkPager;
use kartik\grid\GridView;

?>
<div id="viewAssetModalContainer">
    <div id="assetDispatchContainer">
        <?php yii\widgets\Pjax::begin(['id' => 'assetDispatchForm']) ?>
        <?php $form = ActiveForm::begin([
            'type' => ActiveForm::TYPE_VERTICAL,
            'id' => 'viewAssetsFormAssigned'
        ]); ?>
        <div class="viewAssetsContainer ">
            <div class="viewAssetsSearchcontainer dropdowntitle">
                <?= $form->field($model, 'modalSearch')->textInput(['value' => $searchFilterVal, 'id' => 'viewAssetsSearchAssigned', 'placeholder'=>'Search'])->label(''); ?>
            </div>
            <?php echo Html::img('@web/logo/filter_clear_black.png', ['id' => 'assetsModalCleanFilterButtonAssigned']) ?>
            <div id="assignedAssetsButtonContainer" style="float: right;margin: 0% auto;width: 16%;">
                <?php Pjax::begin(['id' => 'assignButtons', 'timeout' => false]) ?>
                    <div id="assiunassignedButton">
                        <?php echo Html::button('UN-ASSIGNED', ['class' => 'btn btn-primary',
                            'id' => 'UnassignedAssetsButton', 'disabled' => 'disabled']); ?>
                    </div>
                <?php Pjax::end() ?>
            </div>
            <input id="searchFilterVal" type="hidden" name="searchFilterVal" value=<?php echo $searchFilterVal; ?> />
            <input id="mapGridSelected" type="hidden" name="mapGridSelected" value=<?php echo $mapGridSelected; ?> />
            <input id="sectionNumberSelected" type="hidden" name="sectionNumberSelected" value=<?php echo $sectionNumberSelected; ?> />
            <input id="viewAssignedAssetPageNumber" type="hidden" name="viewAssignedAssetPageNumber" value="1"/>
        </div>
        <?php ActiveForm::end(); ?>
        <?php yii\widgets\Pjax::end() ?>
        <?php Pjax::begin(['id' => 'dispatchBtnPjax', 'timeout' => false]) ?>
        <div id="addSurveyorButtonDispatchAssets" style="float: right; width: 16%; margin-top: -1.5%;">
            <?php echo Html::button('DISPATCH', ['class' => 'btn btn-primary', 'id' => 'assignedDispatchAssetsButton', 'disabled' => 'disabled']); ?>
        </div>
        <?php Pjax::end() ?>
    </div>
</div>
<div id="assetsTable">
    <?php Pjax::begin([
        'id' => 'assetTablePjax',
        'timeout' => 10000,
        'enablePushState' => false]) ?>

    <?= GridView::widget([
        'id' => 'assetGV',
        'dataProvider' => $assetDataProvider,
        'export' => false,
        'pjax' => true,
        'summary' => '',
        'floatHeader' => true,
        'floatOverflowContainer' => true,
        'columns' => [
            [
                'label' => 'Address',
                'attribute' => 'Address',
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
            ],
			[
                'label' => 'Assigned User',
                'attribute' => 'AssignedTo',
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
            ],
            [
                'label' => 'Meter Number',
                'attribute' => 'MeterNumber',
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
            ],
            [
                'label' => 'Account Phone#',
                'attribute' => 'AccountTelephoneNumber',
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
            ],
            [
                'label' => 'Attempt Count',
                'attribute' => 'InspectionAttemptCounter',
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
            ],
            [
                'label' => 'Status',
                'attribute' => 'WorkQueueStatus',
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
                'value' => function($model){
                    if ($model['WorkQueueStatus'] == 100)
                        return "Assigned";
                    elseif ($model['WorkQueueStatus'] == 101)
                        return "In Progress";
                    else
                        return "Completed";
                }
            ],
            [
                'header' => 'Un-assigned',
                'class' => 'kartik\grid\CheckboxColumn',
                'headerOptions' => ['class' => 'text-center', 'style' => 'word-wrap: break-word;'],
                'contentOptions' => ['class' => 'text-center unassignAssetsCheckbox'],
                'checkboxOptions' => function ($model, $key, $index, $column) {
                    $assetAddress = $model['HouseNumber']." ". $model['Street']." ". $model['AptSuite'].", ". $model['City']." ". $model['State'].", ". $model['Zip']."<br>";
                    return ['ClientWorkOrderID' => $model['ClientWorkOrderID'], 'AssignedTo' => $model['AssignedTo'],'WorkOrderID' => $model['WorkOrderID'], 'disabled' => $model['WorkQueueStatus'] == 101 ? 'disabled' : false, 'assetAddress' => $assetAddress, 'AssignedUserID' => $model['AssignedToID'] ];
                }
            ],
            [
                'attribute' => 'Add Surveyor',
                'format' => 'raw',
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center surveyorDropDown'],
                'value' => function ($model) {
                    if (strpos($model['LocationType'], 'Gas Main') !== false) {
                        $dropDownListOpenSelect = '<select style="text-align: center;text-align-last: center;" value=null class="assetSurveyorDropDown" WorkOrderID=' . $model['WorkOrderID'] . " MapGrid=" . $model['MapGrid'] . " SectionNumber=" . $model['SectionNumber'] . '><option class="text-center" value=null>Please Select a User</option>';
                        $dropDownListCloseSelect = '</select>';
                        foreach ($model['userList'] as $item) {
                            $dropDownListOpenSelect = $dropDownListOpenSelect . '<option class="surveyorID text-center" value=' . $item['UserID'] . '>' . $item['Name'] . " (" . $item['UserName'] . ")" . '</option>';
                        }
                        return $dropDownListOpenSelect . $dropDownListCloseSelect;
                    }else{
                        return "N/A";
                    }
                }
            ],
            [
                'label' => 'Inpection Type',
                'attribute' => 'InspectionType',
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
            ],
            [
                'label' => 'Billing Code',
                'attribute' => 'BillingCode',
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
            ]
        ],
    ]); ?>
    <div id="assignedAssetsTablePagination" style="margin-top: 2%;">
        <?php
        // display pagination
        echo LinkPager::widget([
            'pagination' => $pages,
        ]);
        ?>
    </div>
    <div class="GridviewTotalNumber" style="margin-left: 0%;position: fixed;z-index: 42;bottom: 4.5%;">
        <?php echo "Showing " . ($pages->offset + 1) . "  to " . ($pages->offset + $pages->getPageSize()) . " of " . $pages->totalCount . " entries"; ?>
    </div>

    <?php Pjax::end() ?>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        mapGridSelectedAssigned = null;

        $('#viewAssetsSearchAssigned').keypress(function (event) {
            var key = event.which;
            if (key == 13) {
                var searchFilterVal = $('#viewAssetsSearchAssigned').val();
                console.log("about to call");
                console.log("searchFilterVal: " + searchFilterVal);
                if (event.keyCode == 13) {
                    event.preventDefault();
                    reloadViewAssetsModal();
                }
            }
        });

        $('#assetsModalCleanFilterButtonAssigned').on('click', function () {
            $('#viewAssetsSearchAssigned').val("");
            reloadViewAssetsModal();
        });

        //pagination listener on view asset modal
        $(document).off('click', '#assignedAssetsTablePagination .pagination li a').on('click', '#assignedAssetsTablePagination .pagination li a', function (event) {
            event.preventDefault();
            var page = $(this).data('page') + 1; // Shift by one to 1-index instead of 0-index.
            $('#viewAssignedAssetPageNumber').val(page);
            reloadViewAssetsModal(page);
        });

        $(document).off('click', '#assignedDispatchAssetsButton').on('click', '#assignedDispatchAssetsButton', function (event){
            var assignedDispatchAsset = [];
            assignedDispatchAsset = getAssignedDispatchAssetsData();
            console.log("DISPATCH ASSET " + assignedDispatchAsset);

            // Ajax post request to dispatch assets
            $.ajax({
                timeout: 99999,
                url: '/dispatch/dispatch/dispatch',
                data: {dispatchMap: [], dispatchSection: [], dispatchAsset: assignedDispatchAsset},
                type: 'POST',
                beforeSend: function () {
                    $('#modalViewAssetDispatch').modal("hide");
                    $('#loading').show();
                }
            }).done(function () {
                $('.modal-backdrop').remove();
                viewAssetRowClicked('/dispatch/assigned/view-asset?mapGridSelected=' + mapGridSelectedAssigned, '#modalViewAssetAssigned', '#modalContentViewAssetAssigned', mapGridSelectedAssigned);
                $('#loading').hide();
            });
        });

        // Assets Surveyor Drop Down List listener
        $(document).off('change','.assetSurveyorDropDown').on('change', '.assetSurveyorDropDown', function (event) {
            $('#assignedDispatchAssetsButton').prop('disabled', true);
            $('#assetGV-container tr').each(function () {
                AssignedUserID = $(this).find('.assetSurveyorDropDown').val();
                if (AssignedUserID != "null" && typeof AssignedUserID != 'undefined') {
                    $('#assignedDispatchAssetsButton').prop('disabled', false);
                    return false
                }
            });
        });
    });

    function reloadViewAssetsModal(page) {
        var form = $('#viewAssetsFormAssigned');
        var searchFilterVal = $('#viewAssetsSearchAssigned').val() == "/" ? "" : $('#viewAssetsSearchAssigned').val();
        var mapGridSelected = $('#mapGridSelected').val() == "/" ? "" : $('#mapGridSelected').val();
        var sectionNumberSelected = $('#sectionNumberSelected').val() == "/" ? "" : $('#sectionNumberSelected').val();
        console.log("searchFilterVal: "+searchFilterVal+" mapGridSelected: "+mapGridSelected+" sectionNumberSelected: "+sectionNumberSelected);
        $('#loading').show();
        $.pjax.reload({
            type: 'GET',
            url: '/dispatch/assigned/view-asset',
            container: '#assetTablePjax', // id to update content
            data: {searchFilterVal: searchFilterVal, mapGridSelected: mapGridSelected, sectionNumberSelected: sectionNumberSelected, viewAssignedAssetPageNumber:page},
            timeout: 99999,
            push: false,
            replace: false,
            replaceRedirect: false
        }).done(function () {
            $("body").css("cursor", "default");
            $('#loading').hide();
        });
    }

    function getAssignedDispatchAssetsData() {
        var AssignedUserID = null;
        var workOrderID = null;
        var dispatchAsset = [];
        var sectionNumber = null;
        mapGridSelectedAssigned = $('#assetGV-container').find('.assetSurveyorDropDown').attr("mapgrid");
        $('#assetGV-container tr').each(function () {
            if ($(this).find(".surveyorDropDown select").hasClass("assetSurveyorDropDown")) {
                workOrderID = $(this).find('.assetSurveyorDropDown').attr("workorderid");
                sectionNumber = $(this).find('.assetSurveyorDropDown').attr("SectionNumber");
                
                $(this).find(".assetSurveyorDropDown option:selected").each(function () {
                    console.log("SELECTED SURVEYOR IS : " + $(this).val());
                    console.log("SELECTED WORKORDER IS : " + workOrderID);
                    AssignedUserID = $(this).val();
                    if (AssignedUserID != "null" && typeof AssignedUserID != 'undefined') {
                        dispatchAsset.push({
                            WorkOrderID: workOrderID,
                            SectionNumber: sectionNumber,
                            AssignedUserID: AssignedUserID
                        });
                    }
                });
            }
        });
        return dispatchAsset;
    }
</script>

