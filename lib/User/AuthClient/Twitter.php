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
use yii\authclient\clients\Twitter as BaseTwitter;

class Twitter extends BaseTwitter implements AuthClientInterface
{
    /**
     * @return string
     */
    public function getUsername()
    {
        return isset($this->getUserAttributes()['screen_name'])
            ? $this->getUserAttributes()['screen_name']
            : null;
    }

    public function getEmail()
    {
        return null;
    }
}
