<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\User\Service;

use Da\User\Contracts\ServiceInterface;
use Da\User\Event\UserEvent;
use Da\User\Model\User;

class UserConfirmationService implements ServiceInterface
{
    protected $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function run()
    {
        $this->model->trigger(UserEvent::EVENT_BEFORE_CONFIRMATION);
        if ((bool)$this->model->updateAttributes(['confirmed_at' => time()])) {
            $this->model->trigger(UserEvent::EVENT_AFTER_CONFIRMATION);

            return true;
        }

        return false;
    }
}
