<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\User\Event;

use Da\User\Model\User;
use yii\base\Event;

/**
 * @property-read User $user
 */
class UserEvent extends Event
{
    const EVENT_BEFORE_CREATE = 'beforeCreate';
    const EVENT_AFTER_CREATE = 'afterCreate';
    const EVENT_BEFORE_DELETE = 'beforeDelete';
    const EVENT_AFTER_DELETE = 'afterDelete';
    const EVENT_BEFORE_REGISTER = 'beforeRegister';
    const EVENT_AFTER_REGISTER = 'afterRegister';
    const EVENT_BEFORE_ACCOUNT_UPDATE = 'beforeAccountUpdate';
    const EVENT_AFTER_ACCOUNT_UPDATE = 'afterAccountUpdate';
    const EVENT_BEFORE_PROFILE_UPDATE = 'beforeProfileUpdate';
    const EVENT_AFTER_PROFILE_UPDATE = 'afterProfileUpdate';
    const EVENT_BEFORE_CONFIRMATION = 'beforeConfirmation';
    const EVENT_AFTER_CONFIRMATION = 'afterConfirmation';
    const EVENT_BEFORE_UNBLOCK = 'beforeUnblock';
    const EVENT_AFTER_UNBLOCK = 'afterUnblock';
    const EVENT_BEFORE_BLOCK = 'beforeBlock';
    const EVENT_AFTER_BLOCK = 'afterBlock';
    const EVENT_BEFORE_LOGOUT = 'beforeLogout';
    const EVENT_AFTER_LOGOUT = 'afterLogout';
    const EVENT_BEFORE_SWITCH_IDENTITY = 'beforeSwitchIdentity';
    const EVENT_AFTER_SWITCH_IDENTITY = 'afterSwitchIdentity';

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
