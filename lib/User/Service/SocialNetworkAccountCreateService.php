<?php
namespace Da\User\Service;


use Da\User\Contracts\AuthClientInterface;
use Da\User\Contracts\ServiceInterface;
use Da\User\Model\SocialNetworkAccount;
use Da\User\Model\User;
use Da\User\Query\UserQuery;
use Yii;

class SocialNetworkAccountCreateService implements ServiceInterface
{
    protected $client;
    protected $query;

    /**
     * SocialNetworkAccountUserLinkService constructor.
     *
     * @param AuthClientInterface $client
     * @param UserQuery $query
     */
    public function __construct(
        AuthClientInterface $client,
        UserQuery $query
    ) {
        $this->client = $client;
        $this->query = $query;
    }

    /**
     * @return object
     */
    public function run()
    {
        $data = $this->client->getUserAttributes();

        /** @var SocialNetworkAccount $account */
        $account = Yii::createObject(
            [
                'class' => SocialNetworkAccount::class,
                'provider' => $this->client->getId(),
                'client_id' => $data['id'],
                'data' => json_encode($data),
                'username' => $this->client->getUserName(),
                'email' => $this->client->getEmail()
            ]
        );

        if (($user = $this->getUser($account)) instanceof User) {
            $account->user_id = $user->id;
        }

        $account->save(false);

        return $account;
    }

    protected function getUser(SocialNetworkAccount $account)
    {
        $user = $this->query->whereEmail($account->email)->one();
        if (null !== $user) {
            return $user;
        }
        /** @var User $user */
        $user = Yii::createObject(
            'User',
            [
                'scenario' => 'connect',
                'username' => $account->username,
                'email' => $account->email
            ]
        );

        if (!$user->validate(['email'])) {
            $user->email = null;
        }

        if (!$user->validate(['username'])) {
            $user->username = null;
        }

        return Yii::$container->get(UserCreateService::class, [$user])->run() ? $user : false;
    }
}
