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
	public $EquipmentAnnualCalibrationStatus;
	public $EquipmentAssignedUserID;
	public $EquipmentAcceptedFlag;
	public $EquipmentAcceptedBy;
	public $EquipmentCreatedByUser;
	public $EquipmentCreateDate;
	public $EquipmentModifiedBy;
	public $EquipmentModifiedDate;
	public $EquipmentModificationReason;


	const MAX_NAME_LENGTH = 100;
	const MAX_SERIAL_NUMBER_LENGTH = 50;
	const MAX_SC_NUMBER_LENGTH = 50;
	const MAX_DETAILS_LENGTH = 100;
	const MAX_TYPE_LENGTH = 100;
	const MAX_MANUFACTURER_LENGTH = 100;
	const MAX_MANUFACTURE_YEAR_LENGTH = 4; // TODO: Increase this before 10,000 AD
    const MAX_CONDITION_LENGTH = 100;
    const MAX_STATUS_LENGTH = 50;
    const MAX_MACID_LENGTH = 100;
    const MAX_MODEL_LENGTH = 100;
    const MAX_COLOR_LENGTH = 50;
    const MAX_WARRANTY_DETAIL_LENGTH = 100;
    const MAX_COMMENT_LENGTH = 100;
    const MAX_ANNUAL_CALIBRATION_STATUS_LENGTH = 10;
    const MAX_MODIFICATION_REASON_LENGTH = 250;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
			[['EquipmentName', 'EquipmentSerialNumber', 'EquipmentSCNumber', 'EquipmentDetails', 'EquipmentType', 'EquipmentManufacturer', 'EquipmentManufactureYear', 'EquipmentCondition', 'EquipmentStatus', 'EquipmentMACID', 'EquipmentModel', 'EquipmentColor', 'EquipmentWarrantyDetail', 'EquipmentComment', 'EquipmentAcceptedFlag', 'EquipmentAcceptedBy', 'EquipmentModificationReason'], 'string'],
            [['EquipmentID', 'EquipmentClientID', 'EquipmentProjectID', 'EquipmentAssignedUserID', 'EquipmentCreatedByUser', 'EquipmentModifiedBy'], 'integer'],
            [['EquipmentAnnualCalibrationDate', 'EquipmentCreateDate', 'EquipmentModifiedDate'], 'safe'],
            ['EquipmentName', 'string', 'max' => self::MAX_NAME_LENGTH],
            ['EquipmentSerialNumber', 'string', 'max' => self::MAX_SERIAL_NUMBER_LENGTH],
            ['EquipmentSCNumber', 'string', 'max' => self::MAX_SC_NUMBER_LENGTH],
            ['EquipmentDetails', 'string', 'max' => self::MAX_DETAILS_LENGTH],
            ['EquipmentType', 'string', 'max' => self::MAX_TYPE_LENGTH],
            ['EquipmentManufacturer', 'string', 'max' => self::MAX_MANUFACTURER_LENGTH],
            ['EquipmentManufactureYear', 'string', 'max' => self::MAX_MANUFACTURE_YEAR_LENGTH],
            ['EquipmentCondition', 'string', 'max' => self::MAX_CONDITION_LENGTH],
            ['EquipmentStatus', 'string', 'max' => self::MAX_STATUS_LENGTH],
            ['EquipmentMACID', 'string', 'max' => self::MAX_MACID_LENGTH],
            ['EquipmentModel', 'string', 'max' => self::MAX_MODEL_LENGTH],
            ['EquipmentColor', 'string', 'max' => self::MAX_COLOR_LENGTH],
            ['EquipmentWarrantyDetail', 'string', 'max' => self::MAX_WARRANTY_DETAIL_LENGTH],
            ['EquipmentComment', 'string', 'max' => self::MAX_COMMENT_LENGTH],
            ['EquipmentAnnualCalibrationStatus', 'string', 'max' => self::MAX_ANNUAL_CALIBRATION_STATUS_LENGTH],
            ['EquipmentModificationReason', 'string', 'max' => self::MAX_MODIFICATION_REASON_LENGTH]
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
			'EquipmentModificationReason' => 'Equipment Modification Reason',
        ];
    }
}
