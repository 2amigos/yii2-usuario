<?php
namespace Da\User\AuthClient;

use Da\User\Contracts\AuthClientInterface;

class Facebook extends \yii\authclient\clients\Facebook implements AuthClientInterface
{
    /**
     * @inheritdoc
     */
    public function getEmail()
    {
        return isset($this->getUserAttributes()['email'])
            ? $this->getUserAttributes()['email']
            : null;
    }

    /**
     * @inheritdoc
     */
    public function getUsername()
    {
        return;
    }
}
