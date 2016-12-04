<?php
namespace Da\User\Event;

use Da\User\Model\User;
use yii\base\Event;

class UserEvent extends Event
{
    const EVENT_BEFORE_CREATE = 'beforeCreate';
    const EVENT_AFTER_CREATE = 'afterCreate';

    protected $user;

    public function __construct(User $user, array $config = [])
    {
        $this->user = $user;
        parent::__construct($config);
    }

    public function getUser()
    {
        return $this->user;
    }
}
