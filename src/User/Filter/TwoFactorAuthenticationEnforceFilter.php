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
use Yii;
use yii\base\ActionFilter;

class TwoFactorAuthenticationEnforceFilter extends ActionFilter
{
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

        /** @var User $identity */
        $permissions =  $module->twoFactorAuthenticationForcedPermissions;
        $identity = Yii::$app->user->identity;
        foreach ( $permissions as $permission){
            if (!$identity->auth_tf_enabled && Yii::$app->authManager->checkAccess($identity->id, $permission) ) {
                Yii::$app->session->setFlash('warning', Yii::t('usuario', 'Every user having your role has two factor authentication mandatory, you must enable it'));
                return Yii::$app->response->redirect(['/user/settings/account'])->send();
            }
        }
        return parent::beforeAction($action);
    }
}
