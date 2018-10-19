<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\User\Event;

use Da\User\Model\Profile;
use yii\base\Event;

/**
 * @property-read Profile $profile
 */
class ProfileEvent extends Event
{
    protected $profile;

    public function __construct(Profile $profile, array $config = [])
    {
        $this->profile = $profile;

        parent::__construct($config);
    }

    public function getProfile()
    {
        return $this->profile;
    }
}
