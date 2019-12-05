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

use yii\base\Event;
use yii\base\Model;

/**
 * @property-read Model $form
 */
class FormEvent extends Event
{
    const EVENT_BEFORE_REQUEST = 'beforeRequest';
    const EVENT_AFTER_REQUEST = 'afterRequest';
    const EVENT_BEFORE_RESEND = 'beforeResend';
    const EVENT_AFTER_RESEND = 'afterResend';
    const EVENT_BEFORE_LOGIN = 'beforeLogin';
    const EVENT_AFTER_LOGIN = 'afterLogin';
    const EVENT_BEFORE_REGISTER = 'beforeRegister';
    const EVENT_AFTER_REGISTER = 'afterRegister';
    const EVENT_FAILED_LOGIN = 'failedLogin';

    protected $form;

    public function __construct(Model $form, array $config = [])
    {
        $this->form = $form;
        parent::__construct($config);
    }

    public function getForm()
    {
        return $this->form;
    }
}
