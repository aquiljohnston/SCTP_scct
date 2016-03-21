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
 * @property integer $ProjectClientID
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
	public $ProjectClientID;
	public $ProjectStartDate;
	public $ProjectEndDate;
	public $ProjectCreateDate;
	public $ProjectCreatedBy;
	public $ProjectModifiedDate;
	public $ProjectModifiedBy;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ProjectName'], 'required'],
            [['ProjectName', 'ProjectDescription', 'ProjectNotes', 'ProjectType', 'ProjectCreatedBy', 'ProjectModifiedBy'], 'string'],
            [['ProjectStatus', 'ProjectClientID'], 'integer'],
            [['ProjectStartDate', 'ProjectEndDate', 'ProjectCreateDate', 'ProjectModifiedDate'], 'safe']
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
            'ProjectClientID' => 'Project Client ID',
            'ProjectStartDate' => 'Project Start Date',
            'ProjectEndDate' => 'Project End Date',
            'ProjectCreateDate' => 'Project Create Date',
            'ProjectCreatedBy' => 'Project Created By',
            'ProjectModifiedDate' => 'Project Modified Date',
            'ProjectModifiedBy' => 'Project Modified By',
        ];
    }
}
