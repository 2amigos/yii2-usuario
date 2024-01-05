<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\User\Contracts;

use yii\authclient\ClientInterface;

/**
 * @property-read string|null $email
 * @property-read string|null $userName
 * @property-read mixed|null $userId
 */
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

    /**
     * @return mixed|null user id
     */
    public function getUserId();
}
