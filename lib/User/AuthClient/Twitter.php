<?php

namespace Da\User\AuthClient;

use Da\User\Contracts\AuthClientInterface;

class Twitter extends \yii\authclient\clients\Twitter implements AuthClientInterface
{
    /**
     * @return string
     */
    public function getUsername()
    {
        return isset($this->getUserAttributes()['screen_name'])
            ? $this->getUserAttributes()['screen_name']
            : null;
    }

    public function getEmail()
    {
        return null;
    }
}
