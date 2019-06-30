<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\Usuario\Base\Event;

use Da\User\Model\User;
use Da\Usuario\Base\Model\Usuario;
use yii\base\Event;

/**
 * @property-read Usuario $user
 */
class UserEvent extends Event implements UsuarioEvent
{
    protected $user;

    public function __construct(Usuario $user, array $config = [])
    {
        $this->user = $user;
        parent::__construct($config);
    }

    public function getUser(): Usuario
    {
        return $this->user;
    }
}
