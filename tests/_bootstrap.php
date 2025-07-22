<?php

define('YII_ENV', 'test');
defined('YII_DEBUG') or define('YII_DEBUG', true);

// Percorsi per il vendor/autoload.php della tua estensione
$paths = [
    __DIR__ . '/vendor/autoload.php', // vendor della tua estensione (cartella corrente)
    '/app/vendor/autoload.php',        // fallback globale Docker
];

$found = false;

foreach ($paths as $autoloadPath) {
    if (file_exists($autoloadPath)) {
        require_once $autoloadPath;
        define('VENDOR_DIR', dirname($autoloadPath));
        $found = true;
        break;
    }
}

if (!$found) {
    throw new \Exception('Failed to locate autoload.php');
}

if (!class_exists('Yii')) {
    require_once VENDOR_DIR . '/yiisoft/yii2/Yii.php';
}
