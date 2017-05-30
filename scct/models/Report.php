<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "rReport".
 *
 * @property integer $ReportID
 * @property string $ReportUID
 * @property integer $ProjectID
 * @property string $CreatedUserUID
 * @property string $ModifiedUserUID
 * @property string $CreateDTLT
 * @property string $ModifiedDTLT
 * @property string $InactiveDTLT
 * @property string $Comments
 * @property integer $Revision
 * @property integer $ActiveFlag
 * @property string $ReportDisplayName
 * @property string $ReportDate
 * @property string $ReportInactiveDate
 * @property string $ReportType
 * @property string $ReportSPName
 * @property string $ReportDescription
 * @property integer $ReportSortSeq
 * @property string $Parm
 * @property integer $ParmInspectorFlag
 * @property integer $ParmDropDownFlag
 * @property integer $ParmDateOverrideFlag
 * @property integer $ParmBetweenDateFlag
 * @property integer $ParmDateFlag
 * @property integer $ExportFlag
 */
class Report extends \yii\base\model
{
public $ReportID;
public $ReportUID;
public $ProjectID;
public $CreatedUserUID;
public $ModifiedUserUID;
public $CreateDTLT;
public $ModifiedDTLT;
public $InactiveDTLT;
public $Comments;
public $Revision;
public $ActiveFlag;
public $ReportDisplayName;
public $ReportDate;
public $ReportInactiveDate;
public $ReportType;
public $ReportSPName;
public $ReportDescription;
public $ReportSortSeq;
public $Parm;
public $ParmInspectorFlag;
public $ParmDropDownFlag;
public $ParmDateOverrideFlag;
public $ParmBetweenDateFlag;
public $ParmDateFlag;
public $ExportFlag;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ReportUID', 'ProjectID', 'CreatedUserUID', 'ModifiedUserUID', 'Revision', 'ActiveFlag'], 'required'],
            [['ReportUID', 'CreatedUserUID', 'ModifiedUserUID', 'Comments', 'ReportDisplayName', 'ReportType', 'ReportSPName', 'ReportDescription', 'Parm'], 'string'],
            [['ProjectID', 'Revision', 'ActiveFlag', 'ReportSortSeq', 'ParmInspectorFlag', 'ParmDropDownFlag', 'ParmDateOverrideFlag', 'ParmBetweenDateFlag', 'ParmDateFlag', 'ExportFlag'], 'integer'],
            [['CreateDTLT', 'ModifiedDTLT', 'InactiveDTLT', 'ReportDate', 'ReportInactiveDate'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ReportID' => 'Report ID',
            'ReportUID' => 'Report Uid',
            'ProjectID' => 'Project ID',
            'CreatedUserUID' => 'Created User Uid',
            'ModifiedUserUID' => 'Modified User Uid',
            'CreateDTLT' => 'Create Dtlt',
            'ModifiedDTLT' => 'Modified Dtlt',
            'InactiveDTLT' => 'Inactive Dtlt',
            'Comments' => 'Comments',
            'Revision' => 'Revision',
            'ActiveFlag' => 'Active Flag',
            'ReportDisplayName' => 'Report Display Name',
            'ReportDate' => 'Report Date',
            'ReportInactiveDate' => 'Report Inactive Date',
            'ReportType' => 'Report Type',
            'ReportSPName' => 'Report Spname',
            'ReportDescription' => 'Report Description',
            'ReportSortSeq' => 'Report Sort Seq',
            'Parm' => 'Parm',
            'ParmInspectorFlag' => 'Parm Inspector Flag',
            'ParmDropDownFlag' => 'Parm Drop Down Flag',
            'ParmDateOverrideFlag' => 'Parm Date Override Flag',
            'ParmBetweenDateFlag' => 'Parm Between Date Flag',
            'ParmDateFlag' => 'Parm Date Flag',
            'ExportFlag' => 'Export Flag',
        ];
    }
}
