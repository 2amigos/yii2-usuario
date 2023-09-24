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

use Da\User\Model\User;
use Da\User\Module;
use Da\User\Traits\AuthManagerAwareTrait;
use Yii;
use yii\base\ActionFilter;

class TwoFactorAuthenticationEnforceFilter extends ActionFilter
{
    use AuthManagerAwareTrait;

    public function beforeAction($action)
    {
        /** @var Module $module */
        $module = Yii::$app->getModule('user');

        $enableTwoFactorAuthentication = $module->enableTwoFactorAuthentication;
        // If enableTwoFactorAuthentication is set to false do nothing
        if (!$enableTwoFactorAuthentication) {
            return parent::beforeAction($action);
        }

        if (Yii::$app->user->isGuest) {
            // Not our business
            return parent::beforeAction($action);
        }

        $permissions = $module->twoFactorAuthenticationForcedPermissions;

        $user = Yii::$app->user->identity;
        $itemsByUser = array_keys($this->getAuthManager()->getItemsByUser($user->id));
        if (!empty(array_intersect($permissions, $itemsByUser)) && !$user->auth_tf_enabled) {
            Yii::$app->session->setFlash('warning', Yii::t('usuario', 'Your role requires 2FA, you won\'t be able to use the application until you enable it'));
            return Yii::$app->response->redirect(['/user/settings/account'])->send();
        }

        return parent::beforeAction($action);
    }
}
