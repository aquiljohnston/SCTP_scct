<?php

namespace app\models;

use app\controllers\BaseController;
use Faker\Provider\Base;
use Yii;
use yii\base\Model;
use linslin\yii2\curl;

/**
 * LoginForm is the model behind the login form.
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;

    private $_user = false;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            // todo: review 
            // if (!$user || !$user->validatePassword($this->password)) {
            if (!$user) {
                $this->addError($attribute, 'Incorrect username or password.');
            }    
            /*if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }*/
        }
    }

    /**
     * Logs in a user using the provided username and password.
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            $user = $this->getUser();
            //Yii::trace("Login user: " . $user['AuthToken'] . ", username: " . $user['UserID']);
            if(is_array($user))
                return $this->user;
            // return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600*24*30 : 0);
        }
        return false;
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            // Authenticate using the SCAPI 
            //$url = "http://apidev.southerncrossinc.com/index.php?r=v1%2Flogin%2Fuser-login";
            $url = BaseController::prependURL("login%2Fuser-login");
            $secretKey = 'sparusholdings12';
            $iv = 'abcdefghijklmnop';
            $pass = openssl_encrypt($this->password,  'AES-128-CBC', $secretKey, OPENSSL_RAW_DATA, $iv);
            $pwd = base64_encode($pass);
            
            $data = array(
                'UserName' => $this->username,
                'Password' => $pwd
            );    
            $json_data = json_encode($data);        
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST,"POST");
            curl_setopt($curl, CURLOPT_POSTFIELDS,$json_data);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
				'X-Client:' . BaseController::XClient,
                'Content-Type: application/json',
                'Content-Length: ' . strlen($json_data))
            );
            $result = curl_exec($curl);
            curl_close($curl);

            $this->_user = json_decode($result, true);
        }

        return $this->_user;
    }
}
