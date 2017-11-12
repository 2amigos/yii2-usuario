<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\User\Filter;

use Closure;
use Da\User\Model\User;
use yii\filters\AccessRule;

class AccessRuleFilter extends AccessRule
{
    /**
     * {@inheritdoc}
     * */
    protected function matchRole($user)
    {
        if (empty($this->roles)) {
            return true;
        }

        foreach ($this->roles as $role) {
            if ($role === '?') {
                if ($user->getIsGuest()) {
                    return true;
                }
            } elseif ($role === '@') {
                if (!$user->getIsGuest()) {
                    return true;
                }
            } elseif ($role === 'admin') {
                /** @var User $identity */
                $identity = $user->getIdentity();

                if (!$user->getIsGuest() && $identity->getIsAdmin()) {
                    return true;
                }
            } else {
                $roleParams = $this->roleParams instanceof Closure
                    ? call_user_func($this->roleParams, $this)
                    : $this->roleParams;

                if ($user->can($role, $roleParams)) {
                    return true;
                }
            }
        }

        return false;
    }
}
