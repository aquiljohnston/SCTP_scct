<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\time-card */

//$this->title = $model->TimeCardID;
$this->params['breadcrumbs'][] = ['label' => 'Time Cards', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="timecard-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
		<?= Html::a('Back', ['index'], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Update', ['update', 'id' => $model['TimeCardID']], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model['TimeCardID']], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'delete',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'TimeCardID',
            'TimeCardStartDate',
            'TimeCardEndDate',
            'TimeCardHoursWorked',
            'TimeCardProjectID',
            'TimeCardTechID',
            'TimeCardApproved:datetime',
            'TimeCardSupervisorName',
            'TimeCardComment',
            'TimeCardCreateDate',
            'TimeCardCreatedBy',
            'TimeCardModifiedDate',
            'TimeCardModifiedBy',
        ],
    ]) ?>

</div>
