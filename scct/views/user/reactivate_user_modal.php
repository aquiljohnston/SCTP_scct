<?php

use kartik\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;
use kartik\form\ActiveForm;
use kartik\widgets\Spinner;

?>
<div id="reactivateUserDialogueTitle">
	<div id="reactivateLoading">		
		<div id="loading-image"><?= Spinner::widget(['preset' => 'medium', 'color' => 'black']);?></div>
		<div class="clearfix"></div>
	</div>
    <div id="reactivateUserModalHeader" class="reactivateUserContainer">
    </div>
    <div id="reactivate-user-filter-form">
        <?php yii\widgets\Pjax::begin(['id' => 'reactivateUserForm']) ?>
        <?php $form = ActiveForm::begin([
            'type' => ActiveForm::TYPE_VERTICAL,
        ]); ?>
        <div class="reactivateUserContainer">
            <div id="reactivateUserSearchContainer" class="searchTitle" style="position: relative;float: left;width: 50%;padding-right: 1%;">
                <?= $form->field($model, 'modalSearch')->textInput(['value' => $searchFilterVal, 'id' => 'reactivateUserSearch', 'placeholder'=>'Search'])->label('Inactive Users'); ?>
            </div>
			<?php echo Html::img('@web/logo/filter_clear_black.png', ['id' => 'reactivateUserCleanFilterButton', 'style' => 'margin-top: 25px;width: 32px;cursor: pointer;']) ?>
        </div>		
        <?php ActiveForm::end(); ?>
        <?php yii\widgets\Pjax::end() ?>
    </div>
</div>
<div id="reactivateUserTable">
    <?php Pjax::begin([
        'id' => 'reactivateUserGridviewPJAX',
        'timeout' => 10000,
        'enablePushState' => false  ]) ?>

    <?= GridView::widget([
        'id' =>'reactivateUserGV',
        'dataProvider' => $reactivateUserDataProvider,
        'export' => false,
        'pjax' =>true,
        'pjaxSettings' => [
            'options' => [
                'id' => 'reactivateUserGridview',
            ],
        ],
        'columns' => [
            [
                'class' => 'kartik\grid\CheckboxColumn',
				'header' => 'Select',
                'contentOptions' => ['class' => 'ReactivateUser'],
                'checkboxOptions' => function ($model, $key, $index, $column) {
                    if (!empty($reactivateUserDataProvider)) {
                        return ['UserName' => $model["UserName"]];
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
			[
                'label' => 'Role Type',
                'attribute' => 'UserAppRoleType',
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
            ],
        ],
    ]); ?>

    <?php Pjax::end() ?>
</div>
<div id="userReactivateBtn">
    <?php echo Html::button('Reactivate', [ 'class' => 'btn btn-primary modalReactivateBtn', 'id' => 'userReactivateModalBtn' ]);?>
</div>

<script type="text/javascript">
	
	function enableDisableControls(enabled, searchFilterVal)
    {
        $(".kv-row-select input[type=checkbox]").prop('disabled', !enabled);
        $("#reactivateModalCleanFilterButton").prop('disabled', !enabled);
        $('.modalReactivateBtn').prop('disabled', true); // always disable this one.  Checking an item will enable it 
        $('#reactivateUserButton').prop('disabled', !enabled); 
        $('#reactivateUserSearch').prop('disabled', !enabled);
        resetButtonState(); // make check enable / disable dispatch button
    }
	
	function resetButtonState()
    {
		$('#reactivateLoading').hide();
        $('.modalReactivateBtn').prop('disabled', true); //TO DISABLED
        $('#reactivateUserButton').prop('disabled', true); //TO DISABLED

        $(".ReactivateUser input[type=checkbox]").click(function () {
            Usernames = $("#reactivateUserGridview #reactivateUserGV").yiiGridView('getSelectedRows');
            if (Usernames.length > 0) {
                $('.modalReactivateBtn').prop('disabled', false); //TO DISABLED
            } else {
                $('.modalReactivateBtn').prop('disabled', true); //TO DISABLED
            }
        });

        $('.modalReactivateBtn').click(function () {
            var form = $("#reactivateUserForm");
            if (!Usernames || Usernames.length > 0) {
                // Ajax post request to dispatch action
                $.ajax({
                    timeout: 99999,
                    url: '/user/reactivate',
                    data: {Usernames: Usernames},
                    type: 'POST',
                    beforeSend: function () {
                        $('#reactivateUserModal').modal("hide");
                        $('#loading').show();
                    }
                }).done(function () {
                    $.pjax.reload({
                        container:'#userGridview',
                        timeout: 99999,
						push: false,
						replace: false,
						replaceRedirect: false,
                        type: 'GET',
                        url: form.attr("action"),
                        data: form.serialize()
                    });
                    $('#userGridview').on('pjax:success', function() {
                        $('#loading').hide();
                    });
                    $('#userGridview').on('pjax:error', function(e) {
                        e.preventDefault();
                    });
                });
            }
        });
    }
	
	function reactivateUserCheckboxListener() {
        $(".ReactivateUser input[type=checkbox]").click(function () {
            reactivate_user_pks = $("#reactivateUserGridview #w2").yiiGridView('getSelectedRows');
			if (reactivate_user_pks.length > 0) {
                $('.modalReactivateBtn').prop('disabled', false); //TO DISABLED
                $('#reactivateUserModal').prop('disabled', false); //TO DISABLED
            } else {
                $('.modalReactivateBtn').prop('disabled', true); //TO DISABLED
            }
        });
    }
	
	// set trigger for search box in the Reactivate User modal
    $(document).ready(function () {
        $('.modalReactivateBtn').prop('disabled', true); // always disable this one.  Checking an item will enable it
        $('#reactivateUserSearch').keypress(function (event) {
            var key = event.which;
            if (key == 13) {
                var searchFilterVal = $('#reactivateUserSearch').val();
                if (event.keyCode == 13) {
                    event.preventDefault();
                    reloadReactivateModal(searchFilterVal);
                }
            }
        });
        resetButtonState();
    });
	
	function reloadReactivateModal(searchFilterVal) {
		$('#reactivateLoading').show();
        $.pjax.reload({
            type: 'POST',
            url: '/user/reactivate-user-modal',
            container: '#reactivateUserGridviewPJAX', // id to update content
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
	
	//ReactivateModal CleanFilterButton listener
    $('#reactivateUserCleanFilterButton').click(function () {
        $("#reactivateUserSearch").val("");
        var searchFilterVal = $('#reactivateUserSearch').val();
        reloadReactivateModal(searchFilterVal);
    });
</script>