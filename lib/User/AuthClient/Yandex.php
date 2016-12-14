<?php

namespace Da\User\AuthClient;

use Da\User\Contracts\AuthClientInterface;
use Yii;

class Yandex extends \yii\authclient\clients\Yandex implements AuthClientInterface
{
    /**
     * {@inheritdoc}
     */
    public function getEmail()
    {
        $emails = isset($this->getUserAttributes()['emails'])
            ? $this->getUserAttributes()['emails']
            : null;

        if ($emails !== null && isset($emails[0])) {
            return $emails[0];
        } else {
            return null;
        }
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

    /**
     * {@inheritdoc}
     */
    protected function defaultTitle()
    {
        return Yii::t('user', 'Yandex');
    }
}
