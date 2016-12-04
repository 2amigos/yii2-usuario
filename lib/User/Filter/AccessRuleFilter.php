<?php

namespace Da\User\Filter;

use Da\User\Model\User;
use yii\filters\AccessRule;


class AccessRuleFilter extends AccessRule
{
    /**
     * @inheritdoc
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
            } elseif ($user->can($role)) {
                return true;
            }
        }

        return false;
    }
}
