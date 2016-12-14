<?php

namespace Da\User\AuthClient;

use Da\User\Contracts\AuthClientInterface;

class Google extends \yii\authclient\clients\Google implements AuthClientInterface
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
        return;
    }
}
