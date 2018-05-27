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

use Da\User\Event\MailEvent;
use Da\User\Model\User;
use Da\User\Service\MailService;
use Exception;
use Yii;
use yii\base\InvalidConfigException;

/**
 * @property MailService $mailService
 */
trait MailAwareTrait
{
    use ContainerAwareTrait;

    /**
     * Sends a mailer
     *
     * @param User $user
     *
     * @throws InvalidConfigException
     * @return bool
     */
    protected function sendMail(User $user)
    {
        $type = $this->mailService->getType();
        $event = $this->make(MailEvent::class, [$type, $user, $this->mailService, null]);
        $user->trigger(MailEvent::EVENT_BEFORE_SEND_MAIL, $event);
        try {
            $this->mailService->run();
        } catch (Exception $e) {
            $event = $this->make(MailEvent::class, [$type, $user, $this->mailService, $e]);
            Yii::error($e->getMessage(), 'usuario');
            $user->trigger(MailEvent::EVENT_AFTER_SEND_MAIL, $event);
            return false;
        }
        $user->trigger(MailEvent::EVENT_AFTER_SEND_MAIL, $event);
        return true;
    }
}
