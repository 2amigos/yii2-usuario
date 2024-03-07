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
use Da\User\Query\UserQuery;
use Da\User\Traits\ModuleAwareTrait;
use Yii;
use yii\authclient\AuthAction;
use yii\helpers\Url;

class SocialNetworkAuthenticateService implements ServiceInterface
{
    use ModuleAwareTrait;

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
        if (!$this->controller->module->enableSocialNetworkRegistration && ($account === null || $account->user === null)) {
            Yii::$app->session->setFlash('danger', Yii::t('usuario', 'Registration on this website is disabled'));
            $this->authAction->setSuccessUrl(Url::to(['/' . $this->getModule()->id . '/security/login']));

            return false;
        }
        if ($account === null) {
            $account = $this->createAccount();
            if (!$account) {
                Yii::$app->session->setFlash('danger', Yii::t('usuario', 'Unable to create an account.'));
                $this->authAction->setSuccessUrl(Url::to(['/' . $this->getModule()->id . '/security/login']));

                return false;
            }
        }

        $event = Yii::createObject(SocialNetworkAuthEvent::class, [$account, $this->client]);

        $this->controller->trigger(SocialNetworkAuthEvent::EVENT_BEFORE_AUTHENTICATE, $event);
        $result = false;

        if ($account->user instanceof User) {
            if ($account->user->getIsBlocked()) {
                Yii::$app->session->setFlash('danger', Yii::t('usuario', 'Your account has been blocked.'));
                $this->authAction->setSuccessUrl(Url::to(['/' . $this->getModule()->id . '/security/login']));
            } else {
                $result = Yii::$app->user->login($account->user, $this->controller->module->rememberLoginLifespan);
                if ($result) {
                    $account->user->updateAttributes([
                        'last_login_at' => time(),
                        'last_login_ip' => $this->controller->module->disableIpLogging ? '127.0.0.1' : Yii::$app->request->getUserIP(),
                    ]);
                    $this->authAction->setSuccessUrl(Yii::$app->getUser()->getReturnUrl());
                }
            }
        } else {
            $this->authAction->setSuccessUrl($account->getConnectionUrl());
            $result = true;
        }

        $this->controller->trigger(SocialNetworkAuthEvent::EVENT_AFTER_AUTHENTICATE, $event);
        return $result;
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
                'client_id' => $this->client->getUserId(),
                'data' => json_encode($data),
                'username' => $this->client->getUserName(),
                'email' => $this->client->getEmail(),
            ]
        );

        if (($user = $this->getUser($account)) instanceof User) {
            $account->user_id = $user->id;
        }

        if (!$account->save(false)) {
            return null;
        }

        return $account;
    }

    protected function getUser(SocialNetworkAccount $account)
    {
        return $this->userQuery->whereEmail($account->email)->one();
    }
}
