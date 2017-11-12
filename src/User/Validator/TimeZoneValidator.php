<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\User\Validator;

use Da\User\Contracts\ValidatorInterface;

class TimeZoneValidator implements ValidatorInterface
{
    protected $timezone;

    public function __construct($timezone)
    {
        $this->timezone = $timezone;
    }

    public function validate()
    {
        return in_array($this->timezone, timezone_identifiers_list(), false);
    }
}
