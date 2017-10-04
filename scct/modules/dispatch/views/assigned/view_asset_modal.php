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
            <input id="searchFilterVal" type="hidden" name="searchFilterVal" value=<?php echo $searchFilterVal; ?> />
            <input id="mapGridSelected" type="hidden" name="mapGridSelected" value=<?php echo $mapGridSelected; ?> />
            <input id="sectionNumberSelected" type="hidden" name="sectionNumberSelected" value=<?php echo $sectionNumberSelected; ?> />
            <input id="viewAssignedAssetPageNumber" type="hidden" name="viewAssignedAssetPageNumber" value="1"/>
        </div>
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
        })
        //pagination listener on view asset modal
        $(document).off('click', '#assignedAssetsTablePagination .pagination li a').on('click', '#assignedAssetsTablePagination .pagination li a', function (event) {
            event.preventDefault();
            var page = $(this).data('page') + 1; // Shift by one to 1-index instead of 0-index.
            $('#viewAssignedAssetPageNumber').val(page);
            reloadViewAssetsModal(page);
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
</script>

