Form Events
===========

The following is the list of the form events and where they happen: 

On Controllers
--------------

- **RecoveryController**
    - **FormEvent::EVENT_BEFORE_REQUEST**: Occurs before a password recovery request
    - **FormEvent::EVENT_AFTER_REQUEST**: Occurs after a password recovery request


- **RegistrationController**
    - **FormEvent::EVENT_BEFORE_RESEND**: Occurs before a confirmation message is being sent via email
    - **FormEvent::EVENT_AFTER_RESEND**: Occurs after a confirmation message is being sent via email
    - **FormEvent::EVENT_BEFORE_REGISTER**: Occurs before user registration
    - **FormEvent::EVENT_AFTER_REGISTER**: Occurs after user registration


- **SecurityController**
    - **FormEvent::EVENT_BEFORE_LOGIN**: Occurs before a user logs into the system
    - **FormEvent::EVENT_AFTER_LOGIN**: Occurs after a user logs into the system
    - **FormEvent::EVENT_FAILED_LOGIN**: Occurs when user failed login

How to Work With Form Events
----------------------------

All these events received an instance of a `Da\User\Event\FormEvent`. The event receives an instance of a form 
depending on where its being called. The following is the list of the forms accessible via `FormEvent::getForm()`: 

- **FormEvent::EVENT_BEFORE_LOGIN|EVENT_AFTER_LOGIN**: It will contain a `Da\User\Form\LoginForm` instance with the 
    submitted data
- **FormEvent::EVENT_BEFORE_RESEND|EVENT_AFTER_RESEND**: It will contain a `Da\User\Form\ResendForm` instance with the 
    submitted data
- **FormEvent::EVENT_BEFORE_REQUEST|EVENT_AFTER_REQUEST**: It will contain a `Da\User\Form\RecoveryForm` instance with 
    the submitted data

The recommended way to make use of events is by creating a new file in your config folder (i.e. `events.php`), configure 
there all your events and then include that file on your 
[`entry script`](http://www.yiiframework.com/doc-2.0/guide-structure-entry-scripts.html).

Here is an example of setting an event for the `RecoveryController`: 

```php 
<?php 
// events.php file

use Da\User\Controller\RecoveryController;
use Da\User\Event\FormEvent;
use yii\base\Event;

Event::on(RecoveryController::class, FormEvent::EVENT_BEFORE_REQUEST, function (FormEvent $event) {
    $form = $event->getForm();
    
    // ... your logic here
});
```

Now, include `events.php` file to your entry script (i.e. `index.php`). The following is taken from the Yii 2 Advanced 
Application Template:

```php 
<?php
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require(__DIR__ . '/../../vendor/autoload.php');
require(__DIR__ . '/../../vendor/yiisoft/yii2/Yii.php');
require(__DIR__ . '/../../common/config/bootstrap.php');
require(__DIR__ . '/../config/bootstrap.php');

require(__DIR__ . '/../config/events.php'); // <--- adding events here! :)

$config = yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/../../common/config/main.php'),
    require(__DIR__ . '/../../common/config/main-local.php'),
    require(__DIR__ . '/../config/main.php'),
    require(__DIR__ . '/../config/main-local.php')
);

$application = new yii\web\Application($config);
$application->run();

```

© [2amigos](http://www.2amigos.us/) 2013-2019

