Social Network Connect Events
=============================

The following is the list of the social network connection events and where they happen: 


On Controllers
--------------

- **RegistrationController**
    - **SocialNetworkConnectEvent::EVENT_BEFORE_CONNECT**: Occurs before a user's social network has been connected
    - **SocialNetworkConnectEvent::EVENT_AFTER_CONNECT**: Occurs after a user's social network has been connected


- **SettingsController**
    - **SocialNetworkConnectEvent::EVENT_BEFORE_DISCONNECT**: Occurs before a user is disconnecting a social network
    - **SocialNetworkConnectEvent::EVENT_AFTER_DISCONNECT**: Occurs after a a user is disconnecting a social network


How to Work With Social Network Connect Events
----------------------------------------------

This event when triggered will contain a `Da\User\Model\User` model instance and also a 
`Da\User\Model\SocialNetworkAccount` instance. For example: 

```php
<?php 
// events.php file

use Da\User\Controller\RegistrationController;
use Da\User\Controller\SecurityController;
use Da\User\Event\SocialNetworkConnectEvent;
use Da\User\Event\SocialNetworkAuthEvent;
use yii\base\Event;

// on RegistrationController

Event::on(
    RegistrationController::class, 
    SocialNetworkConnectEvent::EVENT_BEFORE_CONNECT, 
    function (SocialNetworkConnectEvent $event) {
    
        $user = $event->getUser(); // $token is a Da\User\Model\User instance
        $account = $event->getAccount(); // $account is a Da\User\Model\SocialNetworkAccount

        // ... your logic here
    });

// on SecurityController

Event::on(
    SecurityController::class, 
    SocialNetworkAuthEvent::EVENT_BEFORE_AUTHENTICATE, 
    function (SocialNetworkAuthEvent $event) {
    
        $client = $event->getClient();
        $account = $event->getAccount(); // $account is a Da\User\Model\SocialNetworkAccount

        // ... your logic here
    });

```
 

© [2amigos](http://www.2amigos.us/) 2013-2019
