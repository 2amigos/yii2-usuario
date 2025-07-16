<?php

namespace Da\User\Service;

use Yii;

class UserEntityTraductionService
{
    public static function translationPasskeyLoginJs()
    {
        return [
            'errorMsg' => Yii::t('usuario', 'Error : {msg}', ['msg' => '{msg}']),
            'invalidData' => Yii::t('usuario', 'Initial data isn\'t valid'),
            'invalidRes' => Yii::t('usuario', 'Server error: invalid response'),
            'failedAuth' => Yii::t('usuario', 'Authentication failed: {msg}', ['msg' => '{msg}']),
            'failedAuthUnk' => Yii::t('usuario', 'Authentication failed: unknown error'),
            'errorGen' => Yii::t('usuario', 'Error during the authentication with a passkey.'),
        ];
    }

    public static function translationPasskeyRegisterJs()
    {
        return [
            'psk' => Yii::t('usuario', 'My passkey'),
            'invalidCr' => Yii::t('usuario', 'Credential invalid.'),
            'genError' => Yii::t('usuario', 'There was an error during the registration of the passkey: {msg}', ['msg' => '{msg}']),
        ];
    }
}
