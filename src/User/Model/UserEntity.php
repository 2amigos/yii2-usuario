<?php

namespace Da\User\Model;

use Da\User\Query\UserEntityQuery;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "passkey".
 *
 * @property int $id
 * @property int $user_id
 * @property string $credential_id
 * @property string $public_key
 * @property int $sign_count
 * @property string $attestation_format
 * @property string|null $device_id
 * @property string $created_at
 * @property string|null $last_used_at
 * @property string|null $name
 * @property User $r_user
 */
class UserEntity extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%user_entity}}';
    }

    public function rules()
    {
        return [
            [['sign_count'], 'default', 'value' => 0],
            [['user_id', 'credential_id', 'public_key', 'sign_count', 'type', 'attestation_format', 'id', 'name'], 'required'],
            ['attestation_format', 'default', 'value' => 'none'],
            [['user_id','id','sign_count'], 'integer'],
            [['public_key'], 'string'],
            [['created_at', 'last_used_at'], 'safe'],
            [['credential_id'], 'string', 'max' => 255],
            [['type'], 'string', 'max' => 32],
            [['attestation_format'], 'string', 'max' =>64],
            [['device_id', 'name'], 'string', 'max' => 128],
            [['name'], 'string', 'min' => 4],
            ['name', 'match', 'pattern' => '/^[a-zA-Z0-9 ]+$/', 'message' => Yii::t('usuario', 'The name can contain only letters, numbers, and spaces.')],
            [['credential_id', 'id'], 'unique'],
            ['attestation_format', 'in', 'range' => ['none', 'packed', 'android-key', 'tpm', 'direct', 'unknown'], 'message' => Yii::t('usuario', 'Your attestation format is invalid and isn\'t supported.')],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('usuario', 'ID'),
            'user_id' => Yii::t('usuario', 'User ID'),
            'credential_id' => Yii::t('usuario', 'Credential ID'),
            'public_key' => Yii::t('usuario', 'Public Key'),
            'sign_count' => Yii::t('usuario', 'Sign Count'),
            'type' => Yii::t('usuario', 'Type'),
            'attestation_format' => Yii::t('usuario', 'Attestation Format'),
            'device_id' => Yii::t('usuario', 'Device ID'),
            'created_at' => Yii::t('usuario', 'Created At'),
            'last_used_at' => Yii::t('usuario', 'Last Used At'),
            'name' => Yii::t('usuario', 'Name'),
        ];
    }

    /**
     * Gets attestation formats as an array.
     */
    public function getAttestationFormats(): array
    {
        if (is_string($this->attestation_format)) {
            $decoded = json_decode($this->attestation_format, true);
            return json_last_error() === JSON_ERROR_NONE ? $decoded : [];
        }
        return [];
    }

    /**
     * @return UserEntityQuery
     */
    public static function find()
    {
        return new UserEntityQuery(static::class);
    }
}
