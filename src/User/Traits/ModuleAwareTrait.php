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

use Da\User\Module;
use Yii;
use yii\base\InvalidConfigException;

/**
 * @property-read Module $module
 */
trait ModuleAwareTrait
{

    public function getModule() : Module
    {
        $module = Yii::$app->getModule('user');
        if($module instanceof Module) {
            return $module;
        }
        throw new InvalidConfigException("Expecting Da\User\Module here!");
    }
}
