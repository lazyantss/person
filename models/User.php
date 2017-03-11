<?php

namespace app\models;

use yii\behaviors\TimestampBehavior;
use Yii;

class User extends /*\yii\base\Object*/ \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
    /*
    
    create table `user`(
        id int unsigned not null auto_increment ,
        username varchar(64) not null default 0 comment '用户名',
        email varchar(32) not null default 0 comment '邮箱',
        password varchar(64) not null default 0 comment '密码',
        created_at int not null default 0 comment '注册时间',
        updated_at int not null default 0 comment '修改时间',
        login_ip varchar(20) not null default 0 comment '登录ip',
        login_time varchar(20) not null default 0 comment '登录时间',
        auth_key varchar(64) not null default 0 comment 'key',
        status int not null default 0 comment '激活',
        PRIMARY KEY (id),
        KEY `username` (username),
        KEY `email` (email)
    )
    */

    public static function tableName()
    {
        return 'user';
    }

    // public function rules()
    // {
    //     return [
            
    //         [['username', 'password'], 'required'],
    //         [['username'], 'string', 'max' => 64],
    //         [['password'], 'string', 'max' => 64],
    //         ['login_time',  'default', 'value'=>function() {
    //             return date('Y-m-d H:i:s',time());
    //         }],
    //         ['status', 'default', 'value' => self::STATUS_ACTIVE],
    //         ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
    //     ];
    // }
    // public function behaviors()
    // {
    //     return [
    //         TimestampBehavior::className(),
    //     ];
    // }
   
    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);//如果不存在。返回为null
       
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findIdentity($token);
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        $user = User::find()
                ->where(['email' => $username])
                ->asArray()
                ->one();
        if ($user) {
            return new static($user);
        }
        return null;
    }
    public static function findByPhone($phone,$phone_region) {
        $user = User::find()
                ->where(['phone_number' => $phone, 'phone_region' =>$phone_region])
                ->asArray()
                ->one();
        if ($user) {
            return new static($user);
        }
        return null;
    }
    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->id ;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($id)
    {
        return $this->id === $id;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return $this->password == $password;
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }
    
}
