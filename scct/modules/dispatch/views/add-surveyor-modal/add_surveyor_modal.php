<?php

use kartik\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;
use kartik\form\ActiveForm;

?>
<div id="assignedaddsurveyordialogtitle">
    <div id="addSurveyorModalHeader" class="addsurveryContainer"><span class="addsurveryheader"><b>Map/Plat:</b></span>
    <?php
       /* $count = 0;
        if(count($MapPlat) < 2) {
            echo $MapPlat[0] . "<input type=hidden name=IRUID value=".$IRUID[0].">";
        }else{
            foreach ($MapPlat as $item){
                echo $item . "<input type=hidden name=IRUID value=".$IRUID[$count++].">";
            }
        }*/
    ?>
    </div>
    <div id="add-surveyor-dropDownList-form">
        <?php yii\widgets\Pjax::begin(['id' => 'addSurveyorForm']) ?>
        <?php $form = ActiveForm::begin([
            'type' => ActiveForm::TYPE_VERTICAL,
            //'method' => 'post',
            // 500 internal server error ->'data-pjax' => true,
            //'action' => ['/dispatch/add-surveyor-modal'],
        ]); ?>
        <div class="addsurveryContainer">
            <div id="addsurveyorSearchcontainer" class="dropdowntitle">
                <?= $form->field($model, 'modalSearch')->textInput(['value' => $searchFilterVal, 'id' => 'addSurveyorSearch', 'placeholder'=>'Search'])->label('Surveyor / Inspector'); ?>
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
    <?php echo Html::button('DISPATCH', [ 'class' => 'btn btn-primary modalDispatchBtn', 'id' => 'addSurveyorDispatchButton' ]);?>
</div>
<script type="text/javascript">

    function enableDisableControls(enabled, MapGrid, SectionNumber)
    {
        console.log("enableDisableControls Called");
        $(".kv-row-select input[type=checkbox]").prop('disabled', !enabled);
        $("#SurveyorModalCleanFilterButton").prop('disabled', !enabled);
        $('.modalDispatchBtn').prop('disabled', true); // always disable this one.  Checking an item will enable it 
        $('#addSurveyorButton').prop('disabled', !enabled); 
        $('#assigned-surveyorWorkcenter-id').prop('disabled', !enabled);
        $('#addSurveyorSearch').prop('disabled', !enabled);
        resetButtonState(); // make check enable / disable dispatch button
    }

    function resetButtonState()
    {
        $('.modalDispatchBtn').prop('disabled', true); //TO DISABLED
        $('#addSurveyorButton').prop('disabled', true); //TO DISABLED

        $(".AddSurveyor input[type=checkbox]").click(function () {
            assignedUserID = $("#addSurveyorsGridview #surveyorGV").yiiGridView('getSelectedRows');
            dispatchMapData = getDispatchMapArray(dispatchMap_MapGrid, assignedUserID[0]);
            dispatchSectionData = getDispatchSectionArray(dispatchSection_SectionNumber, assignedUserID[0]);
            console.log(assignedUserID.length);
            if (assignedUserID.length == 1) {
                $('.modalDispatchBtn').prop('disabled', false); //TO DISABLED
                $('#addSurveyorModal').prop('disabled', false); //TO DISABLED

            } else {
                $('.modalDispatchBtn').prop('disabled', true); //TO DISABLED
            }
        });

        $('.modalDispatchBtn').click(function () {
            var form = $("#dispatchActiveForm");
            if (!assignedUserID || assignedUserID.length == 1) {
                // Ajax post request to dispatch action
                $.ajax({
                    timeout: 99999,
                    url: '/dispatch/dispatch/dispatch',
                    data: {dispatchMap: dispatchMapData, dispatchSection: dispatchSectionData},
                    //data: { MapGrid: mapGrid, AssignedUserID: assignedUserID, SectionNumber: sectionNumber },
                    type: 'POST',
                    beforeSend: function () {
                        $('#addSurveyorModal').modal("toggle");
                        $('#loading').show();
                    }
                }).done(function () {
                    $.pjax.reload({
                        container:'#dispatchUnassignedGridview',
                        timeout: 99999,
                        type: 'GET',
                        url: form.attr("action"),
                        data: form.serialize()
                    });
                    $('#dispatchUnassignedGridview').on('pjax:success', function() {
                        console.log("Pjax success");
                        $("#dispatchButton").prop('disabled', true);
                        $('#loading').hide();
                    });
                    $('#dispatchUnassignedGridview').on('pjax:error', function(e) {
                        e.preventDefault();
                    });
                });
            }
        });
    }

    function addSurveyorCheckboxListener() {
        $(".AddSurveyor input[type=checkbox]").click(function () {
            add_surveyor_pks = $("#addSurveyorsGridview #w1").yiiGridView('getSelectedRows');
            console.log(add_surveyor_pks.length);
            if (add_surveyor_pks.length > 0) {
                $('.modalDispatchBtn').prop('disabled', false); //TO DISABLED
                $('#addSurveyorModal').prop('disabled', false); //TO DISABLED

            } else {
                $('.modalDispatchBtn').prop('disabled', true); //TO DISABLED
            }
        });
    }

    // set trigger for search box in the add surveyor modal
    $(document).ready(function() {
        console.log("about to call");
        $('.modalDispatchBtn').prop('disabled', true); // always disable this one.  Checking an item will enable it
        /*$('#addSurveyorSearch').keydown(function(event){
            if(event.keyCode == 13) {
                event.preventDefault();
                $.pjax.reload({
                    type: 'POST',
                    url: '/dispatch/assigned/add-surveyor-modal',
                    container: '#addSurveyorsGridviewPJAX', // id to update content
                    data: {MapGrid: MapGrid, AssignedUserID: SectionNumber, SectionNumber: AssignedUserID},
                    timeout: 99999
                }).done(function () { $("body").css("cursor", "default"); enableDisableControls(true, MapGrid, SectionNumber, AssignedUserID); });
            }
        });*/
        resetButtonState();
	});
	
    //SurveyorModal CleanFilterButton listener
    $('#SurveyorModalCleanFilterButton').click(function () {
        $("#addSurveyorSearch").val("");
    });
</script>