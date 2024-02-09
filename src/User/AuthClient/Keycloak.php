<?php

namespace Da\User\AuthClient;

use Da\User\Contracts\AuthClientInterface;
use yii\authclient\OpenIdConnect;

class Keycloak extends OpenIdConnect implements AuthClientInterface
{
    /**
     * {@inheritdoc}
     */
    public function getEmail()
    {
        // claim from email scope
        return $this->getUserAttributes()['email'] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function getUserName()
    {
        // claim from profile scope
        return $this->getUserAttributes()['preferred_username'] ?? $this->getEmail();
    }

    /**
     * {@inheritdoc}
     */
    public function getUserId()
    {
        return $this->getUserAttributes()['sub'] ?? null;
    }
}
