User Events
===========

The following is the list of the user events and where they happen: 

On Controllers
--------------

- **AdminController**
    - **UserEvent::EVENT_BEFORE_CREATE**: Occurs before a user has been created
    - **UserEvent::EVENT_AFTER_CREATE**: Occurs after a user has been created 
    - **UserEvent::EVENT_BEFORE_ACCOUNT_UPDATE**: Occurs before a user's account has been updated
    - **UserEvent::EVENT_AFTER_ACCOUNT_UPDATE**: Occurs after a user's account has been updated
    - **UserEvent::EVENT_BEFORE_PROFILE_UPDATE**: Occurs before a user's profile has been updated
    - **UserEvent::EVENT_AFTER_PROFILE_UPDATE**: Occurs after a user's profile has been updated
    - **UserEvent::EVENT_BEFORE_CONFIRMATION**: Occurs before a user's email has been confirmed
    - **UserEvent::EVENT_AFTER_CONFIRMATION**: Occurs after a user's email has been confirmed
    - **UserEvent::EVENT_BEFORE_BLOCK**: Occurs before a user is being blocked (forbid access to app)
    - **UserEvent::EVENT_AFTER_BLOCK**: Occurs after a user is being blocked (forbid access to app)
    - **UserEvent::EVENT_BEFORE_UNBLOCK**: Occurs before a user is being un-blocked
    - **UserEvent::EVENT_AFTER_UNBLOCK**: Occurs after a user is being un-blocked
    - **UserEvent::EVENT_BEFORE_SWITCH_IDENTITY**: Occurs before a user is being impersonated by admin
    - **UserEvent::EVENT_AFTER_SWITCH_IDENTITY**: Occurs after a user his being impersonated by admin


- **RegistrationController**
    - **UserEvent::EVENT_BEFORE_CONFIRMATION**
    - **UserEvent::EVENT_AFTER_CONFIRMATION**


- **SecurityController**
    - **UserEvent::EVENT_BEFORE_LOGOUT**: Occurs before user logs out of the app
    - **UserEvent::EVENT_AFTER_LOGOUT**: Occurs after user logs out of the app


- **SettingsController**
    - **UserEvent::EVENT_BEFORE_PROFILE_UPDATE**
    - **UserEvent::EVENT_AFTER_PROFILE_UPDATE**
    - **UserEvent::EVENT_BEFORE_ACCOUNT_UPDATE**: Occurs before the user account is updated
    - **UserEvent::EVENT_AFTER_ACCOUNT_UPDATE**: Occurs after the user account is updated
    - **UserEvent::EVENT_BEFORE_DELETE**: Occurs before the user account is deleted
    - **UserEvent::EVENT_AFTER_DELETE**: Occurs after the user account is deleted

On Models
---------

- **User**
    - **UserEvent::EVENT_BEFORE_REGISTER**
    - **UserEvent::EVENT_AFTER_REGISTER**
    - **UserEvent::EVENT_BEFORE_CONFIRMATION**
    - **UserEvent::EVENT_AFTER_CONFIRMATION**
    - **UserEvent::EVENT_BEFORE_BLOCK**
    - **UserEvent::EVENT_AFTER_BLOCK**
    - **UserEvent::EVENT_BEFORE_UNBLOCK**
    - **UserEvent::EVENT_AFTER_UNBLOCK**

How to Work With User Events
----------------------------

All these events receive an instance of `Da\User\Event\UserEvent`. The Event receives an instance of a `Da\Model\User` 
class that you could use for whatever logic you wish to implement. 

The recommended way to make use of events is by creating a new file in your config folder (i.e. `events.php`), configure 
there all your events and then include that file on your 
[`entry script`](http://www.yiiframework.com/doc-2.0/guide-structure-entry-scripts.html).

Here is an example of setting an event for the `AdminController` and the `User` model: 

```php 
<?php 
// events.php file

use Da\User\Controller\AdminController;
use Da\User\Event\UserEvent;
use Da\User\Model\User;
use yii\base\Event;

// This will happen at the controller's level
Event::on(AdminController::class, UserEvent::EVENT_BEFORE_CREATE, function (UserEvent $event) {
    $user = $event->getUser();
    
    // ... your logic here
});

// This will happen at the model's level
Event::on(User::class, UserEvent::EVENT_BEFORE_CREATE, function (UserEvent $event) {

    $user = $event->getUser();
    
    // ... your logic here
});

```

Now, the only thing I need to do is adding the `events.php` file to your entry script (i.e. `index.php`). The following 
is taken from the Yii 2 Advanced Application Template:

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
