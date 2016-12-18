<?php

namespace Da\User\AuthClient;

use Da\User\Contracts\AuthClientInterface;
use yii\authclient\clients\LinkedIn as BaseLinkedIn;

class LinkedIn extends BaseLinkedIn implements AuthClientInterface
{
    /**
     * {@inheritdoc}
     */
    public function getEmail()
    {
        return isset($this->getUserAttributes()['email-address'])
            ? $this->getUserAttributes()['email-address']
            : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        return null;
    }
}
