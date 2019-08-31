Overriding Controllers
======================

Maybe you need to override the default's functionality of the module's controllers. For that, as you probably know, 
Yii2 Modules have an attribute named `controllerMap` that you can configure with your very own controllers.

Please, before you override a controller's action, make sure that it won't be enough with using the 
[events](../events). For example you can use event for redirect after finish confirmation or recovery:

```php
    'modules' => [
        'user' => [
            'controllerMap' => [
               'recovery' => [
                    'class' => \Da\User\Controller\RecoveryController::class,
                    'on ' . \Da\User\Event\FormEvent::EVENT_AFTER_REQUEST => function (\Da\User\Event\FormEvent $event) {
                        \Yii::$app->controller->redirect(['/user/security/login']);
                        \Yii::$app->end();
                    },
                    'on ' . \Da\User\Event\ResetPasswordEvent::EVENT_AFTER_RESET => function (\Da\User\Event\ResetPasswordEvent $event) {
                        if ($event->token->user ?? false) {
                            \Yii::$app->user->login($event->token->user);
                            \Yii::$app->session->setFlash('success', Yii::t('usuario', 'Password has been changed'));
                        }
                        \Yii::$app->controller->redirect(\Yii::$app->getUser()->getReturnUrl());
                        \Yii::$app->end();
                    },
                ],
                'registration' => [
                    'class' => \Da\User\Controller\RegistrationController::class,
                    'on ' . \Da\User\Event\FormEvent::EVENT_AFTER_REGISTER => function (\Da\User\Event\FormEvent $event) {
                        \Yii::$app->controller->redirect(['/user/security/login']);
                        \Yii::$app->end();
                    },
                    'on ' . \Da\User\Event\FormEvent::EVENT_AFTER_RESEND => function (\Da\User\Event\FormEvent $event) {
                        \Yii::$app->session->setFlash('info', Yii::t('usuario', 'A message has been sent to your email address. It contains a confirmation link that you must click to complete registration.'));
                        \Yii::$app->controller->redirect(['/user/security/login']);
                        \Yii::$app->end();
                    },
                ],
...
```

> See more about this attribute on 
> [ The Definitive Guide to Yii 2.0](http://www.yiiframework.com/doc-2.0/guide-structure-controllers.html#controller-map) 

How to Override
---------------

First, create your new controller: 

```php 
namespace app\controllers;

use Da\User\Controller\RegistrationController as BaseController;

class ProfileController extends BaseController {
    
    public function actionConfirm($id, $code) {
        // ... your code here
    }
}

```

Now, the only thing that is missing is to add your brand new controller to the module's controller's map: 

```php 
'modules' => [
    // ...
    'user' => [
        'class' => 'Da\User\Module',
        'controllerMap' => [
            'profile' => 'app\controllers\ProfileController'
        ]
    ]
]
```

© [2amigos](http://www.2amigos.us/) 2013-2019


