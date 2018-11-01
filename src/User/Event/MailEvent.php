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
use Da\User\Service\MailService;
use yii\base\Event;

/**
 * @property-read string $type
 * @property-read User $user
 * @property-read MailService $mailService
 * @property-read mixed|\Exception $exception
 */
class MailEvent extends Event
{
    const TYPE_WELCOME = 'welcome';
    const TYPE_RECOVERY = 'recovery';
    const TYPE_CONFIRM = 'confirm';
    const TYPE_RECONFIRM = 'reconfirm';

    const EVENT_BEFORE_SEND_MAIL = 'beforeSendMail';
    const EVENT_AFTER_SEND_MAIL = 'afterSendMail';

    protected $type;
    protected $user;
    protected $mailService;
    protected $exception;

    public function __construct($type, User $user, MailService $mailService, $exception, $config = [])
    {
        $this->type = $type;
        $this->user = $user;
        $this->mailService = $mailService;
        $this->exception = $exception;

        parent::__construct($config);
    }

    public function getType()
    {
        return $this->type;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getMailService()
    {
        return $this->mailService;
    }

    public function getException()
    {
        return $this->exception;
    }
}
