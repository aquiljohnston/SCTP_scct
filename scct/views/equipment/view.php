<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\equipment */

//$this->title = $model->EquipmentID;
$this->params['breadcrumbs'][] = ['label' => 'Equipments', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="equipment-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'EquipmentSerialNumber' => $model->EquipmentSerialNumber], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'EquipmentSerialNumber' => $model->EquipmentSerialNumber], [
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
            'EquipmentID',
            'EquipmentName',
            'EquipmentSerialNumber',
            'EquipmentDetails',
            'EquipmentType',
            'EquipmentManufacturer',
            'EquipmentManufactureYear',
            'EquipmentCondition',
            'EquipmentMACID',
            'EquipmentModel',
            'EquipmentColor',
            'EquipmentWarrantyDetail',
            'EquipmentComment',
            'EquipmentClientID',
            'EquipmentProjectID',
            'EquipmentAnnualCalibrationDate',
            'EquipmentAnnualCalibrationStatus',
            'EquipmentAssignedUserID',
            'EquipmentCreatedByUser',
            'EquipmentCreateDate',
            'EquipmentModifiedBy',
            'EquipmentModifiedDate',
        ],
    ]) ?>

</div>
