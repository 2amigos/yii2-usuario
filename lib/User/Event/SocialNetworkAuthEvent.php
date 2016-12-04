<?php
namespace Da\User\Event;

use Da\User\Model\SocialNetworkAccount;
use yii\authclient\ClientInterface;
use yii\base\Event;


class SocialNetworkAuthEvent extends Event
{
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
