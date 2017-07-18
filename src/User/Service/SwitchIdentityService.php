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

use Da\User\Contracts\ServiceInterface;
use Da\User\Controller\AdminController;
use Da\User\Event\UserEvent;
use Da\User\Model\User;
use Da\User\Module;
use Da\User\Query\UserQuery;
use Da\User\Traits\ContainerAwareTrait;
use Yii;
use yii\web\ForbiddenHttpException;
use yii\web\IdentityInterface;

class SwitchIdentityService implements ServiceInterface
{
    use ContainerAwareTrait;

    protected $controller;
    protected $switchIdentitySessionKey;
    protected $userId;
    protected $userQuery;

    public function __construct(AdminController $controller, UserQuery $userQuery, $userId = null)
    {
        /** @var Module $module */
        $module = $controller->module;
        $this->controller = $controller;
        $this->switchIdentitySessionKey = $module->switchIdentitySessionKey;
        $this->userId = $userId;
        $this->userQuery = $userQuery;
    }

    public function run()
    {
        $session = Yii::$app->session;
        if (null === $this->userId) { // switch back identities
            $user = $this->userQuery->whereId($session->get($this->switchIdentitySessionKey))->one();
            $session->remove($this->switchIdentitySessionKey);
        } else {
            /** @var User $identity */
            $identity = Yii::$app->user->identity;
            if (!$identity->getIsAdmin()) {
                // Only admins allowed on module. Developers can override the service and implement different
                // approach. For example, by roles other than, and including, admin.
                throw new ForbiddenHttpException();
            }
            $user = $this->userQuery->whereId($this->userId)->one();
            $session->set($this->switchIdentitySessionKey, Yii::$app->user->id);
        }

        $event = $this->make(UserEvent::class, [$user]);

        $this->controller->trigger(UserEvent::EVENT_BEFORE_SWITCH_IDENTITY, $event);
        /** @var IdentityInterface $user */
        Yii::$app->user->switchIdentity($user, $session->timeout);
        $this->controller->trigger(UserEvent::EVENT_AFTER_SWITCH_IDENTITY, $event);
    }
}
