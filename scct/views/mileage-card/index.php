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
$this->params['download_url'] = '/mileage-card/download-mileage-card-data?' . http_build_query([
        'week' => $week
    ]);
$column = [
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
    [
        'label' => 'Start Date',
        'attribute' => 'MileageStartDate',
        'value' => function ($model) {
            return date("m/d/Y", strtotime($model['MileageStartDate']));
        }
    ],
    [
        'label' => 'End Date',
        'attribute' => 'MileageEndDate',
        'value' => function ($model) {
            return date("m/d/Y", strtotime($model['MileageEndDate']));
        }
    ],
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
            // Disable if already approved or SumHours is 0
            $disabledBoolean = strtoupper($model["MileageCardApprovedFlag"]) == "YES";
            $result = [
                'mileageCardId' => $model["MileageCardID"],
                'approved' => $model["MileageCardApprovedFlag"],
                'totalmileage' => $model["SumMiles"]
            ];
            if ($disabledBoolean) {
                $result['disabled'] = 'true';
            }

            return $result;
        }
    ],
];
?>
<div class="mileage-index">

    <h3 class="title"><?= Html::encode($this->title) ?></h3>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <div id="mileage_card_filter">
        <!-- Approve Multiple Mileage Card button -->
        <div id="mileage_card_approve_btn" class="col-xs-4 col-md-3 col-lg-2">
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
            <?php if ($pages->totalCount > 0) { ?>
                <a id="export_mileagecard_btn" class="btn btn-primary" target="_blank"
                   href="<?= $this->params['download_url']; ?>">Export</a>
            <?php } ?>
        </div>
        <div id="mileageCardDropdownContainer" class="col-xs-8 col-md-9 col-lg-10">
            <?php $form = ActiveForm::begin([
                'type' => ActiveForm::TYPE_HORIZONTAL,
                'formConfig' => ['labelSpan' => 7, 'deviceSize' => ActiveForm::SIZE_SMALL],
                'method' => 'get',
                'options' => [
                    'id' => 'MileageCardForm',
                ]
            ]); ?>
            <div id="mileageCardWeekContainer">
                <select name="weekMileageCard" id="mileageCardWeekSelection">
                    <option value="prior" <?= $priorSelected ?> >Prior Week</option>
                    <option value="current" <?= $currentSelected ?> >Current Week</option>
                </select>
            </div>
            <div id="mileageCardPageSizeContainer">
                <label id="mileageCardPageSizeLabel">
                    <?= $form->field($model, 'pagesize')->dropDownList($pageSize, ['value' => $mileageCardPageSizeParams, 'id' => 'mileageCardPageSize'])->label("Records Per Page"); ?>
                </label>
                <input id="mileageCardPageNumber" type="hidden" name="mileageCardPageNumber" value="1"/>
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
                'columns' => $column
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
