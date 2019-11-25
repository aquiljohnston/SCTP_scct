<?php

namespace app\models;

use Yii;

/**
 * @property integer $ProjectID
 * @property integer $UserID
 * @property string $CreatedDateTime
 * @property number $COA
 * @property number $Quantity
 */
class Expense extends \yii\base\model
{
	public $ProjectID;
	public $UserID;
	public $CreatedDateTime;
	public $ChargeAccount;
	public $Quantity;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
			[['ProjectID', 'UserID', 'CreatedDateTime', 'ChargeAccount', 'Quantity'], 'required'],
			[['ProjectID', 'UserID'], 'integer'],
            [['ChargeAccount', 'Quantity'], 'number'],
            [['CreatedDateTime'], 'string', 'max'=>32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ProjectID' => 'Project ID',
            'UserID' => 'User ID',
            'CreatedDateTime' => 'Created Date Time',
			'ChargeAccount' => 'Charge Account',
            'Quantity' => 'Quantity',
        ];
    }
}
