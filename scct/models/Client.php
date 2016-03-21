<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "ClientTb".
 *
 * @property integer $ClientID
 * @property integer $ClientAccountID
 * @property string $ClientName
 * @property string $ClientContactTitle
 * @property string $ClientContactFName
 * @property string $ClientContactMI
 * @property string $ClientContactLName
 * @property string $ClientPhone
 * @property string $ClientEmail
 * @property string $ClientAddr1
 * @property string $ClientAddr2
 * @property string $ClientCity
 * @property string $ClientState
 * @property string $ClientZip4
 * @property string $ClientTerritory
 * @property integer $ClientActiveFlag
 * @property integer $ClientDivisionsFlag
 * @property string $ClientComment
 * @property string $ClientCreateDate
 * @property string $ClientCreatorUserID
 * @property string $ClientModifiedDate
 * @property string $ClientModifiedBy
 *
 * @property EquipmentTb[] $equipmentTbs
 * @property ProjectTb[] $projectTbs
 */
class Client extends \yii\base\model
{
	
	public $ClientID;
	public $ClientAccountID;
	public $ClientName;
	public $ClientContactTitle;
	public $ClientContactFName;
	public $ClientContactMI;
	public $ClientContactLName;
	public $ClientPhone;
	public $ClientEmail;
	public $ClientAddr1;
	public $ClientAddr2;
	public $ClientCity;
	public $ClientState;
	public $ClientZip4;
	public $ClientTerritory;
	public $ClientActiveFlag;
	public $ClientDivisionsFlag;
	public $ClientComment;
	public $ClientCreateDate;
	public $ClientCreatorUserID;
	public $ClientModifiedDate;
	public $ClientModifiedBy;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ClientName'], 'required'],
            [['ClientName', 'ClientContactTitle', 'ClientContactFName', 'ClientContactMI', 'ClientContactLName', 'ClientPhone', 'ClientEmail', 'ClientAddr1', 'ClientAddr2', 'ClientCity', 'ClientState', 'ClientZip4', 'ClientTerritory', 'ClientComment', 'ClientCreatorUserID', 'ClientModifiedBy'], 'string'],
            [['ClientID', 'ClientActiveFlag', 'ClientDivisionsFlag', 'ClientAccountID'], 'integer'],
            [['ClientCreateDate', 'ClientModifiedDate'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ClientID' => 'Client ID',
			'ClientAccountID' => 'Client Account ID',
            'ClientName' => 'Client Name',
            'ClientContactTitle' => 'Client Contact Title',
            'ClientContactFName' => 'Client Contact Fname',
            'ClientContactMI' => 'Client Contact Mi',
            'ClientContactLName' => 'Client Contact Lname',
            'ClientPhone' => 'Client Phone',
            'ClientEmail' => 'Client Email',
            'ClientAddr1' => 'Client Addr1',
            'ClientAddr2' => 'Client Addr2',
            'ClientCity' => 'Client City',
            'ClientState' => 'Client State',
            'ClientZip4' => 'Client Zip4',
            'ClientTerritory' => 'Client Territory',
            'ClientActiveFlag' => 'Client Active Flag',
            'ClientDivisionsFlag' => 'Client Divisions Flag',
            'ClientComment' => 'Client Comment',
            'ClientCreateDate' => 'Client Create Date',
            'ClientCreatorUserID' => 'Client Creator User ID',
            'ClientModifiedDate' => 'Client Modified Date',
            'ClientModifiedBy' => 'Client Modified By',
        ];
    }
}
