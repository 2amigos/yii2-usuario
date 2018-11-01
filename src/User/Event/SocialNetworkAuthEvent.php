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
use yii\authclient\ClientInterface;
use yii\base\Event;

/**
 * @property-read SocialNetworkAccount $account
 * @property-read ClientInterface $client
 */
class SocialNetworkAuthEvent extends Event
{
    const EVENT_BEFORE_AUTHENTICATE = 'beforeAuthenticate';
    const EVENT_AFTER_AUTHENTICATE = 'afterAuthenticate';
    const EVENT_BEFORE_CONNECT = 'beforeConnect';
    const EVENT_AFTER_CONNECT = 'afterConnect';

    protected $client;
    protected $account;

    public function __construct(SocialNetworkAccount $account, ClientInterface $client, $config = [])
    {
        $this->account = $account;
        $this->client = $client;

        parent::__construct($config);
    }

    public function getClient()
    {
        return $this->client;
    }

    public function getAccount()
    {
        return $this->account;
    }
}
