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
            'options' => ['id' => 'viewDispatchAssetsActiveForm'],
            'action' => '/dispatch/dispatch/view-asset'
        ]); ?>
        <div class="viewAssetsSearchcontainer dropdowntitle">
            <?= $form->field($model, 'modalSearch')->textInput(['value' => $searchFilterVal, 'id' => 'viewAssetsSearchDispatch', 'placeholder'=>'Search'])->label(''); ?>
        </div>
        <?php echo Html::img('@web/logo/filter_clear_black.png', ['id' => 'assetsModalCleanFilterButtonDispatch']) ?>
        <input id="searchFilterVal" type="hidden" name="searchFilterVal" value=<?php echo $searchFilterVal; ?> />
        <input id="mapGridSelected" type="hidden" name="mapGridSelected" value=<?php echo $mapGridSelected; ?> />
        <input id="sectionNumberSelected" type="hidden" name="sectionNumberSelected" value=<?php echo $sectionNumberSelected; ?> />
        <input id="viewDispatchAssetPageNumber" type="hidden" name="viewDispatchAssetPageNumber" value="1"/>
        <?php ActiveForm::end(); ?>
        <?php yii\widgets\Pjax::end() ?>
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
        'columns' => [
            [
                'label' => 'Address',
                'attribute' => 'HouseNumber',
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
                'format' => 'raw',
                'value' => function($model){
                    return $model['HouseNumber'] . " " . $model['Street']. " " .$model['AptSuite']. "<br/>" . $model['City'] . " , " . $model['State'] . " " . $model['Zip'];
                }
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
                'attribute' => 'Add Surveyor',
                'format' => 'raw',
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
                'value' => function ($model) {
                    $dropDownListOpenSelect = '<select class="assetSurveyorDropDown" WorkOrderID='.$model['WorkOrderID']. " MapGrid=".$model['MapGrid'].'><option value=null>Please Select a User</option>';
                    $dropDownListCloseSelect = '</select>';
                    foreach ($model['userList'] as $item){
                        $dropDownListOpenSelect = $dropDownListOpenSelect.'<option value='.$item['UserID'].'>'.$item['UserName'].'</option>';
                    }
                    return $dropDownListOpenSelect.$dropDownListCloseSelect;
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
        $('#viewAssetsSearchDispatch').keypress(function (event) {
            var key = event.which;
            if (key == 13) {
                var searchFilterVal = $('#viewAssetsSearchDispatch').val();
                console.log("about to call");
                console.log("searchFilterVal: " + searchFilterVal);
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

        //pagination listener on view asset modal
        $(document).off('click', '#assetsTablePagination .pagination li a').on('click', '#assetsTablePagination .pagination li a', function (event) {
            event.preventDefault();
            var page = $(this).data('page') + 1; // Shift by one to 1-index instead of 0-index.
            $('#viewAssetPageNumber').val(page);
            reloadViewAssetsModalDispatch(page);
        });
    });

    function reloadViewAssetsModalDispatch(page) {
        var form = $('#viewAssetsFormDispatch');
        var searchFilterVal = $('#viewAssetsSearchDispatch').val() == "/" ? "" : $('#viewAssetsSearchDispatch').val();
        var mapGridSelected = $('#mapGridSelected').val() == "/" ? "" : $('#mapGridSelected').val();
        var sectionNumberSelected = $('#sectionNumberSelected').val() == "/" ? "" : $('#sectionNumberSelected').val();
        console.log("searchFilterVal: "+searchFilterVal+" mapGridSelected: "+mapGridSelected+" sectionNumberSelected: "+sectionNumberSelected);
        $('#loading').show();
        $.pjax.reload({
            type: 'GET',
            url: '/dispatch/dispatch/view-asset',
            container: '#assetTablePjax', // id to update content
            data: {searchFilterVal: searchFilterVal, mapGridSelected: mapGridSelected, sectionNumberSelected: sectionNumberSelected, viewDispatchAssetPageNumber:page},
            timeout: 99999,
            push: false,
            replace: false,
            replaceRedirect: false
        }).done(function () {
            $("body").css("cursor", "default");
            $('#loading').hide();
        });
    }
</script>

