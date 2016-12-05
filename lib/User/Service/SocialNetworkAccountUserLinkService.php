<?php
namespace Da\User\Service;


use Da\User\Contracts\AuthClientInterface;
use Da\User\Contracts\ServiceInterface;
use Da\User\Model\SocialNetworkAccount;
use Da\User\Model\User;
use Da\User\Query\SocialNetworkAccountQuery;
use Yii;


class SocialNetworkAccountUserLinkService implements ServiceInterface
{
    protected $client;
    protected $query;

    /**
     * SocialNetworkAccountUserLinkService constructor.
     *
     * @param AuthClientInterface $client
     * @param SocialNetworkAccountQuery $query
     */
    public function __construct(
        AuthClientInterface $client,
        SocialNetworkAccountQuery $query
    ) {
        $this->client = $client;
        $this->query = $query;
    }

    public function run()
    {
        $account = $this->getSocialNetworkAccount();

        if ($account->user === null) {
            /** @var User $user */
            $user = Yii::$app->user->identity;
            $account->link('user', $user);

            return true;
        }

        return false;
    }

    protected function getSocialNetworkAccount()
    {
        $account = $this->query->whereClient($this->client)->one();

        if (null === $account) {
            $data = $this->client->getUserAttributes();

            $account = Yii::createObject(
                [
                    'class' => SocialNetworkAccount::class,
                    'provider' => $this->client->getId(),
                    'client_id' => $data['id'],
                    'data' => json_encode($data)
                ]
            );

            $account->save(false);
        }

        return $account;
    }
}
