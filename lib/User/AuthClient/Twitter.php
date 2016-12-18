<?php

namespace Da\User\AuthClient;

use Da\User\Contracts\AuthClientInterface;
use yii\authclient\clients\Twitter as BaseTwitter;

class Twitter extends BaseTwitter implements AuthClientInterface
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
