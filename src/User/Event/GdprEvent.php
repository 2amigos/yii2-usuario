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

use Da\User\Model\User;
use yii\base\Event;

/**
 * @property-read User $user
 */
class GdprEvent extends Event
{
    const EVENT_BEFORE_DELETE = 'beforeDelete';
    const EVENT_AFTER_DELETE = 'afterDelete';
    /**
     * @var bool whether logic must continue after this event. Valid only for beforeDelete
     */
    public $isValid = true;

    protected $user;

    public function __construct(User $user, array $config = [])
    {
        $this->user = $user;
        parent::__construct($config);
    }

    public function getUser()
    {
        return $this->user;
    }
}
