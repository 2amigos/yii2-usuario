<?php

namespace tests\unit;

use Codeception\Exception\Warning;
use Da\User\Model\UserEntity;

class UserEntityTest extends \Codeception\Test\Unit
{
    public function testValidUserEntity()
    {
        $model = new UserEntity([
            'id' => 123,
            'user_id' => 1,
            'credential_id' => 'sampleCredentialId',
            'public_key' => 'samplePublicKey',
            'sign_count' => 0,
            'type' => 'public-key',
            'attestation_format' => ['direct','packed'],
            'device_id' => 'Test Device',
            'name' => 'TestPasskey',
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $this->assertTrue($model->validate(), 'Model should validate with correct data.');
    }

    public function testInvalidAttestationFormat()
    {
        $model = new UserEntity([
            'id' => 124,
            'user_id' => 1,
            'credential_id' => 'anotherCredentialId',
            'public_key' => 'someKey',
            'sign_count' => 0,
            'type' => 'public-key',
            'attestation_format' => 'invalid-type',
            'device_id' => 'Firefox',
            'name' => 'ValidName',
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $this->assertFalse($model->validate(), 'Model should not validate with invalid attestation_format.');
        $this->assertArrayHasKey('attestation_format', $model->getErrors());
    }

    public function testNameTooShort()
    {
        $model = new UserEntity([
            'id' => 125,
            'user_id' => 1,
            'credential_id' => 'shortNameTest',
            'public_key' => 'key',
            'sign_count' => 0,
            'type' => 'public-key',
            'attestation_format' => 'none',
            'device_id' => 'Chrome',
            'name' => 'abc', // too short
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $this->assertFalse($model->validate(), 'Model should not validate with short name.');
        $this->assertArrayHasKey('name', $model->getErrors());
    }

    public function testNameInvalidCharacters()
    {
        $model = new UserEntity([
            'id' => 126,
            'user_id' => 1,
            'credential_id' => 'invalidCharTest',
            'public_key' => 'key',
            'sign_count' => 0,
            'type' => 'public-key',
            'attestation_format' => 'none',
            'device_id' => 'DeviceX',
            'name' => 'Invalid@Name', // contains invalid char
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $this->assertFalse($model->validate(), 'Model should not validate with invalid characters in name.');
        $this->assertArrayHasKey('name', $model->getErrors());
    }
}
