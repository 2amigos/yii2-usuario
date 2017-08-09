<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\User\Service;

use Da\User\Contracts\AuthClientInterface;
use Da\User\Contracts\ServiceInterface;
use Da\User\Controller\SecurityController;
use Da\User\Event\SocialNetworkAuthEvent;
use Da\User\Model\SocialNetworkAccount;
use Da\User\Model\User;
use Da\User\Query\SocialNetworkAccountQuery;
use Da\User\Traits\ContainerAwareTrait;
use Yii;

class SocialNetworkAccountConnectService implements ServiceInterface
{
    use ContainerAwareTrait;

    protected $controller;
    protected $client;
    protected $socialNetworkAccountQuery;

    /**
     * SocialNetworkAccountUserLinkService constructor.
     *
     * @param SecurityController        $controller
     * @param AuthClientInterface       $client
     * @param SocialNetworkAccountQuery $socialNetworkAccountQuery
     */
    public function __construct(
        SecurityController $controller,
        AuthClientInterface $client,
        SocialNetworkAccountQuery $socialNetworkAccountQuery
    ) {
        $this->controller = $controller;
        $this->client = $client;
        $this->socialNetworkAccountQuery = $socialNetworkAccountQuery;
    }

    public function run()
    {
        $account = $this->getSocialNetworkAccount();

        $event = $this->make(SocialNetworkAuthEvent::class, [$account, $this->client]);

        $this->controller->trigger(SocialNetworkAuthEvent::EVENT_BEFORE_CONNECT, $event);

        if ($account && $account->user === null) {
            /** @var User $user */
            $user = Yii::$app->user->identity;
            $account->link('user', $user);
            Yii::$app->session->setFlash('success', Yii::t('usuario', 'Your account has been connected'));
            $this->controller->trigger(SocialNetworkAuthEvent::EVENT_AFTER_CONNECT, $event);

            return true;
        }
        Yii::$app->session->setFlash(
            'danger',
            Yii::t('usuario', 'This account has already been connected to another user')
        );

        return false;
    }

    protected function getSocialNetworkAccount()
    {
        $account = $this->socialNetworkAccountQuery->whereClient($this->client)->one();

        if (null === $account) {
            $data = $this->client->getUserAttributes();

            $account = $this->make(
                SocialNetworkAccount::class,
                [],
                [
                    'provider' => $this->client->getId(),
                    'client_id' => $data['id'],
                    'data' => json_encode($data),
                ]
            );

            if ($account->save(false)) {
                return $account;
            }
        }

        return false;
    }
}
