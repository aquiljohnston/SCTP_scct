<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "TimeEntryTb".
 *
 * @property integer $TimeEntryID
 * @property integer $TimeEntryUserID
 * @property string $TimeEntryStartTime
 * @property string $TimeEntryEndTime
 * @property string $TimeEntryDate
 * @property integer $TimeEntryMinutes
 * @property string $TimeEntryTimeCardID
 * @property integer $TimeEntryActivityID
 * @property string $TimeEntryComment
 * @property string $TimeEntryCreateDate
 * @property string $TimeEntryCreatedBy
 * @property string $TimeEntryModifiedDate
 * @property string $TimeEntryModifiedBy
 *
 * @property ActivityTb $timeEntryActivity
 * @property TimeCardTb $timeEntryTimeCard
 */
class TimeEntry extends \yii\base\model
{

	public $TimeEntryID;
	public $TimeEntryUserID;
	public $TimeEntryStartTime;
	public $TimeEntryEndTime;
	public $TimeEntryDate;
	public $TimeEntryMinutes;
	public $TimeEntryTimeCardID;
	public $TimeEntryActivityID;
	public $TimeEntryComment;
	public $TimeEntryCreateDate;
	public $TimeEntryCreatedBy;
	public $TimeEntryModifiedDate;
	public $TimeEntryModifiedBy;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['TimeEntryStartTime', 'TimeEntryEndTime', 'TimeEntryDate', 'TimeEntryCreateDate', 'TimeEntryModifiedDate'], 'safe'],
            [['TimeEntryID', 'TimeEntryUserID', 'TimeEntryMinutes', 'TimeEntryTimeCardID', 'TimeEntryActivityID', 'TimeEntryCreatedBy', 'TimeEntryModifiedBy'], 'integer'],
            [['TimeEntryComment'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'TimeEntryID' => 'Time Entry ID',
			'TimeEntryUserID' => 'Time Entry User ID',
            'TimeEntryStartTime' => 'Time Entry Start Time',
            'TimeEntryEndTime' => 'Time Entry End Time',
            'TimeEntryDate' => 'Time Entry Date',
			'TimeEntryMinutes' => 'Time Entry Minutes',
            'TimeEntryTimeCardID' => 'Time Entry Time Card ID',
            'TimeEntryActivityID' => 'Time Entry Activity ID',
            'TimeEntryComment' => 'Time Entry Comment',
            'TimeEntryCreateDate' => 'Time Entry Create Date',
            'TimeEntryCreatedBy' => 'Time Entry Created By',
            'TimeEntryModifiedDate' => 'Time Entry Modified Date',
            'TimeEntryModifiedBy' => 'Time Entry Modified By',
        ];
    }
}