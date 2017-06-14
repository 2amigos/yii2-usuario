Mailer
======

The way this module sends its emails is throughout the [`Mailer`](http://www.yiiframework.com/doc-2.0/guide-tutorial-mailing.html) 
component of Yii 2. Please, follow Yii 2's guidelines to set it up. 
 
Nevertheless, you have to configure the following attribute of the module: `mailParams`. the following is its default 
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

Actually, the only thing required is the `fromEmail` value. If you look at the code of `Da\User\Factory\MailFactory.php` 
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

© [2amigos](http://www.2amigos.us/) 2013-2017
