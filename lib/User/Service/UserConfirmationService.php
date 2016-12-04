<?php
namespace Da\User\Service;

use Da\User\Contracts\ServiceInterface;
use Da\User\Event\UserEvent;
use Da\User\Model\User;

class UserConfirmationService implements ServiceInterface
{
    protected $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function run()
    {
        $this->model->trigger(UserEvent::EVENT_BEFORE_CONFIRMATION);
        $result = (bool) $this->model->updateAttributes(['confirmed_at' => time()]);
        $this->model->trigger(UserEvent::EVENT_AFTER_CONFIRMATION);

        return $result;
    }
}
