<?php

namespace app\models;

use Yii;

/**
 * @property integer $ID
 * @property integer $ProjectID
 * @property integer $TaskID
 * @property string $TaskName
 * @property string $StartTime
 * @property string $EndTime
 * @property string $ProjectName
 */
class EmployeeDetailTime extends \yii\base\model
{
    const TIME_OF_DAY_MORNING = 'morning';
    const TIME_OF_DAY_AFTERNOON = 'afternoon';


	public $ID;
	public $ProjectID;
	public $TaskID;
	public $TaskName;
	public $StartTime;
	public $EndTime;
	public $TimeOfDay;
	public $TimeOfDayName;
    public $ProjectName;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ID', 'ProjectID', 'TaskID'], 'integer'],
            [['ProjectName', 'TaskName', 'StartTime', 'EndTime'], 'string'],
            [['StartTime', 'EndTime','TimeOfDay', 'TimeOfDayName'], 'safe']

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ID' => 'Entry ID',
			'ProjectID' => 'Project ID',
			'TaskID' => 'Task ID',
			'TaskName' => 'Task Name',
			'StartTime' => 'Start Time',
            'EndTime' => 'End Time',
        ];
    }
}
