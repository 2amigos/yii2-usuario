<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\Usuario\Base\Event;

interface UsuarioEvent
{
    public const BEFORE_CREATE = 'beforeCreate';
    public const AFTER_CREATE = 'afterCreate';
    public const BEFORE_DELETE = 'beforeDelete';
    public const AFTER_DELETE = 'afterDelete';
    public const BEFORE_REGISTER = 'beforeRegister';
    public const AFTER_REGISTER = 'afterRegister';
    public const BEFORE_ACCOUNT_UPDATE = 'beforeAccountUpdate';
    public const AFTER_ACCOUNT_UPDATE = 'afterAccountUpdate';
    public const BEFORE_PROFILE_UPDATE = 'beforeProfileUpdate';
    public const AFTER_PROFILE_UPDATE = 'afterProfileUpdate';
    public const BEFORE_CONFIRMATION = 'beforeConfirmation';
    public const AFTER_CONFIRMATION = 'afterConfirmation';
    public const BEFORE_UNBLOCK = 'beforeUnblock';
    public const AFTER_UNBLOCK = 'afterUnblock';
    public const BEFORE_BLOCK = 'beforeBlock';
    public const AFTER_BLOCK = 'afterBlock';
    public const BEFORE_LOGOUT = 'beforeLogout';
    public const AFTER_LOGOUT = 'afterLogout';
    public const BEFORE_SWITCH_IDENTITY = 'beforeSwitchIdentity';
    public const AFTER_SWITCH_IDENTITY = 'afterSwitchIdentity';
}
