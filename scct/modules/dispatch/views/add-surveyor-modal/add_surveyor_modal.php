<?php

use kartik\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;
use kartik\form\ActiveForm;

?>
<div id="addSurveyorModalContainer">
    <div id="add-surveyor-dropDownList-form">
        <?php yii\widgets\Pjax::begin(['id' => 'addSurveyorForm']) ?>
        <?php $form = ActiveForm::begin([
            'type' => ActiveForm::TYPE_VERTICAL,
        ]); ?>
        <div class="addsurveryContainer">
            <div id="addsurveyorSearchcontainer">
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

    // set trigger for search box in the add surveyor modal
    $(document).ready(function () {
		$('.modalDispatchBtn').prop('disabled', true);
		applySurveyorTableListeners();
		
		//search filter listener
        $('#addSurveyorSearch').keypress(function (event) {
            var key = event.which;
            if (key == 13) {
                var searchFilterVal = $('#addSurveyorSearch').val();
                if (event.keyCode == 13) {
                    event.preventDefault();
                    reloadAddSurveyorModal(searchFilterVal);
                }
            }
        });
		
		//SurveyorModal CleanFilterButton listener
		$('#SurveyorModalCleanFilterButton').click(function () {
			$("#addSurveyorSearch").val("");
			var searchFilterVal = $('#addSurveyorSearch').val();
			reloadAddSurveyorModal(searchFilterVal);
		});

        $('.modalDispatchBtn').click(function () {
            var form = $("#addSurveyorForm");
			//get current screen from 3rd element in path '/dispatch/currentScreen'
			currentScreen = window.location.pathname.split('/')[2];
			//Dynamically build request params depending on screen
			if(currentScreen == 'dispatch'){
				//grab map data
				dispatchMapData = getDispatchMapArray(assignedUserIDs);
				dispatchSectionData = getDispatchSectionArray(assignedUserIDs);
				//build post data
				postData = {dispatchMap: dispatchMapData, dispatchSection: dispatchSectionData};
				//get sort value and append to form values
				var sort = getDispatchIndexSortParams();
				var pjaxFormData = form.serialize() + "&sort=" + sort;
				//set base screen ids for refresh
				gridViewID = '#dispatchUnassignedGridview';
				dispatchButtonID = '#dispatchButton';
			}else if(currentScreen == 'cge'){
				//grab map data
				dispatchMapData = getCgeDispatchMapGridData(assignedUserIDs);
				dispatchAssetsData = getCgeDispatchAssetsData(assignedUserIDs);
				//build post data
				postData = {dispatchMap: dispatchMapData, dispatchAsset: dispatchAssetsData};
				//get form values
				var pjaxFormData = form.serialize();
				//set base screen ids for refresh
				gridViewID = '#cgeGridview';
				dispatchButtonID = '#cgeDispatchButton';
			}
			
            if (!assignedUserIDs || assignedUserIDs.length > 0) {
                // Ajax post request to dispatch action
                $.ajax({
                    timeout: 99999,
                    url: '/dispatch/dispatch/dispatch',
                    data: postData,
                    type: 'POST',
                    beforeSend: function () {
                        $('#addSurveyorModal').modal("hide");
                        $('#loading').show();
                    }
                }).done(function () {
					//need to dynamically load the correct gridview
                    $.pjax.reload({
                        container: gridViewID,
                        timeout: 99999,
                        type: 'GET',
                        url: form.attr("action"),
                        data: pjaxFormData
                    });
                    $(gridViewID).on('pjax:success', function() {
                        $(dispatchButtonID).prop('disabled', true);
                        $('#loading').hide();
                    });
                    $(gridViewID).on('pjax:error', function(e) {
                        e.preventDefault();
                    });
                });
            }
        });
    });
	
	function applySurveyorTableListeners(){
        $(".AddSurveyor input[type=checkbox]").click(function () {
            assignedUserIDs = $("#addSurveyorsGridview #surveyorGV").yiiGridView('getSelectedRows');
            if (assignedUserIDs.length > 0) {
                $('.modalDispatchBtn').prop('disabled', false);
            } else {
                $('.modalDispatchBtn').prop('disabled', true);
            }
        });
    };

    function reloadAddSurveyorModal(searchFilterVal){
		$('#loading').show();
        $.pjax.reload({
            type: 'POST',
            url: '/dispatch/add-surveyor-modal/add-surveyor-modal',
            container: '#addSurveyorsGridviewPJAX',
            data: {searchFilterVal: searchFilterVal},
            timeout: 99999,
            push: false,
            replace: false
        }).done(function () {
			applySurveyorTableListeners();
            $("body").css("cursor", "default");
            $('.modalDispatchBtn').prop('disabled', true);
			$('#loading').hide();
        });
    }
</script>