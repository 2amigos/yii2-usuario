Mailer
======

The way this module sends its emails is throughout the [`Mailer`](http://www.yiiframework.com/doc-2.0/guide-tutorial-mailing.html) 
component of Yii 2. Please, follow Yii 2's guidelines to set it up. 
 
Nevertheless, you wish to configure the following attribute of the module: `mailParams`. the following is its default 
values:

```php
[
    'fromEmail' => 'no-reply@example.com',
    'welcomeMailSubject' => Yii::t('usuario', 'Welcome to {0}', $app->name),
    'confirmationMailSubject' => Yii::t('usuario', 'Confirm account on {0}', $app->name),
    'reconfirmationMailSubject' => Yii::t('usuario', 'Confirm email change on {0}', $app->name),
    'recoveryMailSubject' => Yii::t('usuario', 'Complete password reset on {0}', $app->name),
]
```

Actually, the only thing required is the `fromEmail` value. 
If you want to set it the same as senderEmail and senderName from your config params (like yii2-app-advanced template):
```php
    ...
    'modules' => [
        'user' => [
            'class' => Da\User\Module::class,
            'mailParams' => [
                'fromEmail' => function() {
                    return [Yii::$app->params['senderEmail'] => Yii::$app->params['senderName']];
                }
            ],
        ],
    ],
    ...
```
If you look at the code of `Da\User\Factory\MailFactory.php` 
you will easily find the reason why: 

```php
// take this helper function for example: 

public static function makeRecoveryMailerService($email, Token $token = null)
{
    /** @var Module $module */
    $module = Yii::$app->getModule('user');
    $to = $email;
    $from = $module->mailParams['fromEmail']; // fromEmail!!!
    $subject = $module->mailParams['recoveryMailSubject']; // subject!!!
    $params = [
        'user' => $token && $token->user ? $token->user : null,
        'token' => $token,
    ];

    return static::makeMailerService($from, $to, $subject, 'recovery', $params);
}

```

With that information it creates an `Da\User\Service\MailService` instance and this class makes use of those values to 
actually send the mails: 

```php
public function run()
{
    return $this->mailer
        ->compose(['html' => $this->view, 'text' => "text/{$this->view}"], $this->params)
        ->setFrom($this->from) // $this->from is actually fromEmail!!!
        ->setTo($this->to)
        ->setSubject($this->subject) // $this->subject is actually recoveryMailSubject!!!
        ->send();
}
```

> Tip: You can separate `from` by type of mailer of this module:
```php
    ...
    'modules' => [
        'user' => [
            'class' => Da\User\Module::class,
            'mailParams' => [
                'fromEmail' =>
                    /**
                     * @param $type string The type of mail 
                     *   Da\User\Event\MailEvent::TYPE_WELCOME|Da\User\Event\MailEvent::TYPE_RECOVERY|
                     *   Da\User\Event\MailEvent::TYPE_CONFIRM|Da\User\Event\MailEvent::TYPE_RECONFIRM
                     * @return array
                     */
                    function ($type) {
                        switch ($type) {
                            case Da\User\Event\MailEvent::TYPE_WELCOME:
                                return [Yii::$app->params['supportEmail'] => Yii::t('app', '{0} welcome!', Yii::$app->name)];
                            break;
                            default:
                                return [Yii::$app->params['supportEmail'] => Yii::t('app', '{0} robot', Yii::$app->name)];
                            break;
                        }
                    },
            ],
        ],
    ],
    ...
```


© [2amigos](http://www.2amigos.us/) 2013-2019
