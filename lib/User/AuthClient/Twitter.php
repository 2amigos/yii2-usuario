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

    /**
     * @return null Twitter does not provide user's email address
     */
    public function getEmail()
    {
        return null;
    }
}
