<?php

namespace app\models;

use Yii;

/**
 * @property integer $EntryID
 * @property integer $CardID
 * @property string $Date
 * @property string $StartTime
 * @property string $EndTime
 * @property double $StartingMileage
 * @property double $EndingMileage
 * @property double $PersonalMiles
 * @property double $AdminMiles
 */
class MileageEntryTask extends \yii\base\model
{

	public $EntryID;
	public $CardID;
	public $Date;
	public $StartTime;
	public $EndTime;
	public $StartingMileage;
	public $EndingMileage;
	public $PersonalMiles;
	public $AdminMiles;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
			[['EntryID', 'StartingMileage', 'EndingMileage', 'PersonalMiles', 'AdminMiles','StartTime', 'EndTime', 'CardID', 'Date'], 'required'],
			[['EntryID', 'CardID'], 'integer'],
            [['StartingMileage', 'EndingMileage', 'PersonalMiles', 'AdminMiles'], 'number'],
            [['StartTime', 'EndTime', 'Date'], 'string', 'max'=>32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'EntryID' => 'Entry ID',
            'CardID' => 'Card ID',
            'Date' => 'Date',
			'StartTime' => 'Start Time',
            'EndTime' => 'End Time',
            'StartingMileage' => 'Starting Mileage',
			'EndingMileage' => 'Ending Mileage',
			'PersonalMiles' => 'Personal Miles',
			'AdminMiles' => 'Admin Miles',
        ];
    }
}
