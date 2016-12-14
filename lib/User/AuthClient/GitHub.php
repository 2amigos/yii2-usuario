<?php

namespace Da\User\AuthClient;

use Da\User\Contracts\AuthClientInterface;

class GitHub extends \yii\authclient\clients\GitHub implements AuthClientInterface
{
    /**
     * {@inheritdoc}
     */
    public function getEmail()
    {
        return isset($this->getUserAttributes()['email'])
            ? $this->getUserAttributes()['email']
            : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        return isset($this->getUserAttributes()['login'])
            ? $this->getUserAttributes()['login']
            : null;
    }
}
