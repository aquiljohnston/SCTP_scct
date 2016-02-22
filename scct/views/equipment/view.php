<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\equipment */

//$this->title = $model->EquipmentName;
$this->params['breadcrumbs'][] = ['label' => 'Equipments', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="equipment-view">

    <h1 class="title"><?= Html::encode($this->title) ?></h1>

    <p>
		<?= Html::a('Back', ['index'], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Update', ['update', 'id' => $model['EquipmentID']], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model['EquipmentID']], [
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
