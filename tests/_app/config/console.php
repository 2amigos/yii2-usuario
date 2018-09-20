<?php

return [
    'id' => 'yii2-test-console',
    'basePath' => dirname(__DIR__),
    'aliases' => [
        '@Da/User' => dirname(dirname(dirname(__DIR__))) . '/src/User',
        '@tests' => dirname(dirname(__DIR__)),
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'log' => null,
        'cache' => null,
        'db' => require __DIR__ . '/db.php',
        'authManager' => [
            'class' => Da\User\Component\AuthDbManagerComponent::class,
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
    'controllerMap' => [
        'migrate' => [
            'class' => yii\console\controllers\MigrateController::class,
            'migrationPath' => null,
            'migrationNamespaces' => [
                'Da\User\Migration',
            ],
        ],
    ],
];
