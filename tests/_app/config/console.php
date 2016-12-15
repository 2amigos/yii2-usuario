<?php

return [
    'id' => 'yii2-test-console',
    'basePath' => dirname(__DIR__),
    'aliases' => [
        '@Da/User' => dirname(dirname(dirname(__DIR__))).'/lib/User',
        '@tests' => dirname(dirname(__DIR__)),
    ],
    'components' => [
        'log' => null,
        'cache' => null,
        'db' => require __DIR__.'/db.php',
    ],
];
