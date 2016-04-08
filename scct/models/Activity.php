<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "ActivityTb".
 *
 * @property integer $ActivityID
 * @property string $ActivityStartTime
 * @property string $ActivityEndTime
 * @property string $ActivityTitle
 * @property string $ActivityBillingCode
 * @property integer $ActivityCode
 * @property integer $ActivityPayCode
 * @property string $ActivityCreateDate
 * @property string $ActivityCreatedBy
 * @property string $ActivityModifiedDate
 * @property string $ActivityModifiedBy
 *
 * @property MileageEntryTb[] $mileageEntryTbs
 * @property TimeEntryTb[] $timeEntryTbs
 */
class Activity extends \yii\base\model
{
	public $ActivityID;
	public $ActivityStartTime;
	public $ActivityEndTime;
	public $ActivityTitle;
	public $ActivityBillingCode;
	public $ActivityCode;
	public $ActivityPayCode;
	public $ActivityCreateDate;
	public $ActivityCreatedBy;
	public $ActivityModifiedDate;
	public $ActivityModifiedBy;
	
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ActivityStartTime', 'ActivityEndTime', 'ActivityCreateDate', 'ActivityModifiedDate'], 'safe'],
            [['ActivityTitle', 'ActivityBillingCode'], 'string'],
            [['ActivityID', 'ActivityCode', 'ActivityPayCode' , 'ActivityCreatedBy', 'ActivityModifiedBy'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ActivityID' => 'Activity ID',
            'ActivityStartTime' => 'Activity Start Time',
            'ActivityEndTime' => 'Activity End Time',
            'ActivityTitle' => 'Activity Title',
            'ActivityBillingCode' => 'Activity Billing Code',
            'ActivityCode' => 'Activity Code',
            'ActivityPayCode' => 'Activity Pay Code',
            'ActivityCreateDate' => 'Activity Create Date',
            'ActivityCreatedBy' => 'Activity Created By',
            'ActivityModifiedDate' => 'Activity Modified Date',
            'ActivityModifiedBy' => 'Activity Modified By',
        ];
    }
}
