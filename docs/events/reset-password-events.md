Reset Password Events
=====================

The following is the list of the reset password events and where they happen: 

On Controllers
--------------

- **RecoveryController**
    - **ResetPasswordEvent::EVENT_BEFORE_TOKEN_VALIDATE**: Occurs before a reset password token is validated
    - **ResetPasswordEvent::EVENT_AFTER_RESET**: Occurs after the password has been reset
    
How to Work With Reset Password Events
--------------------------------------

This event is a bit more special than the others. When the token is about to be validated and the 
`EVENT_BEFORE_TOKEN_VALIDATE` is raised, only contains an instance of `Da\User\Model\Token` model and is accessible via 
`Da\Form\ResetPasswordEvent::getToken()` method. But after the token is successfully validated, then when the 
`Event_AFTER_RESET` is triggered, you can also access a `Da\User\Form\RecoveryForm` instance via its 
`Da\Form\ResetPasswordEvent::getToken()` method. 

Check the following code as an example: 

```php 
<?php 
// events.php file

use Da\User\Controller\RecoveryController;
use Da\User\Event\ResetPasswordEvent;
use yii\base\Event;

Event::on(
    RecoveryController::class, 
    ResetPasswordEvent::EVENT_BEFORE_TOKEN_VALIDATE, 
    function (ResetPasswordEvent $event) {
    
        $token = $event->getToken(); // $token is a Da\User\Model\Token instance
        $form = $event->getForm(); // form is NULL

        // ... your logic here
    });

Event::on(
    RecoveryController::class, 
    ResetPasswordEvent::EVENT_AFTER_RESET, 
    function (ResetPasswordEvent $event) {
    
        $token = $event->getToken(); // $token is a Da\User\Model\Token instance
        $form = $event->getForm(); // form is a Da\User\Form\RecoveryForm instance with submitted data

        // ... your logic here
    });

```

© [2amigos](http://www.2amigos.us/) 2013-2019
