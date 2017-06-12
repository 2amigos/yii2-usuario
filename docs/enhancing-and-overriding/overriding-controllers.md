Overriding Controllers
======================

Maybe you need to override the default's functionality of the module's controllers. For that, as you probably know, 
Yii2 Modules have an attribute named `controllerMap` that you can configure with your very own controllers.

Please, before you override a controller's action, make sure that it won't be enough with using the 
(controller's events)[../events/controller-events.md].

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

© [2amigos](http://www.2amigos.us/) 2013-2017


