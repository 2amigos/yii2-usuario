<?php

namespace Da\User\Model;


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
 *  @property string $attestation_format
 * @property string|null $device_id
 * @property string $created_at
 * @property string|null $last_used_at
 * @property string|null $name
 */
class UserEntity extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%user_passkeys}}';
    }

    public function rules()
    {
        return [

            [['user_id', 'credential_id', 'public_key', 'sign_count', 'type', 'attestation_format', 'id'], 'required'],
            ['attestation_format', 'string', 'max' => 64],
            ['attestation_format', 'default', 'value' => null],
            ['attestation_format', function ($attribute) {
                if ($this->$attribute === null) {
                    return;
                }
                $allowed = ['none', 'basic', 'attca', 'self', 'ecdaa', 'unknown', 'internal', 'hybrid', 'direct'];
                $values = array_map('trim', explode(',', $this->$attribute));
                foreach ($values as $value) {
                    if (!in_array($value, $allowed)) {
                        $this->addError($attribute, "Invalid attestation format: $value");
                    }
                }
            }],
            [['user_id'], 'integer'],
            [['id'], 'integer'],
            [['sign_count'], 'integer'],
            [['public_key'], 'string'],
            [['created_at', 'last_used_at'], 'safe'],
            [['credential_id'], 'string', 'max' => 255],
            [['type'], 'string', 'max' => 32],
            [['attestation_format'], 'string', 'max' =>64],
            [['device_id'], 'string', 'max' => 128],
            [['name'], 'string', 'max' => 128],
            [['name'], 'string', 'min' => 4],
            ['name', 'required'],
            ['name', 'match', 'pattern' => '/^[a-zA-Z0-9 ]+$/', 'message' => 'The name can contain only letters, numbers, and spaces.'],
            [['credential_id'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'credential_id' => 'Credential ID',
            'public_key' => 'Public Key',
            'sign_count' => 'Sign Count',
            'type' => 'Type',
            'attestation_format' => 'Attestation Format',
            'device_id' => 'Device ID',
            'created_at' => 'Created At',
            'last_used_at' => 'Last Used At',
            'name' => 'Name',
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
     * Gets the user related to this credential.
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
