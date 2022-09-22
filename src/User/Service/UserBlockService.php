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
use Da\User\Controller\api\v1\AdminController as AdminControllerRest;
use Da\User\Event\UserEvent;
use Da\User\Helper\SecurityHelper;
use Da\User\Model\User;
use TypeError;

class UserBlockService implements ServiceInterface
{
    protected $model;
    protected $event;
    protected $controller;
    protected $securityHelper;

    public function __construct(
        User $model,
        UserEvent $event,
        $controller,
        SecurityHelper $securityHelper
    ) {
        if (!in_array(get_class($controller), [AdminController::class, AdminControllerRest::class])) {
            throw new TypeError('Argument controller must be either of type ' 
                . AdminController::class . ' or ' . AdminControllerRest::class . ', ' . get_class($controller) . ' given');
        }
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
