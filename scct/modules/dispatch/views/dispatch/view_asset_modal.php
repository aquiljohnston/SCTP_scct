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
$pageSize = ["50" => "50", "100" => "100", "200" => "200"];
?>
<style>
/*fix overflow header* -- eigyan*/
    .kv-thead-float{
    width: 100% !important;
    }
</style>

<div id="viewAssetModalContainer">
    <div id="assetDispatchContainer">
        <?php yii\widgets\Pjax::begin(['id' => 'assetDispatchForm']) ?>
        <?php $form = ActiveForm::begin([
            'type' => ActiveForm::TYPE_VERTICAL,
            'options' => ['id' => 'viewDispatchAssetsActiveForm'],
            'action' => '/dispatch/dispatch/view-asset'
        ]); ?>
        <span id="dispatchAssetsPageSizeLabel" style="float: right;margin: -20px auto;width: 16%;color: #0067a6;display: inline !important;">
                <?= $form->field($model, 'pagesize')->dropDownList($pageSize,
                    ['value' => $viewAssetPageSizeParams, 'id' => 'dispatchAssetsPageSize'])
                    ->label('Records Per Page', [
                        'class' => 'recordsPerPage'
                    ]); ?>
        </span>
        <div class="viewAssetsSearchcontainer dropdowntitle">
            <?= $form->field($model, 'modalSearch')->textInput(['value' => $searchFilterVal, 'id' => 'viewAssetsSearchDispatch', 'placeholder'=>'Search'])->label(''); ?>
        </div>
        <?php echo Html::img('@web/logo/filter_clear_black.png', ['id' => 'assetsModalCleanFilterButtonDispatch']) ?>
        <input id="searchFilterVal" type="hidden" name="searchFilterVal" value=<?php echo $searchFilterVal; ?> />
        <input id="mapGridSelected" type="hidden" name="mapGridSelected" value=<?php echo $mapGridSelected; ?> />
        <input id="sectionNumberSelected" type="hidden" name="sectionNumberSelected" value=<?php echo $sectionNumberSelected; ?> />
        <input id="inspectionType" type="hidden" name="inspectionType" value="<?php echo $inspectionType; ?>" />
        <input id="billingCode" type="hidden" name="billingCode" value="<?php echo $billingCode; ?>"/>
        <input id="viewDispatchAssetPageNumber" type="hidden" name="viewDispatchAssetPageNumber" value="1"/>
        <?php ActiveForm::end(); ?>
        <?php yii\widgets\Pjax::end() ?>
        <?php Pjax::begin(['id' => 'dispatchBtnPjax', 'timeout' => false]) ?>
        <div id="addSurveyorButtonDispatchAssets" style="float: right; width: 16%; margin-top: -1.5%;">
            <?php echo Html::button('DISPATCH', ['class' => 'btn btn-primary', 'id' => 'dispatchAssetsButton', 'disabled' => 'disabled']); ?>
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
                'format' => 'raw',
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
                'label' => 'Inspection Type',
                'attribute' => 'InspectionType',
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
            ],
            [
                'label' => 'Billing Code',
                'attribute' => 'BillingCode',
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
            ],
            [
                'label' => 'Office Name',
                'attribute' => 'OfficeName',
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
            ],
            [
                'attribute' => 'Add Surveyor',
                'format' => 'raw',
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center surveyorDropDown'],
                'value' => function ($model) {
                    if (strpos($model['LocationType'], 'Gas Main') !== false || strpos($model['LocationType'], 'Pipeline') !== false) {
                        $dropDownListOpenSelect = '<select multiple style="text-align: center;text-align-last: center; width:155px" value=null class="assetPipelineSurveyorDropDown" WorkOrderID=' . $model['WorkOrderID'] . " MapGrid=" . $model['MapGrid'] . " SectionNumber=" . $model['SectionNumber'] . '>';
                    }else {
                        $dropDownListOpenSelect = '<select style="text-align: center;text-align-last: center; width:155px" value=null class="assetSurveyorDropDown" WorkOrderID=' . $model['WorkOrderID'] . " MapGrid=" . $model['MapGrid'] . " SectionNumber=" . $model['SectionNumber'] . '><option class="text-center" value=null>Please Select a User</option>';
                    }
                    $dropDownListCloseSelect = '</select>';
                    foreach ($model['userList'] as $item) {
                        $dropDownListOpenSelect = $dropDownListOpenSelect . '<option class="surveyorID text-center" value=' . $item['UserID'] . '>' . $item['Name'] . " (" . $item['UserName'] . ")" . '</option>';
                    }
                    return $dropDownListOpenSelect . $dropDownListCloseSelect;
                },
            ]
        ],
    ]); ?>
    <div id="assetsTablePagination" style="margin-top: 2%;">
        <?php
        // display pagination
        echo LinkPager::widget([
            'pagination' => $pages,
        ]); ?>
    </div>
    <div class="GridviewTotalNumber" style="margin-left: 0%;position: fixed;z-index: 42;bottom: 4.5%;">
        <?php echo "Showing " . ($pages->offset + 1) . "  to " . ($pages->offset + $pages->getPageSize()) . " of " . $pages->totalCount . " entries"; ?>
    </div>
    <?php Pjax::end() ?>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        // global variable to use to pop selected assets modal
        mapGridSelected = null;
        inspectionType = $('#inspectionType').val();
        billingCode = $('#billingCode').val();

        $('#viewAssetsSearchDispatch').keypress(function (event) {
            var key = event.which;
            if (key == 13) {
                var searchFilterVal = $('#viewAssetsSearchDispatch').val();
                //console.log("about to call");
                //console.log("searchFilterVal: " + searchFilterVal);
                if (event.keyCode == 13) {
                    event.preventDefault();
                    reloadViewAssetsModalDispatch();
                }
            }
        });

        $('#assetsModalCleanFilterButtonDispatch').on('click', function () {
            $('#viewAssetsSearchDispatch').val("");
            reloadViewAssetsModalDispatch();
        });

        //page size listener
        $(document).off('change', '#dispatchAssetsPageSize').on('change', '#dispatchAssetsPageSize', function () {
            reloadViewAssetsModalDispatch();
        });

        //pagination listener on view asset modal
        $(document).off('click', '#assetsTablePagination .pagination li a').on('click', '#assetsTablePagination .pagination li a', function (event) {
            event.preventDefault();
            var page = $(this).data('page') + 1; // Shift by one to 1-index instead of 0-index.
            $('#viewAssetPageNumber').val(page);
            reloadViewAssetsModalDispatch(page);
        });

        // Assets Surveyor Drop Down List listener
        $(document).off('change','.assetSurveyorDropDown').on('change', '.assetSurveyorDropDown', function (event) {
            $('#dispatchAssetsButton').prop('disabled', true);
            $('#assignedDispatchAssetsButton').prop('disabled', true);
            $('#assetGV-container tr').each(function () {
                AssignedUserID = $(this).find('.assetSurveyorDropDown').val();
                if (AssignedUserID != "null" && typeof AssignedUserID != 'undefined') {
                    $('#dispatchAssetsButton').prop('disabled', false);
                    $('#assignedDispatchAssetsButton').prop('disabled', false);
                    return false
                }
            });
        });

        // Assets Surveyor Multi- Drop Down List Listener
        $(document).off('change','.assetPipelineSurveyorDropDown').on('change', '.assetPipelineSurveyorDropDown', function (event) {
            $('#dispatchAssetsButton').prop('disabled', true);
            $('#assetGV-container tr').each(function () {
                var surveyorSelectedForPipeline = $(this).find('.assetPipelineSurveyorDropDown').val();
                if (surveyorSelectedForPipeline != null && surveyorSelectedForPipeline.length > 0) {
                    $('#dispatchAssetsButton').prop('disabled', false);
                    return false
                }
            });
        });

        $(document).off('click', '#dispatchAssetsButton').on('click', '#dispatchAssetsButton', function (event){
            var dispatchAsset = [];
            dispatchAsset = getDispatchAssetsData();
            //console.log("DISPATCH ASSET " + dispatchAsset);

            // Ajax post request to dispatch assets
            $.ajax({
                timeout: 99999,
                url: '/dispatch/dispatch/dispatch',
                data: {dispatchMap: [], dispatchSection: [], dispatchAsset: dispatchAsset},
                type: 'POST',
                beforeSend: function () {
                    $('#modalViewAssetDispatch').modal("hide");
                    $('#loading').show();
                }
            }).done(function () {
                $('.modal-backdrop').remove();
                viewAssetRowClicked('/dispatch/dispatch/view-asset?billingCode='+billingCode+'&inspectionType='+inspectionType+'&mapGridSelected=' + mapGridSelected, '#modalViewAssetDispatch', '#modalContentViewAssetDispatch', mapGridSelected);
                $('#loading').hide();
            });
        });
    });



    function reloadViewAssetsModalDispatch(page) {
        var form = $('#viewAssetsFormDispatch');
        var searchFilterVal = $('#viewAssetsSearchDispatch').val() == "/" ? "" : $('#viewAssetsSearchDispatch').val();
        var mapGridSelected = $('#mapGridSelected').val() == "/" ? "" : $('#mapGridSelected').val();
        var sectionNumberSelected = $('#sectionNumberSelected').val() == "/" ? "" : $('#sectionNumberSelected').val();
        var recordsPerPageSelected = $('#dispatchAssetsPageSize').val();
        var inspectionType = $('#inspectionType').val();
        var billingCode = $('#billingCode').val();
        console.log("searchFilterVal: "+searchFilterVal+" mapGridSelected: "+mapGridSelected+" sectionNumberSelected: "+sectionNumberSelected);
        $('#loading').show();
        $.pjax.reload({
            type: 'GET',
            url: '/dispatch/dispatch/view-asset',
            container: '#assetTablePjax', // id to update content
            data: {
                searchFilterVal: searchFilterVal, 
                mapGridSelected: mapGridSelected, 
                sectionNumberSelected: sectionNumberSelected, 
                viewDispatchAssetPageNumber:page, 
                recordsPerPageSelected: recordsPerPageSelected,
                inspectionType: inspectionType,
                billingCode: billingCode
            },
            timeout: 99999,
            push: false,
            replace: false,
            replaceRedirect: false
        }).done(function () {
            $("body").css("cursor", "default");
            $('#loading').hide();
        });
    }

    function getDispatchAssetsData() {
        var AssignedUserID = "";
        var workOrderID = "";
        var dispatchAsset = [];
        var surveyorSelectedForPipeline = null;
        var sectionNumber = null;

        $('#assetGV-container tr').each(function () {
            if ($(this).find(".surveyorDropDown select").hasClass("assetPipelineSurveyorDropDown")) {
                workOrderID = $(this).find('.assetPipelineSurveyorDropDown').attr("workorderid");
                sectionNumber = $(this).find('.assetPipelineSurveyorDropDown').attr("SectionNumber");
                surveyorSelectedForPipeline = $(this).find('.assetPipelineSurveyorDropDown').val();
                mapGridSelected = $('#assetGV-container').find('.assetPipelineSurveyorDropDown').attr("mapgrid");
                if(surveyorSelectedForPipeline != null && surveyorSelectedForPipeline.length > 0) {
                    $(this).find(".assetPipelineSurveyorDropDown option:selected").each(function () {
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
            }else {
                AssignedUserID = $(this).find('.assetSurveyorDropDown').val();
                workOrderID = $(this).find('.assetSurveyorDropDown').attr("workorderid");
                sectionNumber = $(this).find('.assetSurveyorDropDown').attr("SectionNumber");
                mapGridSelected = $('#assetGV-container').find('.assetSurveyorDropDown').attr("mapgrid");
                if (AssignedUserID != "null" && typeof AssignedUserID != 'undefined') {
                     dispatchAsset.push({
                         WorkOrderID: workOrderID,
                         SectionNumber: sectionNumber,
                         AssignedUserID: AssignedUserID
                     });
                 }
            }
        });
        return dispatchAsset;
    }
</script>

