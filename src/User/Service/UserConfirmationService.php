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
use Da\User\Traits\MailAwareTrait;

class UserConfirmationService implements ServiceInterface
{
    use MailAwareTrait;
    
    protected $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function run()
    {
        $model = $this->model;
        $event = $this->make(UserEvent::class, [$model]);
        
        $this->model->trigger(UserEvent::EVENT_BEFORE_CONFIRMATION, $event);
        if ((bool)$this->model->updateAttributes(['confirmed_at' => time()])) {
            $this->model->trigger(UserEvent::EVENT_AFTER_CONFIRMATION, $event);

            return true;
        }

        return false;
    }
}
