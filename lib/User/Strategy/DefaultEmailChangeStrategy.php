<?php

namespace Da\User\Strategy;

use Da\User\Contracts\MailChangeStrategyInterface;
use Da\User\Factory\MailFactory;
use Da\User\Factory\TokenFactory;
use Da\User\Form\SettingsForm;
use Da\User\Traits\ContainerTrait;
use Yii;

class DefaultEmailChangeStrategy implements MailChangeStrategyInterface
{
    use ContainerTrait;

    protected $form;

    public function __construct(SettingsForm $form)
    {
        $this->form = $form;
    }

    public function run()
    {
        $this->form->getUser()->unconfirmed_email = $this->form->email;

        $token = TokenFactory::makeConfirmNewMailToken($this->form->getUser()->id);

        $mailService = MailFactory::makeReconfirmationMailerService($this->form->getUser(), $token);

        if ($mailService->run()) {
            Yii::$app
                ->session
                ->setFlash('info', Yii::t('user', 'A confirmation message has been sent to your new email address'));

            return $this->form->getUser()->save();
        }

        return false;
    }
}
