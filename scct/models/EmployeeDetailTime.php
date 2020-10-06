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
 */
class EmployeeDetailTime extends \yii\base\model
{

	public $ID;
	public $ProjectID;
	public $TaskID;
	public $TaskName;
	public $StartTime;
	public $EndTime;
	public $TimeOfDay;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ID', 'ProjectID', 'TaskID'], 'integer'],
            [['TaskName', 'StartTime', 'EndTime'], 'string'],
            [['StartTime', 'EndTime'], 'safe']
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
