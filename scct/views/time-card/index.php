<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\widgets\Pjax;
use app\controllers\TimeCard;
use kartik\form\ActiveForm;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Time Cards';
$this->params['breadcrumbs'][] = $this->title;
$pageSize = ["10" => "10", "25" => "25", "50" => "50", "100" => "100"];
$this->params['download_url'] = '/time-card/download-time-card-data?' . http_build_query([
        'week' => $week
    ]);
?>

<div class="timecard-index">

    <h3 class="title"><?= Html::encode($this->title) ?></h3>
    <div id="timecard_filter">
        <div id="multiple_time_card_approve_btn" class="col-xs-4 col-md-3 col-lg-2">
            <?php
            echo Html::button('Approve',
                [
                    'class' => 'btn btn-primary multiple_approve_btn',
                    'id' => 'multiple_approve_btn_id',
                ]);

            if ($week == "prior") {
                $priorSelected = "selected";
                $currentSelected = "";
            } else {
                $priorSelected = "";
                $currentSelected = "selected";
            }
            ?>
            <?php
            if ($pages->totalCount > 0) {
                ?>
                <a id="export_timecard_btn" class="btn btn-primary" target="_blank"
                   href="<?= $this->params['download_url']; ?>">Export</a>
            <?php } ?>

        </div>
        <div id="timeCardDropdownContainer" class="col-xs-8 col-md-9 col-lg-10">

            <?php $form = ActiveForm::begin([
                'type' => ActiveForm::TYPE_HORIZONTAL,
                'formConfig' => ['labelSpan' => 7, 'deviceSize' => ActiveForm::SIZE_SMALL],
                'method' => 'post',
                'options' => [
                    'id' => 'TimeCardForm',
                ]

            ]); ?>
            <div id="timeCardWeekContainer">
                <select name="week" id="weekSelection"<!--onchange="this.form.submit()-->">
                <option value="prior" <?= $priorSelected ?> >Prior Week</option>
                <option value="current" <?= $currentSelected ?> >Current Week</option>
                </select>
                <input type="hidden" name="r" value="time-card/index"/>
            </div>
            <div id="timeCardPageSizeLabelContainer">
                <label id="timeCardPageSizeLabel">
                    <?= $form->field($model, 'pagesize')->dropDownList($pageSize, ['value' => $timeCardPageSizeParams, 'id' => 'timeCardPageSize'])->label("Records Per Page"); ?>
                </label>
                <input id="pageNumber" type="hidden" name="pageNumber" value="1"/>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
    <div id="timeCardGridViewContainer">
        <div id="timeCardGV" class="timeCardForm">
            <?php Pjax::begin(['id' => 'timeCardGridview', 'timeout' => false]) ?>
            <?= GridView::widget([
                'id' => 'GridViewForTimeCard',
                'dataProvider' => $dataProvider,
                'export' => false,
                'bootstrap' => false,
                'filterModel' => $searchModel,
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
                    [
                        'label' => 'Start Date',
                        'attribute' => 'TimeCardStartDate',
                        'value' => function ($model) {
                            return date("m/d/Y", strtotime($model['TimeCardStartDate']));
                        }
                    ],
                    [
                        'label' => 'End Date',
                        'attribute' => 'TimeCardEndDate',
                        'value' => function ($model) {
                            return date("m/d/Y", strtotime($model['TimeCardEndDate']));
                        }
                    ],
                    'SumHours',
                    [
                        'label' => 'Approved',
                        'attribute' => 'TimeCardApprovedFlag',
                        'filter' => $approvedInput
                    ],
                    ['class' => 'kartik\grid\ActionColumn',
                        'template' => '{view}', // does not include delete
                        'urlCreator' => function ($action, $model, $key, $index) {
                            if ($action === 'view') {
                                $url = '/time-card/view?id=' . $model["TimeCardID"];
                                return $url;
                            }
                        },
                        'buttons' => [
                            // Currently unused due to template string above
                            'delete' => function ($url, $model, $key) {
                                $url = '/time-card/delete?id=' . $model["TimeCardID"];
                                $options = [
                                    'title' => Yii::t('yii', 'Delete'),
                                    'aria-label' => Yii::t('yii', 'Delete'),
                                    'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                    'data-method' => 'Delete',
                                    'data-pjax' => '0',
                                ];
                                return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, $options);
                            },
                        ],
                    ],
                    [
                        'class' => 'kartik\grid\CheckboxColumn',
                        'checkboxOptions' => function ($model, $key, $index, $column) {
                            return ['timecardid' => $model["TimeCardID"], 'approved' => $model["TimeCardApprovedFlag"], 'totalworkhours' => $model["SumHours"]];
                        }
                        /*'pageSummary' => true,
                        'rowSelectedClass' => GridView::TYPE_SUCCESS,
                        'contentOptions'=>['style'=>'width: 0.5%'],*/
                    ],
                ],
            ]); ?>
            <div id="TCPagination">
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
