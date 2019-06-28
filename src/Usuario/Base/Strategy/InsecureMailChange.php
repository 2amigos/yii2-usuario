<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\Usuario\Base\Strategy;

use Da\User\Form\SettingsForm;
use Da\Usuario\Base\Contracts\MailChangeStrategy;

class InsecureMailChange implements MailChangeStrategy
{
    protected $form;

    public function __construct(SettingsForm $form)
    {
        $this->form = $form;
    }

    public function run(): bool
    {
        $this->form->getUser()->email = $this->form->email;

        return $this->form->getUser()->save();
    }
}
