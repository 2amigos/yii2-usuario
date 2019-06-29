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

use yii\base\Event;
use yii\base\Model;

/**
 * @property-read Model $form
 */
class FormEvent extends Event
{
    public const EVENT_BEFORE_REQUEST = 'beforeRequest';
    public const EVENT_AFTER_REQUEST = 'afterRequest';
    public const EVENT_BEFORE_RESEND = 'beforeResend';
    public const EVENT_AFTER_RESEND = 'afterResend';
    public const EVENT_BEFORE_LOGIN = 'beforeLogin';
    public const EVENT_AFTER_LOGIN = 'afterLogin';
    public const EVENT_BEFORE_REGISTER = 'beforeRegister';
    public const EVENT_AFTER_REGISTER = 'afterRegister';

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
