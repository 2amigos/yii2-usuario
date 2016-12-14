<?php

namespace Da\User\AuthClient;

use Da\User\Contracts\AuthClientInterface;

class LinkedIn extends \yii\authclient\clients\LinkedIn implements AuthClientInterface
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
        return;
    }
}
