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
use yii\authclient\clients\LinkedIn as BaseLinkedIn;

class LinkedIn extends BaseLinkedIn implements AuthClientInterface
{
    /**
     * {@inheritdoc}
     */
    public function getEmail()
    {
        $userAttributes = $this->getUserAttributes();

        if (isset($userAttributes['email-address'])) {
            return $userAttributes['email-address'];
        }

        if (isset($userAttributes['email'])) {
            return $userAttributes['email'];
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        /* returns the e-mail as it corresponds with the username */
        return $this->getEmail();
    }
}
