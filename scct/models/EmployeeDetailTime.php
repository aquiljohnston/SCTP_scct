<?php

namespace app\models;

use Yii;

/**
 * @property integer $ID
 * @property integer $ProjectID
 * @property string $ProjectName
 * @property string $Task
 * @property string $StartTime
 * @property string $EndTime
 * @property string $TaskID
 */
class EmployeeDetailTime extends \yii\base\model
{
    const TIME_OF_DAY_MORNING = 'morning';
    const TIME_OF_DAY_AFTERNOON = 'afternoon';


	public $ID;
	public $ProjectID;
	public $ProjectName;
	public $Task;
	public $StartTime;
	public $EndTime;
	public $TimeOfDay;
	public $TimeOfDayName;
	public $TaskID;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ID', 'ProjectID', 'TaskID'], 'integer'],
            [['ProjectName', 'Task', 'StartTime', 'EndTime'], 'string'],
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
			'ProjectName' => 'Project Name',
			'Task' => 'Task',
			'StartTime' => 'Start Time',
            'EndTime' => 'End Time',
        ];
    }
}
