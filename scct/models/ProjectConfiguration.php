<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "ProjectConfiguration".
 *
 * @property int $ID
 * @property int|null $ProjectID
 * @property string|null $ProjectReferenceID
 * @property string|null $CreatedDate
 * @property string|null $CreatedBy
 * @property string|null $ModifiedBy
 * @property string|null $ModifiedDate
 * @property int|null $IsEndOfDayTaskOut
 *
 * @property ProjectTb $project
 */
class ProjectConfiguration extends \yii\base\model
{
	public $ID;
	public $ProjectID;
	public $ProjectReferenceID;
	public $CreatedDate;
	public $CreatedBy;
	public $ModifiedBy;
	public $ModifiedDate;
	public $IsEndOfDayTaskOut;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ProjectID', 'IsEndOfDayTaskOut'], 'integer'],
            [['ProjectReferenceID', 'CreatedBy', 'ModifiedBy'], 'string'],
            [['CreatedDate', 'ModifiedDate'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'ID' => 'ID',
            'ProjectID' => 'Project ID',
            'ProjectReferenceID' => 'Project Reference ID',
            'CreatedDate' => 'Created Date',
            'CreatedBy' => 'Created By',
			'ModifiedBy' => 'Modified By',
            'ModifiedDate' => 'Modified Date',
            'IsEndOfDayTaskOut' => 'Is End Of Day Task Out',
        ];
    }
}
