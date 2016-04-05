<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "EquipmentTb".
 *
 * @property string $EquipmentID
 * @property string $EquipmentName
 * @property string $EquipmentSerialNumber
 * @property string $EquipmentSCNumber
 * @property string $EquipmentDetails
 * @property string $EquipmentType
 * @property string $EquipmentManufacturer
 * @property string $EquipmentManufactureYear
 * @property string $EquipmentCondition
 * @property string $EquipmentStatus
 * @property string $EquipmentMACID
 * @property string $EquipmentModel
 * @property string $EquipmentColor
 * @property string $EquipmentWarrantyDetail
 * @property string $EquipmentComment
 * @property integer $EquipmentClientID
 * @property integer $EquipmentProjectID
 * @property string $EquipmentAnnualCalibrationDate
 * @property string $EquipmentAnnualCalibrationStatus
 * @property string $EquipmentAssignedUserID
 * @property string $EquipmentAcceptedFlag
 * @property string $EquipmentAcceptedBy
 * @property string $EquipmentCreatedByUser
 * @property string $EquipmentCreateDate
 * @property string $EquipmentModifiedBy
 * @property string $EquipmentModifiedDate
 *
 * @property ClientTb $equipmentClient
 * @property UserTb $equipmentAssignedUser
 */
class Equipment extends \yii\base\model
{
	public $EquipmentID;
	public $EquipmentName;
	public $EquipmentSerialNumber;
	public $EquipmentSCNumber;
	public $EquipmentDetails;
	public $EquipmentType;
	public $EquipmentManufacturer;
	public $EquipmentManufactureYear;
	public $EquipmentCondition;
	public $EquipmentStatus;
	public $EquipmentMACID;
	public $EquipmentModel;
	public $EquipmentColor;
	public $EquipmentWarrantyDetail;
	public $EquipmentComment;
	public $EquipmentClientID;
	public $EquipmentProjectID;
	public $EquipmentAnnualCalibrationDate;
	public $EquipmentAssignedUserID;
	public $EquipmentAcceptedFlag;
	public $EquipmentAcceptedBy;
	public $EquipmentCreatedByUser;
	public $EquipmentCreateDate;
	public $EquipmentModifiedBy;
	public $EquipmentModifiedDate;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['EquipmentName', 'EquipmentSerialNumber', 'EquipmentSCNumber', 'EquipmentDetails', 'EquipmentType', 'EquipmentManufacturer', 'EquipmentManufactureYear', 'EquipmentCondition', 'EquipmentStatus', 'EquipmentMACID', 'EquipmentModel', 'EquipmentColor', 'EquipmentWarrantyDetail', 'EquipmentComment', 'EquipmentCreatedByUser', 'EquipmentModifiedBy', 'EquipmentAcceptedFlag', 'EquipmentAcceptedBy'], 'string'],
            [['EquipmentID', 'EquipmentClientID', 'EquipmentAssignedUserID'], 'integer'],
            [['EquipmentAnnualCalibrationDate', 'EquipmentCreateDate', 'EquipmentModifiedDate', 'EquipmentProjectID'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'EquipmentID' => 'Equipment ID',
            'EquipmentName' => 'Equipment Name',
            'EquipmentSerialNumber' => 'Equipment Serial Number',
			'EquipmentSCNumber' => 'Equipment SC Number',
            'EquipmentDetails' => 'Equipment Details',
            'EquipmentType' => 'Equipment Type',
            'EquipmentManufacturer' => 'Equipment Manufacturer',
            'EquipmentManufactureYear' => 'Equipment Manufacture Year',
            'EquipmentCondition' => 'Equipment Condition',
			'EquipmentStatus' => 'Equipment Status',
            'EquipmentMACID' => 'Equipment Macid',
            'EquipmentModel' => 'Equipment Model',
            'EquipmentColor' => 'Equipment Color',
            'EquipmentWarrantyDetail' => 'Equipment Warranty Detail',
            'EquipmentComment' => 'Equipment Comment',
            'EquipmentClientID' => 'Equipment Client ID',
            'EquipmentProjectID' => 'Equipment Project ID',
            'EquipmentAnnualCalibrationDate' => 'Equipment Annual Calibration Date',
            'EquipmentAssignedUserID' => 'Equipment Assigned User ID',
			'EquipmentAcceptedFlag' => 'Equipment Accepted Flag',
			'EquipmentAcceptedBy' => 'Equipment Accepted By',
            'EquipmentCreatedByUser' => 'Equipment Created By User',
            'EquipmentCreateDate' => 'Equipment Create Date',
            'EquipmentModifiedBy' => 'Equipment Modified By',
            'EquipmentModifiedDate' => 'Equipment Modified Date',
        ];
    }
}
