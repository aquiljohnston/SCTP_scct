<?php
/**
 * Created by PhpStorm.
 * User: tzhang
 * Date: 10/11/2017
 * Time: 1:16 PM
 */
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;
use kartik\form\ActiveForm;

?>
<div id="assignedaddsurveyordialogtitle">
    <div id="add-surveyor-dropDownList-form">
        <?php yii\widgets\Pjax::begin(['id' => 'addSurveyorForm']) ?>
        <?php $form = ActiveForm::begin([
            'type' => ActiveForm::TYPE_VERTICAL,
        ]); ?>
        <div class="addsurveryContainer">
            <div id="addsurveyorSearchcontainer" class="dropdowntitle">
                <?= $form->field($model, 'modalSearch')->textInput(['value' => $searchFilterVal, 'id' => 'addSurveyorSearchCge', 'placeholder'=>'Search'])->label('Surveyor / Inspector'); ?>
            </div>
            <?php echo Html::img('@web/logo/filter_clear_black.png', ['id' => 'SurveyorModalCleanFilterButton']) ?>
        </div>
        <?php ActiveForm::end(); ?>
        <?php yii\widgets\Pjax::end() ?>
    </div>
</div>
<div id="dispatchSurveyorsTable">
    <?php Pjax::begin([
        'id' => 'addSurveyorsGridviewPJAX',
        'timeout' => 10000,
        'enablePushState' => false  ]) ?>

    <?= GridView::widget([
        'id' =>'surveyorGV',
        'dataProvider' => $addSurveyorsDataProvider,
        'export' => false,
        'pjax' =>true,
        'pjaxSettings' => [
            'options' => [
                'id' => 'addSurveyorsGridview',
            ],
        ],
        'columns' => [
            [
                'class' => 'kartik\grid\CheckboxColumn',
                'header' => 'Select',
                'contentOptions' => ['class' => 'AddSurveyor'],
                'checkboxOptions' => function ($model, $key, $index, $column) {
                    if (!empty($addSurveyorsDataProvider)) {
                        return ['UserID' => $model["UserID"]];
                    }
                },
            ],
            [
                'label' => 'Name',
                'attribute' => 'Name',
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
            ],
            [
                'label' => 'User Name',
                'attribute' => 'UserName',
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
            ],
        ],
    ]); ?>

    <?php Pjax::end() ?>
</div>
<div id="addSurveyorsDispatchBtn">
    <?php echo Html::button('DISPATCH', [ 'class' => 'btn btn-primary modalDispatchCgeBtn', 'id' => 'addSurveyorDispatchButton' ]);?>
</div>
<script type="text/javascript">

    function enableDisableControls(enabled, searchFilterVal)
    {
        console.log("enableDisableControls Called");
        $(".kv-row-select input[type=checkbox]").prop('disabled', !enabled);
        $("#SurveyorModalCleanFilterButton").prop('disabled', !enabled);
        $('.modalDispatchCgeBtn').prop('disabled', true); // always disable this one.  Checking an item will enable it
        $('#addSurveyorButton').prop('disabled', !enabled);
        $('#assigned-surveyorWorkcenter-id').prop('disabled', !enabled);
        $('#addSurveyorSearchCge').prop('disabled', !enabled);
        resetCgeDispatchButtonState(); // make check enable / disable dispatch button
    }
	
    function resetCgeDispatchButtonState()
    {
        $('.modalDispatchCgeBtn').prop('disabled', true); //TO DISABLED
        $('#addSurveyorButton').prop('disabled', true); //TO DISABLED

        $(".AddSurveyor input[type=checkbox]").click(function () {
            assignedUserID = $("#addSurveyorsGridview #surveyorGV").yiiGridView('getSelectedRows');
            dispatchMapGridData = getCgeDispatchMapGridData(assignedUserID[0]);
            dispatchAssetsData = getCgeDispatchAssetsData(assignedUserID[0]);
            console.log(assignedUserID.length);
            if (assignedUserID.length == 1) {
                $('.modalDispatchCgeBtn').prop('disabled', false); //TO DISABLED
                $('#modalDispatchCgeBtn').prop('disabled', false); //TO DISABLED

            } else {
                $('.modalDispatchCgeBtn').prop('disabled', true); //TO DISABLED
            }
        });

        $('.modalDispatchCgeBtn').click(function () {
            var form = $("#cgeActiveForm");
            if (!assignedUserID || assignedUserID.length == 1) {
                // Ajax post request to dispatch action
                $.ajax({
                    timeout: 99999,
                    url: '/dispatch/cge/dispatch',
                    data: {dispatchMap: dispatchMapGridData, dispatchAsset: dispatchAssetsData},
                    type: 'POST',
                    beforeSend: function () {
                        $('#addSurveyorCgeModal').modal("hide");
                        $('#loading').show();
                    }
                }).done(function () {
                    $('#cgeDispatchButton').prop('disabled', true); //TO DISABLED
                    resetCge_Global_Variable();
                    $.pjax.reload({
                        container:'#cgeGridview',
                        timeout: 99999,
                        type: 'GET',
                        url: form.attr("action"),
                        data: form.serialize()
                    });
                    $('#cgeGridview').on('pjax:success', function() {
                        console.log("Pjax success");
                        $("#dispatchButton").prop('disabled', true);
                        $('#loading').hide();
                    });
                    $('#cgeGridview').on('pjax:error', function(e) {
                        e.preventDefault();
                    });
                });
            }
        });
    };

    // set trigger for search box in the add surveyor modal
    $(document).ready(function () {
        $('.modalDispatchCgeBtn').prop('disabled', true); // always disable this one.  Checking an item will enable it
        $('#addSurveyorSearchCge').keypress(function (event) {
            var key = event.which;
            if (key == 13) {
                var searchFilterVal = $('#addSurveyorSearchCge').val();
                console.log("about to call");
                console.log("searchFilterVal: " + searchFilterVal);
                if (event.keyCode == 13) {
                    event.preventDefault();
                    reloadCgeAssetsModal(searchFilterVal);
                }
            }
        });
        resetCgeDispatchButtonState();
    });

    function reloadCgeAssetsModal(searchFilterVal) {
        $.pjax.reload({
            type: 'POST',
            url: '/dispatch/add-surveyor-modal/add-surveyor-modal?modalName=cge',
            container: '#addSurveyorsGridviewPJAX', // id to update content
            data: {searchFilterVal: searchFilterVal},
            timeout: 99999,
            push: false,
            replace: false,
            replaceRedirect: false
        }).done(function () {
            $("body").css("cursor", "default");
            enableDisableControls(true, searchFilterVal);
        });
    }

    //SurveyorModal CleanFilterButton listener
    $('#SurveyorModalCleanFilterButton').click(function () {
        $("#addSurveyorSearchCge").val("");
        var searchFilterVal = $('#addSurveyorSearchCge').val();
        reloadCgeAssetsModal(searchFilterVal);
    });
</script>