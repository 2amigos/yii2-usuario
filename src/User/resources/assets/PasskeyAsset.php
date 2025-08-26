<?php

namespace Da\User\resources\assets;

use yii\web\AssetBundle;

class PasskeyAsset extends AssetBundle
{
    public $sourcePath = __DIR__;

    public $js = [
        'js/passkey-login.js',
        'js/passkey-register.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
