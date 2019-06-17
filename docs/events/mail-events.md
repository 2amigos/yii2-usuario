Mail Events
===========

The following is the list of the mail events and where they happen:

On Models
---------

- **User**
    - `MailEvent::EVENT_BEFORE_SEND_MAIL`: triggered before a mail is sent
    - `MailEvent::EVENT_AFTER_SEND_MAIL`: triggered after a mail is sent

Mail Event Getter Methods
-------------------------

Each of the events above would receive the following properties via getter methods:
  
1. `getType()`: _string_, returns the type of mailer sent. The following mailer types will be returned:
    - `MailEvent::TYPE_WELCOME` or `'welcome'`: when a welcome mailer is sent on user creation or registration signup. 
    - `MailEvent::TYPE_RECOVERY` or `'recovery'`: when a password reset / recovery mailer is sent.
    - `MailEvent::TYPE_CONFIRM` or `'confirm'`: when an account confirmation mailer is sent.
    - `MailEvent::TYPE_RECONFIRM` or `'reconfirm'`: when an account confirmation mailer is requested for resending.
- `getUser()`: _Da\User\Model\User_, returns the current user model
- `getMailService()`: _Da\User\Service\MailService_, returns the mail service instance
- `getException()`: _Exception_, returns the exception object instance in case a mailer sending exception is received. 
   This will be `NULL` if no exception is received. It will also always be `NULL` for `MailEvent::EVENT_BEFORE_SEND_MAIL`
   and any exception received will only be trapped via `MailEvent::EVENT_AFTER_SEND_MAIL`.

How to Work With Mail Events
----------------------------

All these events receive an instance of `Da\User\Event\MailEvent`. The Event receives an instance of a `Da\Model\User` 
class and the other getter method properties as listed earlier, that you could use for whatever logic you wish to implement. 

The recommended way to make use of events is by creating a new file in your config folder (i.e. `events.php`), configure 
there all your events and then include that file on your 
[`entry script`](http://www.yiiframework.com/doc-2.0/guide-structure-entry-scripts.html).

Here is an example of setting an event for the `User` model: 

```php 
<?php 
// events.php file

use Da\User\Event\MailEvent;
use Da\User\Model\User;
use yii\base\Event;

// BEFORE MAIL IS SENT: This will happen at the model's level
Event::on(User::class, MailEvent::EVENT_BEFORE_SEND_MAIL, function (MailEvent $event) {

    $user = $event->getUser();
    $type = $event->getType();
    $mailService = $event->getMailService();
    
    // ... your logic here
}

// AFTER MAIL IS SENT: This will happen at the model's level
Event::on(User::class, MailEvent::EVENT_AFTER_SEND_MAIL, function (MailEvent $event) {

    $user = $event->getUser();
    $type = $event->getType();
    $mailService = $event->getMailService();
    $exception = $event->getException(); // fetches exception received if any
    
    // ... your logic here based on exception received for example
    if ($exception !== null) {
        // do something
    }
}
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
