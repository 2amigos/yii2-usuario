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
use Da\User\Factory\MailFactory;
use Da\User\Model\SocialNetworkAccount;
use Da\User\Model\User;
use Da\User\Query\SocialNetworkAccountQuery;
use Da\User\Query\UserQuery;
use Yii;
use yii\authclient\AuthAction;
use yii\helpers\Url;

class SocialNetworkAuthenticateService implements ServiceInterface
{
    protected $controller;
    protected $authAction;
    protected $client;
    protected $socialNetworkAccountQuery;
    protected $userQuery;

    public function __construct(
        SecurityController $controller,
        AuthAction $authAction,
        AuthClientInterface $client,
        SocialNetworkAccountQuery $socialNetworkAccountQuery,
        UserQuery $userQuery
    ) {
        $this->controller = $controller;
        $this->authAction = $authAction;
        $this->client = $client;
        $this->socialNetworkAccountQuery = $socialNetworkAccountQuery;
        $this->userQuery = $userQuery;
    }

    public function run()
    {
        $account = $this->socialNetworkAccountQuery->whereClient($this->client)->one();
        if (!$this->controller->module->enableRegistration && ($account === null || $account->user === null)) {
            Yii::$app->session->setFlash('danger', Yii::t('usuario', 'Registration on this website is disabled'));
            $this->authAction->setSuccessUrl(Url::to(['/user/security/login']));

            return false;
        }
        if ($account === null) {
            $account = $this->createAccount();
            if (!$account) {
                Yii::$app->session->setFlash('danger', Yii::t('usuario', 'Unable to create an account.'));
                $this->authAction->setSuccessUrl(Url::to(['/user/security/login']));

                return false;
            }
        }

        $event = Yii::createObject(SocialNetworkAuthEvent::class, [$account, $this->client]);

        $this->controller->trigger(SocialNetworkAuthEvent::EVENT_BEFORE_AUTHENTICATE, $event);

        if ($account->user instanceof User) {
            if ($account->user->getIsBlocked()) {
                Yii::$app->session->setFlash('danger', Yii::t('usuario', 'Your account has been blocked.'));
                $this->authAction->setSuccessUrl(Url::to(['/user/security/login']));
            } else {
                Yii::$app->user->login($account->user, $this->controller->module->rememberLoginLifespan);
                $this->authAction->setSuccessUrl(Yii::$app->getUser()->getReturnUrl());
            }
        } else {
            $this->authAction->setSuccessUrl($account->getConnectionUrl());
        }

        $this->controller->trigger(SocialNetworkAuthEvent::EVENT_AFTER_AUTHENTICATE, $event);
    }

    protected function createAccount()
    {
        $data = $this->client->getUserAttributes();

        /** @var SocialNetworkAccount $account */
        $account = $this->controller->make(
            SocialNetworkAccount::class,
            [],
            [
                'provider' => $this->client->getId(),
                'client_id' => $data['id'],
                'data' => json_encode($data),
                'username' => $this->client->getUserName(),
                'email' => $this->client->getEmail(),
            ]
        );

        if (($user = $this->getUser($account)) instanceof User) {
            $account->user_id = $user->id;
            $account->save(false);

            return $account;
        }

        return false;
    }

    protected function getUser(SocialNetworkAccount $account)
    {
        $user = $this->userQuery->whereEmail($account->email)->one();
        if (null !== $user) {
            return $user;
        }
        /** @var User $user */
        $user = $this->controller->make(
            User::class,
            [],
            [
                'scenario' => 'connect',
                'username' => $account->username,
                'email' => $account->email,
            ]
        );

        if (!$user->validate(['email'])) {
            $user->email = null;
        }

        if (!$user->validate(['username'])) {
            $user->username = null;
        }

        $mailService = MailFactory::makeWelcomeMailerService($user);

        return $this->controller->make(UserCreateService::class, [$user, $mailService])->run() ? $user : false;
    }
}
