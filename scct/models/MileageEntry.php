<?php

namespace app\models;

use Yii;

/**
 * @property integer $EntryID
 * @property string $StartTime
 * @property string $EndTime
 * @property number $StartingMileage
 * @property number $EndingMileage
 * @property number $PersonalMiles
 * @property number $AdminMiles
 */
class MileageEntry extends \yii\base\model
{

	public $EntryID;
	public $StartTime;
	public $EndTime;
	public $StartingMileage;
	public $EndingMileage;
	public $PersonalMiles;
	public $AdminMiles;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['MileageEntryStartingMileage', 'MileageEntryEndingMileage'], 'number'],
            [['MileageEntryID', 'MileageEntryType', 'MileageEntryMileageCardID', 'MileageEntryActivityID', 'MileageEntryStatus', 'MileageEntryUserID', 'MileageEntryCreatedBy', 'MileageEntryModifiedBy'], 'integer'],
            [['MileageEntryApprovedBy', 'MileageEntryComment'], 'string'],
            [['MileageEntryStartDate', 'MileageEntryEndDate',  'MileageEntryCreateDate', 'MileageEntryModifiedDate'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'EntryID' => 'Entry ID',
			'StartTime' => 'Start Time',
            'EndTime' => 'End Time',
            'StartingMileage' => 'Starting Mileage',
			'EndingMileage' => 'Ending Mileage',
			'PersonalMiles' => 'Personal Miles',
			'AdminMiles' => 'Admin Miles',
        ];
    }
}
