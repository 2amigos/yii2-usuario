<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\User\Contracts;

interface MailChangeStrategyInterface extends StrategyInterface
{
    const TYPE_INSECURE = 0;
    const TYPE_DEFAULT = 1;
    const TYPE_SECURE = 2;
}
