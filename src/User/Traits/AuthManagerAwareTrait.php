<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\User\Traits;

use Da\User\Contracts\AuthManagerInterface;
use Yii;
use yii\base\InvalidConfigException;

trait AuthManagerAwareTrait
{
    /**
     * @return AuthManagerInterface
     */
    public function getAuthManager()
    {
        $authManager = Yii::$app->getAuthManager();
        if($authManager instanceof AuthManagerInterface) {
            return $authManager;
        }
        throw new InvalidConfigException("AuthManager must implement Da\User\Contracts\AuthManagerInterface");
    }
}
