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
use Da\User\Traits\ModuleAwareTrait;
use Yii;
use yii\filters\AccessRule;

class AccessRuleFilter extends AccessRule
{
    use ModuleAwareTrait;

    /**
     * @inheritDoc
     */
    public function allows($action, $user, $request)
    {
        $consentAction = 'user/settings/gdpr-consent';
        if (!$user->isGuest && $action->uniqueId !== $consentAction) {
            $module = $this->getModule();
            if ($module->gdprRequireConsentToAll) {
                $excludedUrls = $module->gdprConsentExcludedUrls;
                $excludedUrls[] = $module->gdprPrivacyPolicyUrl;
                foreach ($excludedUrls as $url) {
                    if (!fnmatch($url, $action->uniqueId)) {
                        /** @var User $identity */
                        $identity = $user->identity;
                        if (!$identity->gdpr_consent) {
                            Yii::$app->response->redirect([ "/$consentAction"])->send();
                        }
                    }
                }
            }
        }
        return parent::allows($action, $user, $request);
    }

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
