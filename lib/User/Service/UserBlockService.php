<?php
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
            $this->controller->trigger(UserEvent::EVENT_BEFORE_UNBLOCK, $this->event);
            $result = (bool)$this->model->updateAttributes(['blocked_at' => null]);
            $this->controller->trigger(UserEvent::EVENT_AFTER_UNBLOCK, $this->event);
        } else {
            $this->controller->trigger(UserEvent::EVENT_BEFORE_BLOCK, $this->event);
            $result = (bool)$this->model->updateAttributes(
                ['blocked_at' => time(), 'auth_key' => $this->securityHelper->generateRandomString()]
            );
            $this->controller->trigger(UserEvent::EVENT_AFTER_BLOCK, $this->event);
        }
        return $result;
    }
}
