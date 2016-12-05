<?php
namespace Da\User\Contracts;

use yii\authclient\ClientInterface;

interface AuthClientInterface extends ClientInterface
{
    /**
     * @return string|null email
     */
    public function getEmail();

    /**
     * @return string|null username
     */
    public function getUserName();
}
