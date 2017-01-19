<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "ProjectTb".
 *
 * @property integer $ProjectID
 * @property string $ProjectName
 * @property string $ProjectDescription
 * @property string $ProjectNotes
 * @property string $ProjectType
 * @property integer $ProjectStatus
 * @property string $ProjectUrlPrefix
 * @property integer $ProjectClientID
 * @property integer $ProjectState
 * @property string $ProjectStartDate
 * @property string $ProjectEndDate
 * @property string $ProjectCreateDate
 * @property string $ProjectCreatedBy
 * @property string $ProjectModifiedDate
 * @property string $ProjectModifiedBy
 *
 * @property ProjectUserTb[] $projectUserTbs
 * @property ProjectOQRequirementstb[] $projectOQRequirementstbs
 * @property ClientTb $projectClient
 */
class Project extends \yii\base\model
{
	
	public $ProjectID;
	public $ProjectName;
	public $ProjectDescription;
	public $ProjectNotes;
	public $ProjectType;
	public $ProjectStatus;
	public $ProjectUrlPrefix;
	public $ProjectClientID;
	public $ProjectState;
	public $ProjectStartDate;
	public $ProjectEndDate;
	public $ProjectCreateDate;
	public $ProjectCreatedBy;
	public $ProjectModifiedDate;
	public $ProjectModifiedBy;

	// Constants defined to avoid "magic numbers"
    // Constant values taken from the database definition
    const MAX_NAME_LENGTH = 100;
    const MAX_DESCRIPTION_LENGTH = 255;
    const MAX_NOTES_LENGTH = 255;
    const MAX_TYPE_LENGTH = 50;
    const MAX_STATE_LENGTH = 25;
    const MAX_PREFIX_LENGTH = 10;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ProjectName', 'ProjectUrlPrefix'], 'required'],
            [['ProjectName', 'ProjectDescription', 'ProjectNotes', 'ProjectType', 'ProjectState', 'ProjectUrlPrefix'], 'string'],
            [['ProjectID', 'ProjectStatus', 'ProjectClientID', 'ProjectCreatedBy', 'ProjectModifiedBy'], 'integer'],
            [['ProjectStartDate', 'ProjectEndDate', 'ProjectCreateDate', 'ProjectModifiedDate'], 'safe'],
            ['ProjectUrlPrefix', 'string', 'max' => self::MAX_PREFIX_LENGTH],
            ['ProjectName', 'string', 'max' => self::MAX_NAME_LENGTH],
            ['ProjectDescription', 'string', 'max' => self::MAX_DESCRIPTION_LENGTH],
            ['ProjectNotes', 'string', 'max' => self::MAX_NOTES_LENGTH],
            ['ProjectType', 'string', 'max' => self::MAX_TYPE_LENGTH],
            ['ProjectState', 'string', 'max' => self::MAX_STATE_LENGTH]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ProjectID' => 'Project ID',
            'ProjectName' => 'Project Name',
            'ProjectDescription' => 'Project Description',
            'ProjectNotes' => 'Project Notes',
            'ProjectType' => 'Project Type',
            'ProjectStatus' => 'Project Status',
			'ProjectUrlPrefix' => 'Project Url Prefix',
            'ProjectClientID' => 'Project Client ID',
			'ProjectState' => 'Project State',
            'ProjectStartDate' => 'Project Start Date',
            'ProjectEndDate' => 'Project End Date',
            'ProjectCreateDate' => 'Project Create Date',
            'ProjectCreatedBy' => 'Project Created By',
            'ProjectModifiedDate' => 'Project Modified Date',
            'ProjectModifiedBy' => 'Project Modified By',
        ];
    }
}
