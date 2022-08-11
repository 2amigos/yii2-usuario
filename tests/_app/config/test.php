<?php

use Da\User\Filter\TwoFactorAuthenticationEnforceFilter;

return [
    'id' => 'yii2-user-tests',
    'basePath' => dirname(__DIR__),
    'language' => 'en-US',
    'aliases' => [
        '@Da/User' => dirname(dirname(dirname(__DIR__))) . '/src/User',
        '@tests' => dirname(dirname(__DIR__)),
        '@vendor' => VENDOR_DIR,
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'bootstrap' => ['Da\User\Bootstrap'],
    'modules' => [
        'user' => [
            'class' => 'Da\User\Module',
            'administrators' => ['user'],
        ],
    ],
    'components' => [
        'assetManager' => [
            'basePath' => __DIR__ . '/../assets',
        ],
        'db' => require __DIR__ . '/db.php',
        'mailer' => [
            'messageClass' => \yii\symfonymailer\Message::class,
            [
                'class' => \yii\symfonymailer\Mailer::class,
            ],
            'useFileTransport' => false
        ],
        'urlManager' => [
            'showScriptName' => true,
        ],
        'request' => [
            'cookieValidationKey' => 'test',
            'enableCsrfValidation' => false,
        ],
        'i18n' => [
            'translations' => [
                'usuario*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => __DIR__ . '/../../../src/User/resources/i18n',
                    'sourceLanguage' => 'en-US',
                    'fileMap' => [
                        'usuario' => 'usuario.php',
                    ],
                ],
            ],
        ],
    ],
    'params' => [],
    'on beforeAction' => function() {
        Yii::$app->controller->attachBehavior(
            'enforceTwoFactorAuthentication',[
                'class' => TwoFactorAuthenticationEnforceFilter::class,
                'except' => ['login', 'logout','account','two-factor', 'two-factor-enable'],
            ]
        );
    },
];
