<?php

namespace Da\User\Helper;

use CBOR\Decoder;
use CBOR\Stream;
use CBOR\StringStream;
use Da\User\Model\User;
use Da\User\Model\UserEntity;
use Da\User\Traits\ModuleAwareTrait;
use Random\RandomException;
use Webauthn\AuthenticatorAssertionResponseValidator;
use Webauthn\CeremonyStep\CeremonyStepManager;
use Webauthn\CeremonyStep\CheckAllowedOrigins;
use Webauthn\PublicKeyCredentialUserEntity;
use Yii;


/**
 *
 */

class UserEntityHelper
{
    use ModuleAwareTrait;
    public function base64UrlDecode(string $data): string
    {
        $remainder = strlen($data) % 4;
        if ($remainder) {
            $data .= str_repeat('=', 4 - $remainder);
        }
        return base64_decode(strtr($data, '-_', '+/'));
    }

    public function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    function utf8ize($data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->utf8ize($value);
            }
        } elseif (is_string($data)) {
            return mb_convert_encoding($data, 'UTF-8', 'UTF-8');
        }
        return $data;
    }
    //this function is used to extract the attestation type from the passkeys. the attestation type is the type of device used as the passkey provider
    public function extractAttestationFormat(string $attestationBase64Url): ?string
    {
        $binary = $this->base64UrlDecode($attestationBase64Url);
        $stream = new \CBOR\StringStream($binary);
        $decoder = \CBOR\Decoder::create();
        $object = $decoder->decode($stream);

        if (!($object instanceof \CBOR\MapObject)) {
            return null;
        }

        $reflection = new \ReflectionClass($object);
        $prop = $reflection->getProperty('data');
        $prop->setAccessible(true);
        $items = $prop->getValue($object);

        foreach ($items as $item) {
            $keyProp = (new \ReflectionClass($item))->getProperty('key');
            $keyProp->setAccessible(true);
            $key = $keyProp->getValue($item);

            $valueProp = (new \ReflectionClass($item))->getProperty('value');
            $valueProp->setAccessible(true);
            $value = $valueProp->getValue($item);

            $keyDataProp = (new \ReflectionClass($key))->getProperty('data');
            $keyDataProp->setAccessible(true);
            $keyString = $keyDataProp->getValue($key);

            if ($keyString === 'fmt') {
                $valueDataProp = (new \ReflectionClass($value))->getProperty('data');
                $valueDataProp->setAccessible(true);
                $valueString = $valueDataProp->getValue($value);

                return $valueString;
            }
        }
        return null;
    }

    /**
     * @throws RandomException
     */
    public function challengeGeneration(): array
    {
        $passkeys = UserEntity::find()->Andwhere(['type' => 'public-key'])->all();

        if (empty($passkeys)) {
            return ['success' => false, 'message' => Yii::t('usuario', 'No passkey registered.')];
        }

        $credentialDescriptors = array_map(fn($pk) => [
            'type' => 'public-key',
            'id' => $pk->credential_id,
        ], $passkeys);

        //effective generation of the challenge
        $challengeRaw = random_bytes(32);
        $challengeBase64 = rtrim(strtr($this->base64UrlEncode($challengeRaw), "+/", "-_"), "=");
        $this->storeChallenge($challengeBase64);
        Yii::$app->session->set('allow_credentials', $credentialDescriptors);

        return [
            'success' => true,
            'challenge' => $challengeBase64,
            'rpId' => Yii::$app->request->hostName,   //hostname of the app it will be useful later (like in the actionLoginPasskey)
            'allowCredentials' => $credentialDescriptors
        ];
    }

    public function challenge($credentialId): array
    {
        if ($credentialId === false) {
            return ['success' => false, 'message' =>  Yii::t('usuario', 'Invalid credential ID format.')];
        }
        $credentialIdB64url = rtrim(strtr($this->base64UrlEncode($credentialId), '+/', '-_'), '=');
        $passkey = UserEntity::find()->Andwhere(['credential_id' => $credentialIdB64url])->one();
        if (!$passkey) {
            Yii::error(Yii::t('usuario', 'Passkey not found for credential ID: ') . bin2hex($credentialId), __METHOD__);
            return ['success' => false, 'message' => Yii::t('usuario', 'Passkey not found.')];
        }
        $user = User::findOne($passkey->user_id);
        if (!$user) {
            return ['success' => false, 'message' => Yii::t('usuario', 'User not found.')];
        }
        $userEntity = PublicKeyCredentialUserEntity::create($user->username, (string)$user->id, $user->username); //in the table user of usuario we don't have the real name of an account so we create PublicKeyCredentialUserEntity using the username two times
        $challengeBase64 = $this->retrieveChallenge();
        return [
            $this->base64UrlDecode($challengeBase64),
            $userEntity,
            $passkey,
        ];
    }

    //this function checks if the current can access the passkey pages
    public function checkAccessConditions() : bool
    {
        $module = $this->getModule();
        if(Yii::$app->user->isGuest||!$module->enablePasskeyLogin){
            return false;
        }
        return true;
    }

    public function loadTableData(){
        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => \Da\User\Model\UserEntity::find()->Andwhere(['user_id' => Yii::$app->user->id]),
            'pagination' => [
                'pageSize' => 10,
            ],
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
                ]
            ],
        ]);
        return $dataProvider;
    }

    //this function is fundamental because while developing (unless you're working on a https application) you must validate your server
    //address to be trusted by the web-authn library
    function createAssertionValidator(): AuthenticatorAssertionResponseValidator
    {
        $ceremonyStepManager = new CeremonyStepManager([
            new CheckAllowedOrigins([Yii::$app->request->hostName], false),
        ]);
        return new AuthenticatorAssertionResponseValidator($ceremonyStepManager);
    }
    //this function puts the challenge as a session variable
    public function storeChallenge(?string $challengeBase64): void
    {
        Yii::$app->session->set('webauthn_challenge', $challengeBase64);
    }
    //this function is for retrieve the challenge saved in the session
    protected function retrieveChallenge(): ?string
    {
        return Yii::$app->session->get('webauthn_challenge') ?: null;
    }
}
