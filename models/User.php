<?php

namespace app\models;
use yii\base\NotSupportedException;
use yii\db\ActiveRecord;
use yii\helpers\Security;
use yii\web\IdentityInterface;
use app\models\UserProfile;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
class   User extends ActiveRecord  implements \yii\web\IdentityInterface
{
   /* public $id;
    public $username;
    public $password;
    public $authKey;
    public $accessToken;*/

    /*private static $users = [
        '100' => [
            'id' => '100',
            'username' => 'admin',
            'password' => 'admin',
            'authKey' => 'test100key',
            'accessToken' => '100-token',
        ],
        '101' => [
            'id' => '101',
            'username' => 'demo',
            'password' => 'demo',
            'authKey' => 'test101key',
            'accessToken' => '101-token',
        ],
    ];*/


    public static function tableName()
    {
        return 'user_login';
    }


    public function rules(){
      [['username', 'password'], 'required'];
}
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInfo()
    {

        return $this->hasOne(UserProfile::className(), ['id' => 'id']);
    }
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->access_token = \Yii::$app->security->generateRandomString();
            }
            return true;
        }
        return false;
    }
    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {

        return static::findOne($id);
        //return isset(self::$users[$id]) ? new static(self::$users[$id]) : null;
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
       /* foreach (self::$users as $user) {
            if (strcasecmp($user['username'], $username) === 0) {
                return new static($user);
            }
        }

        return null;*/
        return static::findOne(['email_id' => $username]);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    public function generateAuthKey()

    {

        $this->access_token = Security::generateRandomKey();

    }

/**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        //echo $this->authKey;die;
        return $this->access_token;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->access_token === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        //echo $this->password;die;
        return $this->password === $password;
    }
}
