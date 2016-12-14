<?php

namespace Da\User\AuthClient;

use Da\User\Contracts\AuthClientInterface;
use Yii;

class VKontakte extends \yii\authclient\clients\VKontakte implements AuthClientInterface
{
    /**
     * {@inheritdoc}
     */
    public $scope = 'email';

    /**
     * {@inheritdoc}
     */
    public function getEmail()
    {
        return $this->getAccessToken()->getParam('email');
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        return isset($this->getUserAttributes()['screen_name'])
            ? $this->getUserAttributes()['screen_name']
            : null;
    }

    /**
     * {@inheritdoc}
     */
    protected function defaultTitle()
    {
        return Yii::t('user', 'VKontakte');
    }
}
