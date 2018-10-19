<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\User\Event;

use Da\User\Model\SocialNetworkAccount;
use Da\User\Model\User;
use yii\base\Event;

/**
 * @property-read User $user
 * @property-read SocialNetworkAccount $account
 */
class SocialNetworkConnectEvent extends Event
{
    const EVENT_BEFORE_CONNECT = 'beforeConnect';
    const EVENT_AFTER_CONNECT = 'afterConnect';
    const EVENT_BEFORE_DISCONNECT = 'beforeDisconnect';
    const EVENT_AFTER_DISCONNECT = 'afterDisconnect';

    protected $user;
    protected $account;

    public function __construct(User $user, SocialNetworkAccount $account, $config = [])
    {
        $this->user = $user;
        $this->account = $account;

        parent::__construct($config);
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getAccount()
    {
        return $this->account;
    }
}
