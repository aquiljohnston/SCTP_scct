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
 */
class EmployeeDetailTime extends \yii\base\model
{

	public $ID;
	public $ProjectID;
	public $ProjectName;
	public $Task;
	public $StartTime;
	public $EndTime;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ID', 'ProjectID'], 'integer'],
            [['ProjectName', 'Task', 'StartTime', 'EndTime'], 'string'],
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
			'ProjectName' => 'Project Name',
			'Task' => 'Task',
			'StartTime' => 'Start Time',
            'EndTime' => 'End Time',
        ];
    }
}
