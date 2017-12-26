<?php
/**
 * Created by PhpStorm.
 * User: tzhang
 * Date: 12/19/2017
 * Time: 9:30 AM
 */

use yii\helpers\Html;
use kartik\grid\GridView;
use app\controllers\TaskController;
use kartik\form\ActiveForm;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\widgets\Pjax;

$this->title = 'Task';
$this->params['breadcrumbs'][] = $this->title;
$taskColumn = [
    [
        'label' => 'Task Name',
        'attribute' => 'TaskName',
        'headerOptions' => ['class' => 'text-center'],
        'contentOptions' => ['class' => 'text-center'],
        'format' => 'html'
    ],
    [
        'header' => 'Add Task',
        'class' => 'kartik\grid\CheckboxColumn',
        'headerOptions' => ['class' => 'text-center'],
        'contentOptions' => ['class' => 'taskCheckbox'],
        'checkboxOptions' => function ($model, $key, $index, $column) {
            /*if (empty($model['SectionNumber']))
                return ['SectionNumber' => '000', 'MapGrid' => $model['MapGrid'], 'disabled' => false];
            else
                return ['SectionNumber' => $model['SectionNumber'], 'MapGrid' => $model['MapGrid'], 'disabled' => false];*/
        }
    ]
];

$userColumn = [
    [
        'label' => 'User Name',
        'attribute' => 'UserName',
        'headerOptions' => ['class' => 'text-center'],
        'contentOptions' => ['class' => 'text-center'],
        'format' => 'html'
    ],
    [
        'header' => 'Add Surveyor',
        'class' => 'kartik\grid\CheckboxColumn',
        'headerOptions' => ['class' => 'text-center'],
        'contentOptions' => ['class' => 'userCheckbox'],
        'checkboxOptions' => function ($model, $key, $index, $column) {
            /*if (empty($model['SectionNumber']))
                return ['SectionNumber' => '000', 'MapGrid' => $model['MapGrid'], 'disabled' => false];
            else
                return ['SectionNumber' => $model['SectionNumber'], 'MapGrid' => $model['MapGrid'], 'disabled' => false];*/
        }
    ]
];
?>
<div class="task-index">
    <div id="taskSearchContainer">
        <h3 class="title"><?= Html::encode($this->title) ?></h3>
        <?php $form = ActiveForm::begin([
            'type' => ActiveForm::TYPE_HORIZONTAL,
            'formConfig' => [/*'labelSpan' => 1,*/'showLabels' => false, 'deviceSize' => ActiveForm::SIZE_SMALL],
            //'method' => 'get',
            //'action' => Url::to(['task/index']),
            'options' => [
                'id' => 'taskForm',
            ]
        ]); ?>
        <label id="taskFilter" style="width: 51%">
            <?= $form->field($model, 'taskfilter')->textInput(['id' => 'taskSearchField'])->label(false); ?>
            <?php echo Html::img('@web/logo/filter_clear_black.png', ['id' => 'taskSearchCleanFilterButton']) ?>
        </label>
        <label id="userFilter" style="width: 40%">
            <?= $form->field($model, 'userfilter')->textInput(['id' => 'userSearchField'])->label(false); ?>
            <?php echo Html::img('@web/logo/filter_clear_black.png', ['id' => 'userSearchCleanFilterButton']) ?>
        </label>
        <p style="float: right;">
            <?= Html::a('Submit', null, ['class' => 'btn btn-success', 'disabled' => 'disabled']) ?>
        </p>
        <?php ActiveForm::end(); ?>
    </div>

    <div class="container">
        <div class="row vdivide">
            <div class="col-sm-6 text-center">
                <?php Pjax::begin(['id' => 'taskGridView', 'timeout' => false]) ?>
                <?= GridView::widget([
                    'dataProvider' => $taskDataProvider,
                    'export' => false,
                    'bootstrap' => false,
                    'floatHeader' => true,
                    'id' => 'taskTable',
                    'summary' => false,
                    'columns' => $taskColumn
                ]); ?>
                <!--<div id="TaskPagination">
            <?php
                /*            echo LinkPager::widget([
                                'pagination' => $taskPages,
                            ]);
                            */?>
        </div>
        <div class="TaskGridViewTotalNumber">
            <?php /*echo "Showing " . ($taskPages->offset + 1) . " to " . ($taskPages->offset + $taskPages->getPageSize()) . " of " . $taskPages->totalCount . " entries"; */?>
        </div>-->
                <?php Pjax::end() ?>
            </div>
            <div class="col-sm-6 text-center">
                <?php Pjax::begin(['id' => 'userGridView', 'timeout' => false]) ?>
                <?= GridView::widget([
                    'dataProvider' => $userDataProvider,
                    'export' => false,
                    'bootstrap' => false,
                    'floatHeader' => true,
                    'id' => 'userTable',
                    'summary' => false,
                    'columns' => $userColumn
                ]); ?>
                <!--<div id="UserPagination">
            <?php
                /*            echo LinkPager::widget([
                                'pagination' => $userPages,
                            ]);
                            */?>
        </div>
        <div class="UserGridViewTotalNumber">
            <?php /*echo "Showing " . ($userPages->offset + 1) . " to " . ($userPages->offset + $userPages->getPageSize()) . " of " . $userPages->totalCount . " entries"; */?>
        </div>-->
                <?php Pjax::end() ?>
            </div>
        </div>
    </div>
</div>
