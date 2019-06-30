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

/**
 * @property-read string $type
 * @property-read Usuario $user
 * @property-read MailService $mailService
 * @property-read mixed|\Exception $exception
 */
interface MailProcessEvent
{
    public const WELCOME_EMAIL = 'welcome';
    public const RECOVERY_EMAIL = 'recovery';
    public const CONFIRM_EMAIL = 'confirm';
    public const RECONFIRM_EMAIL = 'reconfirm';

    public const BEFORE_SEND_MAIL = 'beforeSendMail';
    public const AFTER_SEND_MAIL = 'afterSendMail';

    public function getType(): string;

    public function getUser(): Usuario;

    public function getMailService(): MailService;

    public function getException(): MailerException;
}
