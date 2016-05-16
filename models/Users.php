<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "users".
 *
 * @property integer $user_id
 * @property string $user_name
 * @property string $user_email
 * @property string $user_password
 * @property string $phone
 * @property string $address
 * @property string $date
 * @property string $user_type
 */
class Users extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_name', 'user_email', 'user_password', 'phone', 'address', 'date', 'user_type'], 'required'],
            [['user_name', 'user_email', 'user_password', 'phone', 'address', 'date', 'user_type'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'user_name' => 'User Name',
            'user_email' => 'User Email',
            'user_password' => 'User Password',
            'phone' => 'Phone',
            'address' => 'Address',
            'date' => 'Date',
            'user_type' => 'User Type',
        ];
    }
}
