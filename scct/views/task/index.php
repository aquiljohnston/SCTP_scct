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
        }
    ]
];
?>
<div class="task-index">
    <div id="taskSearchContainer">
        <h3 class="title"><?= Html::encode($this->title) ?></h3>
        <?php $form = ActiveForm::begin([
            'type' => ActiveForm::TYPE_HORIZONTAL,
            'formConfig' => ['showLabels' => false, 'deviceSize' => ActiveForm::SIZE_SMALL],
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
                <?php Pjax::end() ?>
            </div>
        </div>
    </div>
</div>
