Social Network Auth Events
==========================

The following is the list of the social network authentication events and where they happen: 


On Controllers
--------------

- **SecurityController**
    - **SocialNetworkAuthEvent::EVENT_BEFORE_CONNECT**: Occurs before a user's social network has been connected
    - **SocialNetworkAuthEvent::EVENT_AFTER_CONNECT**: Occurs after a user's social network has been connected
    - **SocialNetworkAuthEvent::EVENT_BEFORE_AUTHENTICATE**: Occurs before a user is authenticated via social network
    - **SocialNetworkAuthEvent::EVENT_AFTER_AUTHENTICATE**: Occurs after a a user is authenticated via social network


How to Work With Social Network Auth Events
-------------------------------------------

This event when triggered will contain a `Da\User\Model\SocialNetworkAccount` model instance and also the client used 
to authenticate throughout a social network. For example: 

```php
<?php 
// events.php file

use Da\User\Controller\SecurityController;
use Da\User\Event\SocialNetworkAuthEvent;
use yii\base\Event;

Event::on(
    SecurityController::class, 
    SocialNetworkAuthEvent::EVENT_BEFORE_CONNECT, 
    function (SocialNetworkAuthEvent $event) {
    
        $client = $event->getClient(); // $client is one of the Da\User\AuthClient\ clients
        $account = $event->getAccount(); // $account is a Da\User\Model\SocialNetworkAccount

        // ... your logic here
    });

```

> For further information about how to authenticate via social networks and the available clients, please visit the 
> [guide](../helpful-guides/social-network-authentication.md)


© [2amigos](http://www.2amigos.us/) 2013-2019
