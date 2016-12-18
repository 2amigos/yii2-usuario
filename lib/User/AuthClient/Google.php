<?php

namespace Da\User\AuthClient;

use Da\User\Contracts\AuthClientInterface;
use yii\authclient\clients\Google as BaseGoogle;

class Google extends BaseGoogle implements AuthClientInterface
{
    /**
     * {@inheritdoc}
     */
    public function getEmail()
    {
        return isset($this->getUserAttributes()['emails'][0]['value'])
            ? $this->getUserAttributes()['emails'][0]['value']
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
