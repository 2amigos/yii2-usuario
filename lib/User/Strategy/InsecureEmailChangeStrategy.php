<?php

namespace Da\User\Strategy;

use Da\User\Contracts\MailChangeStrategyInterface;
use Da\User\Form\SettingsForm;

class InsecureEmailChangeStrategy implements MailChangeStrategyInterface
{
    protected $form;

    public function __construct(SettingsForm $form)
    {
        $this->form = $form;
    }

    public function run()
    {
        $this->form->getUser()->email = $this->form->email;

        return $this->form->getUser()->save();
    }
}
