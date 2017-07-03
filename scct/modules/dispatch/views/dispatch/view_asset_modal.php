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
        ]); ?>
        <div class="viewAssetsSearchcontainer dropdowntitle">
            <?= $form->field($model, 'modalSearch')->textInput(['value' => $searchFilterVal, 'id' => 'viewAssetsSearchDispatch', 'placeholder'=>'Search'])->label(''); ?>
        </div>
        <?php echo Html::img('@web/logo/filter_clear_black.png', ['id' => 'assetsModalCleanFilterButtonDispatch']) ?>
        <input id="searchFilterVal" type="hidden" name="searchFilterVal" value=<?php echo $searchFilterVal; ?> />
        <input id="mapGridSelected" type="hidden" name="mapGridSelected" value=<?php echo $mapGridSelected; ?> />
        <input id="sectionNumberSelected" type="hidden" name="sectionNumberSelected" value=<?php echo $sectionNumberSelected; ?> />
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
                'class' => 'kartik\grid\CheckboxColumn',
                'header' => 'Select',
                'contentOptions' => ['class' => 'AddSurveyor'],
                'checkboxOptions' => function ($model, $key, $index, $column) {
                    if (!empty($model)) {
                        return ['WorkOrderID' => $model["WorkOrderID"]];
                    }
                },
            ],
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
        ],
    ]); ?>

    <?php Pjax::end() ?>
</div>
<!--<div id="assetDispatchButtonContainer">
    <?php /*echo Html::button('DISPATCH', ['class' => 'btn btn-primary modalDispatchBtn', 'id' => 'assetDispatchButton']); */?>
</div>-->

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
        })
    });

    function reloadViewAssetsModalDispatch() {
        var form = $('#viewAssetsFormDispatch');
        var searchFilterVal = $('#viewAssetsSearchDispatch').val() == "/" ? "" : $('#viewAssetsSearchDispatch').val();
        var mapGridSelected = $('#mapGridSelected').val() == "/" ? "" : $('#mapGridSelected').val();
        var sectionNumberSelected = $('#sectionNumberSelected').val() == "/" ? "" : $('#sectionNumberSelected').val();
        console.log("searchFilterVal: "+searchFilterVal+" mapGridSelected: "+mapGridSelected+" sectionNumberSelected: "+sectionNumberSelected);
        $.pjax.reload({
            type: 'GET',
            url: '/dispatch/dispatch/view-asset',
            container: '#assetTablePjax', // id to update content
            data: {searchFilterVal: searchFilterVal, mapGridSelected: mapGridSelected, sectionNumberSelected: sectionNumberSelected},
            timeout: 99999
        }).done(function () {
            $("body").css("cursor", "default");
            //enableDisableControls(true, searchFilterVal);
        });
    }
</script>

