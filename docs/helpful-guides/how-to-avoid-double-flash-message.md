How to avoid double flash messages
====================================

If you set  
```php
    'modules' => [
        'user' => [
            'class' => Da\User\Module::class,
            'enableFlashMessages' => true,
        ],
    ],
```

You can see double flash message. Use can use a redefine view from your theme:
```php
    'components' => [
        'view' => [
            'theme' => [
                'pathMap' => [
                    '@app/views' => '@backend/views/layouts-your-theme',
                    '@Da/User/resources/views/shared' => '@backend/views/layouts-your-theme/shared',
                ],
            ],
        ],
    ],
```
where `backend/views/layouts-your-theme/shared/_alert.php` have a check `PRESERVE_DOUBLE_ALERT`
```php

if (defined('PRESERVE_DOUBLE_ALERT')){
    return;
}
define('PRESERVE_DOUBLE_ALERT', true);
?>

<?php if ($module->enableFlashMessages ?? true): ?>
    <div class="row">
        <div class="col-xs-12">
            <?= youralert\FlashAlerts::widget([
                'errorIcon' => '<i class="fa fa-warning"></i>',
                'successIcon' => '<i class="fa fa-check"></i>',
                'successTitle' => Yii::t('main', 'Done!'), //for non-titled type like 'success-first'
                'closable' => true,
                'encode' => false,
                'bold' => false,
            ]); ?>
        </div>
    </div>
<?php endif ?>
```
`backend/views/layouts-your-theme/shared/_alert.php`
```php
$this->title = $title;
echo $this->render('/_alert', ['module' => $module]);
```

Also we must use this alert or messages in your layouts:
```html
            <div class="panel-body">
                <h3><?= Html::encode($this->title) ?></h3>
                <?= $this->render('@app/views/shared/_alert') ?>
                <?= $content ?>
            </div>
```

© [2amigos](http://www.2amigos.us/) 2013-2017
