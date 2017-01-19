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

	const MAX_NAME_LENGTH = 100;
	const MAX_CONTACT_TITLE_LENGTH = 100;
	const MAX_CONTACT_F_NAME_LENGTH = 100;
	const MAX_CONTACT_M_I_LENGTH = 50;
	const MAX_CONTACT_L_NAME_LENGTH = 100;
	const MAX_PHONE_LENGTH = 15;
	const MAX_EMAIL_LENGTH = 255; //long enough for any valid email address
    const MAX_ADDR_1_LENGTH = 100;
    const MAX_ADDR_2_LENGTH = 100;
    const MAX_CITY_LENGTH = 100;
    const MAX_STATE_LENGTH = 50;
    const MAX_ZIP_4_LENGTH = 10; //TODO: Review name and length of Zip Code field
    const MAX_TERRITORY_LENGTH = 100;
    const MAX_COMMENT_LENGTH = 255;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ClientName'], 'required'],
            [['ClientName', 'ClientContactTitle', 'ClientContactFName', 'ClientContactMI', 'ClientContactLName', 'ClientPhone', 'ClientEmail', 'ClientAddr1', 'ClientAddr2', 'ClientCity', 'ClientState', 'ClientZip4', 'ClientTerritory', 'ClientComment'], 'string'],
            [['ClientID', 'ClientAccountID', 'ClientActiveFlag', 'ClientDivisionsFlag',  'ClientCreatorUserID', 'ClientModifiedBy'], 'integer'],
            [['ClientCreateDate', 'ClientModifiedDate'], 'safe'],
            ['ClientName', 'string', 'max' => self::MAX_NAME_LENGTH],
            ['ClientContactTitle', 'string', 'max' => self::MAX_CONTACT_TITLE_LENGTH],
            ['ClientContactFName', 'string', 'max' => self::MAX_CONTACT_F_NAME_LENGTH],
            ['ClientContactMI', 'string', 'max' => self::MAX_CONTACT_M_I_LENGTH],
            ['ClientContactLName', 'string', 'max' => self::MAX_CONTACT_L_NAME_LENGTH],
            ['ClientPhone', 'string', 'max' => self::MAX_PHONE_LENGTH],
            ['ClientEmail', 'string', 'max' => self::MAX_EMAIL_LENGTH],
            ['ClientAddr1', 'string', 'max' => self::MAX_ADDR_1_LENGTH],
            ['ClientAddr2', 'string', 'max' => self::MAX_ADDR_2_LENGTH],
            ['ClientCity', 'string', 'max' => self::MAX_CITY_LENGTH],
            ['ClientState', 'string', 'max' => self::MAX_STATE_LENGTH],
            ['ClientZip4', 'string', 'max' => self::MAX_ZIP_4_LENGTH],
            ['ClientTerritory', 'string', 'max' => self::MAX_TERRITORY_LENGTH],
            ['ClientComment', 'string', 'max' => self::MAX_COMMENT_LENGTH]
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
