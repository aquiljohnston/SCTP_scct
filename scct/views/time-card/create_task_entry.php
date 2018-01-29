<?php
/**
 * Created by PhpStorm.
 * User: tzhang
 * Date: 2018/1/25
 * Time: 16:42
 */

/* @var $this yii\web\View */

$this->title = 'Create Task Entry';
$this->params['breadcrumbs'][] = ['label' => 'Task Entry', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="time-entry-create">

    <?= $this->render('task_entry_form', [
        'model' => $model,
        'allTask' => $allTask,
        'chartOfAccountType' => $chartOfAccountType,
        'timeCardID' => $timeCardID
    ]) ?>

</div>