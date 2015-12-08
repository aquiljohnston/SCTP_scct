<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\EquipmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Equipments';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="equipment-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Equipment', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'EquipmentID',
            'EquipmentName',
            'EquipmentSerialNumber',
            'EquipmentDetails',
            'EquipmentType',
            // 'EquipmentManufacturer',
            // 'EquipmentManufactureYear',
            // 'EquipmentCondition',
            // 'EquipmentMACID',
            // 'EquipmentModel',
            // 'EquipmentColor',
            // 'EquipmentWarrantyDetail',
            // 'EquipmentComment',
            // 'EquipmentClientID',
            // 'EquipmentProjectID',
            // 'EquipmentAnnualCalibrationDate',
            // 'EquipmentAnnualCalibrationStatus',
            // 'EquipmentAssignedUserID',
            // 'EquipmentCreatedByUser',
            // 'EquipmentCreateDate',
            // 'EquipmentModifiedBy',
            // 'EquipmentModifiedDate',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
