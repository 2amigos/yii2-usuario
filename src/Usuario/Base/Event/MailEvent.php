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

use Da\Usuario\Base\Exception\MailerException;
use Da\Usuario\Base\Model\Usuario;
use Da\Usuario\Base\Service\MailService;
use yii\base\Event;

/**
 * @property-read string $type
 * @property-read Usuario $user
 * @property-read MailService $mailService
 * @property-read mixed|\Exception $exception
 */
class MailEvent extends Event implements MailProcessEvent
{
    protected $type;
    protected $user;
    protected $mailService;
    protected $exception;

    public function __construct($type, Usuario $user, MailService $mailService, $exception, $config = [])
    {
        $this->type = $type;
        $this->user = $user;
        $this->mailService = $mailService;
        $this->exception = $exception;

        parent::__construct($config);
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getUser(): Usuario
    {
        return $this->user;
    }

    public function getMailService(): MailService
    {
        return $this->mailService;
    }

    public function getException(): MailerException
    {
        return $this->exception;
    }
}
