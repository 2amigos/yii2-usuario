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

use Da\User\Form\RecoveryForm;
use Da\User\Model\Token;
use yii\base\Event;

/**
 * @property-read Token $token
 * @property-read RecoveryForm $form
 */
class ResetPasswordEvent extends Event
{
    const EVENT_BEFORE_TOKEN_VALIDATE = 'beforeTokenValidate';
    const EVENT_AFTER_TOKEN_VALIDATE = 'afterTokenValidate';
    const EVENT_BEFORE_RESET = 'beforeReset';
    const EVENT_AFTER_RESET = 'afterReset';

    protected $form;
    protected $token;

    public function __construct(Token $token = null, RecoveryForm $form = null, array $config = [])
    {
        $this->form = $form;
        $this->token = $token;

        parent::__construct($config);
    }

    public function getForm()
    {
        return $this->form;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function updateForm(RecoveryForm $form)
    {
        return new static($this->getToken(), $form);
    }
}
