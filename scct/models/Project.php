<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "ProjectTb".
 *
 * @property string $ProjectID
 * @property string $ProjectName
 * @property string $ProjectDescription
 * @property string $ProjectNotes
 * @property string $ProjectType
 * @property integer $ProjectStatus
 * @property integer $ProjectClientID
 * @property string $ProjectStartDate
 * @property string $ProjectEndDate
 *
 * @property ProjectEmployeeTb[] $projectEmployeeTbs
 * @property ProjectUserTb[] $projectUserTbs
 * @property ProjectOQRequirementstb[] $projectOQRequirementstbs
 * @property ClientTb $projectClient
 * @property TimeCardTb[] $timeCardTbs
 */
class Project extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ProjectTb';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ProjectName'], 'required'],
            [['ProjectName', 'ProjectDescription', 'ProjectNotes', 'ProjectType'], 'string'],
            [['ProjectStatus', 'ProjectClientID'], 'integer'],
            [['ProjectStartDate', 'ProjectEndDate'], 'safe']
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
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProjectEmployeeTbs()
    {
        return $this->hasMany(ProjectEmployeeTb::className(), ['PE_ProjectID' => 'ProjectID']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProjectUserTbs()
    {
        return $this->hasMany(ProjectUserTb::className(), ['ProjUserProjectID' => 'ProjectID']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProjectOQRequirementstbs()
    {
        return $this->hasMany(ProjectOQRequirementstb::className(), ['ProjectOQRequirementsProjectID' => 'ProjectID']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProjectClient()
    {
        return $this->hasOne(ClientTb::className(), ['ClientID' => 'ProjectClientID']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTimeCardTbs()
    {
        return $this->hasMany(TimeCardTb::className(), ['TimeCardProjectID' => 'ProjectID']);
    }
}
