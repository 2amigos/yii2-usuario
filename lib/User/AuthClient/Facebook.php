<?php

namespace Da\User\AuthClient;

use Da\User\Contracts\AuthClientInterface;
use yii\authclient\clients\Facebook as BaseFacebook;

class Facebook extends BaseFacebook implements AuthClientInterface
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
        return null;
    }
}
