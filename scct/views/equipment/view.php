<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use app\assets\EquipmentAsset;

//register assets
EquipmentAsset::register($this);

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
		
		<?php 
			// get approve equipment action
			$approveUrl = urldecode(Url::to(['equipment/approve', 'id' => $model["EquipmentID"]]));
		?>
		<?php if($model['EquipmentAcceptedFlag']=='Yes'){ ?>
			<?= Html::button('Accept Equipment', [
				'class' => 'btn btn-primary multiple_approve_btn',
				'disabled' => true,
				'id' => 'enable_single_approve_btn_id_equipment',
			])?>
		<?php }else{ ?>
				<?= Html::a('Accept Equipment', $approveUrl, [
					'class' => 'btn btn-primary multiple_approve_btn',
					'disabled' => false,
					'id' => 'disable_single_approve_btn_id_equipment',
					'data' => [
							   'confirm' => 'Are you sure you want to accept this item?']
				])?>
		<?php } ?>
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
			'EquipmentSCNumber',
            'EquipmentDetails',
            'EquipmentType',
            'EquipmentManufacturer',
            'EquipmentManufactureYear',
            'EquipmentCondition',
			'EquipmentStatus',
            'EquipmentMACID',
            'EquipmentModel',
            'EquipmentColor',
            'EquipmentWarrantyDetail',
            'EquipmentComment',
            'EquipmentClientID',
            'EquipmentProjectID',
            'EquipmentAnnualCalibrationDate',
            'EquipmentAssignedUserID',
			'EquipmentAcceptedFlag',
			'EquipmentAcceptedBy',
            'EquipmentCreatedByUser',
            'EquipmentCreateDate',
            'EquipmentModifiedBy',
            'EquipmentModifiedDate',
        ],
    ]) ?>

</div>
