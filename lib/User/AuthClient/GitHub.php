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
use yii\authclient\clients\GitHub as BaseGitHub;

class GitHub extends BaseGitHub implements AuthClientInterface
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
        return isset($this->getUserAttributes()['login'])
            ? $this->getUserAttributes()['login']
            : null;
    }
}
