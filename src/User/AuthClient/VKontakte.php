<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\User\AuthClient;

use Da\User\Contracts\AuthClientInterface;
use Yii;
use yii\authclient\clients\VKontakte as BaseVKontakte;

class VKontakte extends BaseVKontakte implements AuthClientInterface
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
        return Yii::t('usuario', 'VKontakte');
    }
}
