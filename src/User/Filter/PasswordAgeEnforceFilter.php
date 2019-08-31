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

class PasswordAgeEnforceFilter extends ActionFilter
{
    public function beforeAction($action)
    {
        /** @var Module $module */
        $module = Yii::$app->getModule('user');
        $maxPasswordAge = $module->maxPasswordAge;
        // If feature is not set do nothing (or raise a configuration error?)
        if (is_null($maxPasswordAge)) {
            return parent::beforeAction($action);
        }
        if (Yii::$app->user->isGuest) {
            // Not our business
            return parent::beforeAction($action);
        }
        /** @var User $identity */
        $identity = Yii::$app->user->identity;
        if ($identity->password_age >= $maxPasswordAge) {
            // Force password change
            Yii::$app->getSession()->setFlash('warning', Yii::t('usuario', 'Your password has expired, you must change it now'));
            return Yii::$app->response->redirect(['/user/settings/account'])->send();
        }

        return parent::beforeAction($action);
    }
}
