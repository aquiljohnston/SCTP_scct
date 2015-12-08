<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\MileageCard */

$this->title = $model->MileageCardID;
$this->params['breadcrumbs'][] = ['label' => 'Mileage Cards', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mileage-card-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->MileageCardID], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->MileageCardID], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'MileageCardID',
            'MileageCardEmpID',
            'MileageCardTechID',
            'MileageCardProjectID',
            'MileageCardType',
            'MileageCardAppStatus',
            'MileageCardCreateDate',
            'MileageCardCreatedBy',
            'MileageCardModifiedDate',
            'MileageCardModifiedBy',
        ],
    ]) ?>

</div>
