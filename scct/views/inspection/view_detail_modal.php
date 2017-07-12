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
use yii\helpers\Url;

?>
<div id="viewSectionDetailModalContainer">
    <div id="sectionDetailInspectionContainer">
        <?php yii\widgets\Pjax::begin(['id' => 'sectionDetailInspectionForm']) ?>
        <?php $form = ActiveForm::begin([
            'type' => ActiveForm::TYPE_VERTICAL,
        ]); ?>
        <div class="viewSectionDetailSearchcontainer dropdowntitle">
            <?= $form->field($model, 'modalSearch')->textInput(['value' => $searchFilterVal, 'id' => 'viewSectionDetailSearchInspection', 'placeholder'=>'Search'])->label(''); ?>
        </div>
        <?php echo Html::img('@web/logo/filter_clear_black.png', ['id' => 'sectionDetailModalCleanFilterButtonInspection']) ?>
        <input id="searchFilterVal" type="hidden" name="searchFilterVal" value=<?php echo $searchFilterVal; ?> />
        <input id="mapGridSelected" type="hidden" name="mapGridSelected" value=<?php echo $mapGridSelected; ?> />
        <input id="sectionNumberSelected" type="hidden" name="sectionNumberSelected" value=<?php echo $sectionNumberSelected; ?> />
        <?php ActiveForm::end(); ?>
        <?php yii\widgets\Pjax::end() ?>
    </div>
</div>
<div id="sectionDetailTable">
    <?php Pjax::begin([
        'id' => 'sectionDetailTablePjax',
        'timeout' => 10000,
        'enablePushState' => false]) ?>

    <?= GridView::widget([
        'id' => 'sectionDetailGV',
        'dataProvider' => $sectionDetailDataProvider,
        'export' => false,
        'pjax' => true,
        'summary' => '',
        'columns' => [
            [
                'class' => 'kartik\grid\ExpandRowColumn',
                'expandAllTitle' => 'Expand all',
                'collapseTitle' => 'Collapse all',
                'expandIcon' => '<span class="glyphicon glyphicon-expand"></span>',
                'value' => function ($model, $key, $index, $column) {
                    /*if ($model['sectionCount'] == null){
                        return GridView::ROW_NONE;
                    }*/
                    return GridView::ROW_COLLAPSED;
                },

                'detailUrl' => Url::to(['inspection/view-event']),
                'detailAnimationDuration' => 'fast'
            ],
            [
                'label' => 'MapGrid',
                'attribute' => 'MapGrid',
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center MapGrid'],
            ],
            [
                'label' => 'SectionNumber',
                'attribute' => 'SectionNumber',
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center SectionNumber'],
            ],
            [
                'label' => 'Inspector',
                'attribute' => 'Inspector',
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
            ],
            [
                'label' => 'InspectionDateTime',
                'attribute' => 'InspectionDateTime',
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
            ],
            [
                'header' => 'Adhoc',
                'class' => 'kartik\grid\CheckboxColumn',
                'contentOptions' => ['class' => 'Adhoc'],
                'checkboxOptions' => function ($model, $key, $index, $column) {
                    if ($model['Adhoc'] == 1)
                        return ['disabled' => true, 'checked' => true];
                    else
                        return ['disabled' => true, 'checked' => false];
                }
            ],
            [
                'header' => 'AOC',
                'class' => 'kartik\grid\CheckboxColumn',
                'contentOptions' => ['class' => 'AOC'],
                'checkboxOptions' => function ($model, $key, $index, $column) {
                    if ($model['AOC'] == 1)
                        return ['disabled' => true, 'checked' => true];
                    else
                        return ['disabled' => true, 'checked' => false];
                }
            ],
            [
                'header' => 'CGE',
                'class' => 'kartik\grid\CheckboxColumn',
                'contentOptions' => ['class' => 'CGE'],
                'checkboxOptions' => function ($model, $key, $index, $column) {
                    if ($model['CGE'] == 1)
                        return ['disabled' => true, 'checked' => true];
                    else
                        return ['disabled' => true, 'checked' => false];
                }
            ]
        ],
    ]); ?>

    <?php Pjax::end() ?>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('#viewSectionDetailSearchInspection').keypress(function (event) {
            var key = event.which;
            if (key == 13) {
                var searchFilterVal = $('#viewSectionDetailSearchInspection').val();
                console.log("about to call");
                console.log("searchFilterVal: " + searchFilterVal);
                if (event.keyCode == 13) {
                    event.preventDefault();
                    reloadViewSectionDetailModalInspection();
                }
            }
        });

        $('#sectionDetailModalCleanFilterButtonInspection').on('click', function () {
            $('#viewSectionDetailSearchInspection').val("");
            reloadViewSectionDetailModalInspection();
        });

        var sectionDetailGV = $("#sectionDetailGV");

        //expandable row column listener
        //$(document).off('kvexprow:toggle', "#sectionDetailTable #sectionDetailGV").on('kvexprow:toggle', "#sectionDetailTable #sectionDetailGV", function (event, ind, key, extra, state) {
        sectionDetailGV.on('kvexprow.beforeLoad.kvExpandRowColumn', function (event, ind, key, extra, state) {
            var mapGridSelected = $(this).find("[data-key='"+key+"']").find('.MapGrid').text();
            var sectionNumberSelected = $(this).find("[data-key='"+key+"']").find('.SectionNumber').text();
            console.log("MAPGRID: "+mapGridSelected);
            console.log("sectionNumberSelected: "+sectionNumberSelected);
            /*$.ajax({
                type: 'POST'
                url: '/inspection/view-event',
                data: { mapGridSelected: mapGridSelected, sectionNumberSelected: sectionNumberSelected }
            });*/
            //$.post( "/inspection/view-event", { mapGridSelected: mapGridSelected, sectionNumberSelected: sectionNumberSelected } );
        });
    });

    function reloadViewSectionDetailModalInspection() {
        var form = $('#viewSectionDetailFormInspection');
        var searchFilterVal = $('#viewSectionDetailSearchInspection').val() == "/" ? "" : $('#viewSectionDetailSearchInspection').val();
        var mapGridSelected = $('#mapGridSelected').val() == "/" ? "" : $('#mapGridSelected').val();
        var sectionNumberSelected = $('#sectionNumberSelected').val() == "/" ? "" : $('#sectionNumberSelected').val();
        console.log("searchFilterVal: "+searchFilterVal+" mapGridSelected: "+mapGridSelected+" sectionNumberSelected: "+sectionNumberSelected);
        $.pjax.reload({
            type: 'GET',
            url: '/inspection/view-section-detail-modal',
            container: '#sectionDetailTablePjax', // id to update content
            data: {searchFilterVal: searchFilterVal, mapGridSelected: mapGridSelected, sectionNumberSelected: sectionNumberSelected},
            timeout: 99999
        }).done(function () {
            $("body").css("cursor", "default");
        });
    }
</script>

