<?php

namespace app\models;

use Yii;
use yii\web\IdentityInterface;
use app\controllers\BaseController;

/**
 * This is the model class for a user.
 *
 * @property string $UserID
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
 * @property integer $UserActiveFlag
 * @property string $UserCreatedDate
 * @property string $UserModifiedDate
 * @property string $UserCreatedBy
 * @property string $UserModifiedBy
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
	public $UserKey;
	public $UserActiveFlag;
	public $UserCreatedDate;
	public $UserModifiedDate;
	public $UserCreatedBy;
	public $UserModifiedBy;
	public $UserCreateDTLTOffset;
	public $UserModifiedDTLTOffset;
	public $UserInactiveDTLTOffset;
	
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['UserName', 'UserFirstName', 'UserLastName', 'UserEmployeeType', 'UserPhone', 'UserCompanyName', 'UserCompanyPhone', 'UserAppRoleType', 'UserComments', 'UserCreatedBy', 'UserModifiedBy', 'UserCreateDTLTOffset'], 'string'],
            [['UserKey', 'UserActiveFlag', 'UserModifiedDTLTOffset', 'UserInactiveDTLTOffset'], 'integer'],
            [['UserCreatedDate', 'UserModifiedDate'], 'safe']
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
            'UserKey' => 'User Key',
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
		$url = 'api.southerncrossinc.com/index.php?r=user%2Fview&id='.$id;
		$response = BaseController::executeGetRequest($url);
		$identityAttributes = json_decode($response, true);
		$userIdentity = new User();
		$userIdentity->attributes = $identityAttributes;
		
		return $userIdentity;
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
		$url = 'api.southerncrossinc.com/index.php?r=auth%2Fget-user-by-token&token='.$token;
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
		$url = 'api.southerncrossinc.com/index.php?r=auth%2Fvalidate-auth-key&token='.$authKey;
		$response = BaseController::executeGetRequest($url);
		return $response;
    }

}
