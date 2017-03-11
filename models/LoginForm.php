<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 *
 * @property User|null $user This property is read-only.
 *
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $phone_number;
    public $phone_region;
    public $remember = true;
    private $_user = false;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            ['username', 'required', 'when' => function($model) {
                return empty($model->phone_number) ;    }
            ],
            ['phone_number', 'required', 'when' => function($model) {
                return isset($model->phone_number) ;    }
            ],
            ['phone_region', 'required', 'when' => function($model) {
                return isset($model->phone_region) ;    }
            ],
            ['password', 'required'],
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
           
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            setcookie('username', isset($this->username)?$this->username:$this->phone_number, time()+60*60*24*30, '/');
            return Yii::$app->user->login($this->getUser(),  $this->remember? 3600*24*30 : 0 );
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
            if (isset($this->username)){
                $this->_user = User::findByUsername($this->username);

            }else {

                $this->_user = User::findByPhone($this->phone_number,$this->phone_region);
            }
        }

        return $this->_user;
    }
}
