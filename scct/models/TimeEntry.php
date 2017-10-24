<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "TimeEntryTb".
 *
 * @property integer $TimeEntryID
 * @property integer $TimeEntryUserID
 * @property string  $TimeEntryStartTime
 * @property string  $TimeEntryEndTime
 * @property string  $TimeEntryDate
 * @property string  $TimeEntryActiveFlag
 * @property integer $TimeEntryTimeCardID
 * @property integer $TimeCardFK
 * @property integer $TimeEntryActivityID
 * @property string  $TimeEntryComment
 * @property string  $TimeEntryArchiveFlag
 * @property string  $TimeEntryCreateDate
 * @property string  $TimeEntryCreatedBy
 * @property string  $TimeEntryModifiedDate
 * @property string  $TimeEntryModifiedBy
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
	public $TimeEntryActiveFlag;
	public $TimeEntryTimeCardID;
	public $TimeCardFK;
	public $TimeEntryActivityID;
	public $TimeEntryComment;
	public $TimeEntryArchiveFlag;
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
            [['TimeEntryUserID', 'TimeEntryTimeCardID', 'TimeEntryActivityID', 'TimeCardFK'], 'integer'],
            [['TimeEntryComment', 'TimeEntryActiveFlag', 'TimeEntryArchiveFlag', 'TimeEntryCreatedBy', 'TimeEntryModifiedBy'], 'string']
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
			'TimeEntryActiveFlag' => 'Time Entry Active Flag',
            'TimeEntryTimeCardID' => 'Time Entry Time Card ID',
			'TimeCardFK' => 'Time Card FK',
            'TimeEntryActivityID' => 'Time Entry Activity ID',
            'TimeEntryComment' => 'Time Entry Comment',
			'TimeEntryArchiveFlag' => 'Time Entry Archive Flag',
            'TimeEntryCreateDate' => 'Time Entry Create Date',
            'TimeEntryCreatedBy' => 'Time Entry Created By',
            'TimeEntryModifiedDate' => 'Time Entry Modified Date',
            'TimeEntryModifiedBy' => 'Time Entry Modified By',
        ];
    }
}