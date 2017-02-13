<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use app\controllers\MileageCard;
use yii\widgets\LinkPager;
use yii\widgets\Pjax;
use kartik\form\ActiveForm;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Mileage Cards';
$this->params['breadcrumbs'][] = $this->title;
$pageSize = ["10" => "10", "25" => "25", "50" => "50", "100" => "100"];
?>
<div class="mileage-index">

    <h3 class="title"><?= Html::encode($this->title) ?></h3>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <div id="mileage_card_filter">
        <!-- Approve Multiple Mileage Card button -->
        <div id="mileage_card_approve_btn" class="col-sm-2 col-md-1 col-lg-1">
			<?php
            echo Html::button('Approve',
                [
                    'class' => 'btn btn-primary multiple_approve_btn',
                    'id' => 'multiple_mileage_card_approve_btn',
                ]);
            if ($week == "prior") {
                $priorSelected = "selected";
                $currentSelected = "";
            } else {
                $priorSelected = "";
                $currentSelected = "selected";
            }
            ?>
		</div>
        <div id="mileageCardDropdownContainer" class="col-sm-10 col-md-11 col-lg-11">
			<?php $form = ActiveForm::begin([
                'type' => ActiveForm::TYPE_HORIZONTAL,
                'formConfig' => ['labelSpan' => 7, 'deviceSize' => ActiveForm::SIZE_SMALL],
                'method' => 'post',
                'options' => [
                    'id' => 'MileageCardForm',
                ]
            ]); ?>
            <div id="mileageCardWeekContainer">
				<select name="week" id="mileageCardWeekSelection">
					<option value="prior" <?= $priorSelected ?>>Prior Week</option>
				<option value="current" <?= $currentSelected ?>>Current Week</option>
				</select>
				<input type="hidden" name="r" value="mileage-card/index"/>
			</div>
			<div id="mileageCardPageSizeContainer">
				<label id="mileageCardPageSizeLabel">
					<?= $form->field($model, 'pagesize')->dropDownList($pageSize, ['value' => $mileageCardPageSizeParams, 'id' => 'mileageCardPageSize'])->label("Records Per Page"); ?>
				</label>
				<input id="pageNumber" type="hidden" name="pageNumber" value="1"/>
			</div>
            <?php ActiveForm::end(); ?>
		</div>
    </div>
    <!-- General Table Layout for displaying Mileage Card Information -->
    <div id="mileageCardGridViewContainer">
        <div id="mileageCardGV" class="mileageCardForm">
            <?php Pjax::begin(['id' => 'mileageCardGridview', 'timeout' => false]) ?>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'export' => false,
                'bootstrap' => false,
                'pjax' => true,
                'summary' => '',
                'columns' => [
                    [
                        'label' => 'User First Name',
                        'attribute' => 'UserFirstName',
                        'filter' => '<input class="form-control" name="filterfirstname" value="' . Html::encode($searchModel['UserFirstName']) . '" type="text">'
                    ],
                    [
                        'label' => 'User Last Name',
                        'attribute' => 'UserLastName',
                        'filter' => '<input class="form-control" name="filterlastname" value="' . Html::encode($searchModel['UserLastName']) . '" type="text">'
                    ],
                    [
                        'label' => 'Project Name',
                        'attribute' => 'ProjectName',
                        'filter' => '<input class="form-control" name="filterprojectname" value="' . Html::encode($searchModel['ProjectName']) . '" type="text">'
                    ],
                    'MileageStartDate',
                    'MileageEndDate',
                    'SumMiles',
                    [
                        'label' => 'Approved',
                        'attribute' => 'MileageCardApprovedFlag',
                        'filter' => $approvedInput
                    ],

                    ['class' => 'kartik\grid\ActionColumn',
                        'template' => '{view}',
                        'urlCreator' => function ($action, $model, $key, $index) {
                            if ($action === 'view') {
                                $url = '/mileage-card/view?id=' . $model["MileageCardID"];
                                return $url;
                            }
                        },
                    ],
                    [
                        'class' => 'kartik\grid\CheckboxColumn',
                        'checkboxOptions' => function ($model, $key, $index, $column) {
                            return ['mileageCardId' => $model["MileageCardID"], 'approved' => $model["MileageCardApprovedFlag"], 'totalmileage' => $model["SumMiles"]];
                        }
                    ],
                ],
            ]); ?>
            <div id="MCPagination">
                <?php
                // display pagination
                echo LinkPager::widget([
                    'pagination' => $pages,
                ]);
                ?>
            </div>
            <div class="GridviewTotalNumber">
                <?php echo "Showing " . ($pages->offset + 1) . "  to " . ($pages->offset + $pages->getPageSize()) . " of " . $pages->totalCount . " entries"; ?>
            </div>
            <?php Pjax::end() ?>
        </div>
    </div>
</div>
