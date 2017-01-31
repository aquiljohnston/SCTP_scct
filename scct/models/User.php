<?php

namespace app\models;

use Yii;
use yii\web\IdentityInterface;
use app\controllers\BaseController;

/**
 * This is the model class for a user.
 *
 * @property integer $UserID
 * @property string $UserName
 * @property string $UserFirstName
 * @property string $UserLastName
 * @property string $UserEmployeeType
 * @property string $UserPhone
 * @property string $UserCompanyName
 * @property string $UserCompanyPhone
 * @property string $UserAppRoleType
 * @property string $UserComments
 * @property string $UserKey
 * @property string $Password 
 * @property integer $UserActiveFlag
 * @property string $UserCreatedDate
 * @property string $UserModifiedDate
 * @property integer $UserCreatedBy
 * @property integer $UserModifiedBy
 * @property string $UserCreateDTLTOffset
 * @property integer $UserModifiedDTLTOffset
 * @property integer $UserInactiveDTLTOffset
 *
 * @property EquipmentTb[] $equipmentTbs
 * @property ProjectUserTb[] $projectUserTbs
 * @property KeyTb $userKey
 */
class User extends \yii\base\model implements IdentityInterface
{
    // /**
     // * @inheritdoc
     // */
    // public static function tableName()
    // {
        // return 'UserTb';
    // }

	public $UserID;
	public $UserName;
	public $UserFirstName;
	public $UserLastName;
	public $UserEmployeeType;
	public $UserPhone;
	public $UserCompanyName;
	public $UserCompanyPhone;
	public $UserAppRoleType;
	public $UserComments;
	public $UserPassword;
	public $UserActiveFlag;
	public $UserCreatedDate;
	public $UserModifiedDate;
	public $UserCreatedBy;
	public $UserModifiedBy;
	public $UserCreateDTLTOffset;
	public $UserModifiedDTLTOffset;
	public $UserInactiveDTLTOffset;

	const MAX_NAME_LENGTH = 100;
	const MAX_FIRST_NAME_LENGTH = 50;
	const MAX_LAST_NAME_LENGTH = 50;
	const MAX_EMPLOYEE_TYPE_LENGTH = 50;
	const MAX_PHONE_LENGTH = 20;
	const MAX_COMPANY_NAME_LENGTH = 100;
	const MAX_COMPANY_PHONE_LENGTH = 14;
	const MAX_APP_ROLE_TYPE_LENGTH = 50;
	const MAX_COMMENTS_LENGTH = 250;
	const MAX_KEY_LENGTH = 75;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['UserName', 'UserFirstName', 'UserLastName', 'UserEmployeeType', 'UserPhone', 'UserCompanyName', 'UserCompanyPhone', 'UserAppRoleType', 'UserComments', 'UserKey', 'UserCreateDTLTOffset'], 'string'],
            [['UserID', 'UserActiveFlag', 'UserModifiedDTLTOffset', 'UserInactiveDTLTOffset', 'UserCreatedBy', 'UserModifiedBy'], 'integer'],
            [['UserCreatedDate', 'UserModifiedDate'], 'safe'],
            ['UserName', 'string', 'max' => self::MAX_NAME_LENGTH],
            ['UserFirstName', 'string', 'max' => self::MAX_FIRST_NAME_LENGTH],
            ['UserLastName', 'string', 'max' => self::MAX_LAST_NAME_LENGTH],
            ['UserEmployeeType', 'string', 'max' => self::MAX_EMPLOYEE_TYPE_LENGTH],
            ['UserPhone', 'string', 'max' => self::MAX_PHONE_LENGTH],
            ['UserCompanyName', 'string', 'max' => self::MAX_COMPANY_NAME_LENGTH],
            ['UserCompanyPhone', 'string', 'max' => self::MAX_COMPANY_PHONE_LENGTH],
            ['UserAppRoleType', 'string', 'max' => self::MAX_APP_ROLE_TYPE_LENGTH],
            ['UserComments', 'string', 'max' => self::MAX_COMMENTS_LENGTH],
            ['UserKey', 'string', 'max' => self::MAX_KEY_LENGTH]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'UserID' => 'User ID',
            'UserName' => 'User Name',
            'UserFirstName' => 'User First Name',
            'UserLastName' => 'User Last Name',
            'UserEmployeeType' => 'User Employee Type',
            'UserPhone' => 'User Phone',
            'UserCompanyName' => 'User Company Name',
            'UserCompanyPhone' => 'User Company Phone',
            'UserAppRoleType' => 'User App Role Type',
            'UserComments' => 'User Comments',
            'UserPassword' => 'User Password',
            'UserActiveFlag' => 'User Active Flag',
            'UserCreatedDate' => 'User Created Date',
            'UserModifiedDate' => 'User Modified Date',
            'UserCreatedBy' => 'User Created By',
            'UserModifiedBy' => 'User Modified By',
            'UserCreateDTLTOffset' => 'User Create Dtltoffset',
            'UserModifiedDTLTOffset' => 'User Modified Dtltoffset',
            'UserInactiveDTLTOffset' => 'User Inactive Dtltoffset',
        ];
    }

    // /**
     // * @return \yii\db\ActiveQuery
     // */
    // public function getEquipmentTbs()
    // {
        // return $this->hasMany(EquipmentTb::className(), ['EquipmentAssignedUserID' => 'UserID']);
    // }

    // /**
     // * @return \yii\db\ActiveQuery
     // */
    // public function getProjectUserTbs()
    // {
        // return $this->hasMany(ProjectUserTb::className(), ['ProjUserUserID' => 'UserID']);
    // }

    // /**
     // * @return \yii\db\ActiveQuery
     // */
    // public function getUserKey()
    // {
        // return $this->hasOne(KeyTb::className(), ['KeyID' => 'UserKey']);
    // }
	
	
	
	// //////identity interface methods/////
    // public static function findIdentity($id)
    // {
        // return static::findOne($id);
    // }

    // public static function findIdentityByAccessToken($token, $type = null)
    // {
        // return static::findOne(['access_token' => $token]);
    // }

    // public function getId()
    // {
		// //$userID = Yii::$app->session['userID'];
        // //return $userID;
		// return $this->UserID;
    // }

    // public function getAuthKey()
    // {
        // return $this->authKey;
    // }

    // public function validateAuthKey($authKey)
    // {
        // return $this->authKey === $authKey;
    // }
	
	//identity interface methods
    public static function findIdentity($id)
	{
		$url = 'user%2Fget-me';
		$response = BaseController::executeGetRequest($url);
		$decodedResponse = json_decode($response, true);
		$userIdentity = new User();
		if (array_key_exists("User",$decodedResponse))
		{
			$identityAttributes = $decodedResponse["User"];
			$userIdentity->attributes = $identityAttributes;
		}
		
		return $userIdentity;
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
		$url = 'auth%2Fget-user-by-token&token='.$token;
		$response = BaseController::executeGetRequest($url);
		$identityAttributes = json_decode($response, true);
		$userIdentity = new User();
		$userIdentity->attributes = $identityAttributes;
		
		return $userIdentity;
    }

    public function getId()
    {
		$userID = Yii::$app->session['userID'];
        return $userID;
    }

    public function getAuthKey()
    {
		$authKey = Yii::$app->session['token'];
        return $authKey;
    }

    public function validateAuthKey($authKey)
    {
		$url = 'auth%2Fvalidate-auth-key&token='.$authKey;
		$response = BaseController::executeGetRequest($url);
		return $response;
    }

}
