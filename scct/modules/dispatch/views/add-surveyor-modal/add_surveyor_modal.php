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

        <div id="surveyorWorkcenter" class="dropdowntitle">
            <?php // surveyorWorkcenter Dropdown
            echo $form->field($model, 'surveyorWorkcenter')->dropDownList($surveyorWorkCenterList, ['id'=>'assigned-surveyorWorkcenter-id', 'value' => $workCenterFilterVal])->label('Work Center');  ?>
        </div>
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
                        return ['UserUID' => $model["UserUID"]];
                    }
                },
            ],
            [
                'label' => 'Name',
                'attribute' => 'UserFullName'
            ],
            [
                'label' => 'LANID',
                'attribute' => 'UserLANID'
            ],
            [
                'label' => 'Work Center',
                'attribute' => 'WorkCenter'
            ],

        ],
    ]); ?>

    <?php Pjax::end() ?>
</div>
<div id="addSurveyorsDispatchBtn">
    <?php echo Html::button('DISPATCH', [ 'class' => 'btn btn-primary modalDispatchBtn', 'id' => 'addSurveyorDispatchButton' ]);?>
</div>
<script type="text/javascript">

    function enableDisableControls(enabled, IRUIDArr)
    {
        console.log("enableDisableControls Called");
        $(".kv-row-select input[type=checkbox]").prop('disabled', !enabled);
        $("#SurveyorModalCleanFilterButton").prop('disabled', !enabled);
        $('.modalDispatchBtn').prop('disabled', true); // always disable this one.  Checking an item will enable it 
        $('#addSurveyorButton').prop('disabled', !enabled); 
        $('#assigned-surveyorWorkcenter-id').prop('disabled', !enabled);
        $('#addSurveyorSearch').prop('disabled', !enabled);
        resetButtonState(IRUIDArr); // make check enable / disable dispatch button
    }

    function resetButtonState(IRUIDArr)
    {
        console.log("resetButtonState Called");
        for (var i = 0; i < IRUIDArr.length; i++){
            console.log("IRUID length is "+IRUIDArr[i]);
        }

        var add_surveyor_pks = 0;
        $('.modalDispatchBtn').prop('disabled', true); //TO DISABLED
        $('#addSurveyorButton').prop('disabled', true); //TO DISABLED

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

        $('.modalDispatchBtn').click(function () {
            $('#AssignedTableRecordsUpdate').val(true);
            var form = $("#AssignForm");
            if (!add_surveyor_pks || add_surveyor_pks.length != 0) {
                // Ajax post request to dispatch action
                $.ajax({
                    timeout: 99999,
                    url: '/dispatch/dispatch/dispatch',
                    data: { UserUID: add_surveyor_pks, IRUID: IRUIDArr },
                    type: 'POST',
                    beforeSend: function () {
                        $('#addSurveyorModal').modal("toggle");
                        $('#loading').show();
                    }
                }).done(function () {
                    $( '#dialog-add-surveyor' ).prop('display', true);
                    $( '#dialog-add-surveyor' ).dialog("open");
                    $.pjax.reload({
                        container:'#assignedGridview',
                        timeout: 99999,
                        type: 'POST',
                        url: form.attr("action"),
                        data: form.serialize()
                    });
                    $('#assignedGridview').on('pjax:success', function() {
                        console.log("Pjax success");
                        var MapPlatArr = [];
                        var IRUIDArr = [];
                        $('#addSurveyor').prop('disabled', true);
                        resetAddSurveyorButton(MapPlatArr, IRUIDArr);
                        unassignCheckboxListener();
                        $('#AssignedTableRecordsUpdate').val(true);
                        $('#loading').hide();
                    });
                    $('#assignedGridview').on('pjax:error', function(e) {
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
    /*$(document).ready(function() {
        var MapPlatArr = [];
        var IRUIDArr = [];
        var pks = $("#assignedGridview #assign").yiiGridView('getSelectedRows');

        if (pks != 0){
            var IRUIDCounter = 0;
            var MapPlatCounter = 0;
            for (var i = 0; i < pks.length; i++) {
                if (MapPlatArr[MapPlatCounter] != $(".Add input[AssignedWorkQueueUID=" + pks[i] + "]").attr("mapplat")){
                    MapPlatArr[MapPlatCounter] = $(".Add input[AssignedWorkQueueUID=" + pks[i] + "]").attr("mapplat");
                    MapPlatCounter++;
                }
                if (IRUIDArr[IRUIDCounter] != $(".Add input[AssignedWorkQueueUID=" + pks[i] + "]").attr("IRUID")){
                    IRUIDArr[IRUIDCounter] = $(".Add input[AssignedWorkQueueUID=" + pks[i] + "]").attr("IRUID");
                    IRUIDCounter++;
                }
                continue;
            }
        } else {
            return false;
        }
        $('#addSurveyorSearch').keydown(function(event){
            if(event.keyCode == 13) {
                event.preventDefault();
                var workCenterFilterVal = $('#assigned-surveyorWorkCenter-id').val();
                var searchFilterVal = $('#addSurveyorSearch').val();

                $.pjax.reload({
                    type: 'POST',
                    url: '/dispatch/assigned/add-surveyor-modal',
                    container: '#addSurveyorsGridviewPJAX', // id to update content
                    data: {"mapplat": MapPlatArr,"IRUID": IRUIDArr, "workCenterFilterVal": workCenterFilterVal, "searchFilterVal": searchFilterVal},
                    timeout: 99999
                }).done(function () { $("body").css("cursor", "default"); enableDisableControls(true, IRUIDArr); });
            }
        });
        $('#assigned-surveyorWorkcenter-id').change(function() {
            /!*var MapPlatArr = [];
             var IRUIDArr = [];
             var pks = $("#assignedGridview #w1").yiiGridView('getSelectedRows');
             var workCenterFilterVal = $(this).val();
             var searchFilterVal = $('#addSurveyorSearch').val();

             if (pks != 0){
             var IRUIDCounter = 0;
             var MapPlatCounter = 0;
             for (var i = 0; i < pks.length; i++) {
             if (MapPlatArr[MapPlatCounter] != $(".Add input[AssignedWorkQueueUID=" + pks[i] + "]").attr("mapplat")){
             MapPlatArr[MapPlatCounter] = $(".Add input[AssignedWorkQueueUID=" + pks[i] + "]").attr("mapplat");
             MapPlatCounter++;
             }
             if (IRUIDArr[IRUIDCounter] != $(".Add input[AssignedWorkQueueUID=" + pks[i] + "]").attr("IRUID")){
             IRUIDArr[IRUIDCounter] = $(".Add input[AssignedWorkQueueUID=" + pks[i] + "]").attr("IRUID");
             IRUIDCounter++;
             }
             continue;
             }
             } else {
             return false;
             }*!/
            var workCenterFilterVal = $(this).val();
            var searchFilterVal = $('#addSurveyorSearch').val();
            $.pjax.reload({
                type: 'POST',
                url: '/dispatch/assigned/add-surveyor-modal',
                container: '#addSurveyorsGridviewPJAX', // id to update content
                data: {"mapplat": MapPlatArr,"IRUID": IRUIDArr, "workCenterFilterVal": workCenterFilterVal, "searchFilterVal": searchFilterVal},
                timeout: 99999
            }).done(function () { $("body").css("cursor", "default"); enableDisableControls(true, IRUIDArr); });
        });
        resetButtonState(IRUIDArr);
        //addPaginationListener(IRUIDArr);
	});*/
	
    //SurveyorModal CleanFilterButton listener
    $('#SurveyorModalCleanFilterButton').click(function () {
        $("#assigned-surveyorWorkcenter-id").val("");
        $("#addSurveyorSearch").val("");
    });
</script>