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
use Da\User\Helper\SecurityHelper;
use Da\User\Model\User;

class UserBlockService implements ServiceInterface
{
    protected $model;
    protected $event;
    protected $controller;
    protected $securityHelper;

    public function __construct(
        User $model,
        UserEvent $event,
        AdminController $controller,
        SecurityHelper $securityHelper
    ) {
        $this->model = $model;
        $this->event = $event;
        $this->controller = $controller;
        $this->securityHelper = $securityHelper;
    }

    public function run()
    {
        if ($this->model->getIsBlocked()) {
            $this->triggerEvents(UserEvent::EVENT_BEFORE_UNBLOCK);
            $result = (bool)$this->model->updateAttributes(['blocked_at' => null]);
            $this->triggerEvents(UserEvent::EVENT_AFTER_UNBLOCK);
        } else {
            $this->triggerEvents(UserEvent::EVENT_BEFORE_BLOCK);
            $result = (bool)$this->model->updateAttributes(
                ['blocked_at' => time(), 'auth_key' => $this->securityHelper->generateRandomString()]
            );
            $this->triggerEvents(UserEvent::EVENT_AFTER_BLOCK);
        }

        return $result;
    }

    protected function triggerEvents($name)
    {
        $this->controller->trigger($name, $this->event);
        $this->model->trigger($name, $this->event);
    }
}
