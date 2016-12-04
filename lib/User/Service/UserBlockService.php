<?php
namespace Da\User\Service;

use Da\User\Contracts\ServiceInterface;
use Da\User\Event\UserEvent;
use Da\User\Model\User;

class UserBlockService implements ServiceInterface
{
    protected $model;
    protected $event;

    public function __construct(User $model, UserEvent $event)
    {
        $this->model = $model;
        $this->event = $event;
    }

    public function run()
    {

    }
}
