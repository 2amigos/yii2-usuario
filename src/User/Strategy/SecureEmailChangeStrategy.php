<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\User\Strategy;

use Da\User\Contracts\MailChangeStrategyInterface;
use Da\User\Factory\MailFactory;
use Da\User\Factory\TokenFactory;
use Da\User\Form\SettingsForm;
use Da\User\Model\User;
use Da\User\Traits\ContainerAwareTrait;
use Yii;

class SecureEmailChangeStrategy implements MailChangeStrategyInterface
{
    use ContainerAwareTrait;

    protected $form;

    public function __construct(SettingsForm $form)
    {
        $this->form = $form;
    }

    public function run()
    {
        if ($this->make(DefaultEmailChangeStrategy::class, [$this->form])->run()) {
            $token = TokenFactory::makeConfirmOldMailToken($this->form->getUser()->id);
            $mailService = MailFactory::makeReconfirmationMailerService($this->form->getUser(), $token);

            if ($mailService->run()) {
                // unset flags if they exist
                $this->form->getUser()->flags &= ~User::NEW_EMAIL_CONFIRMED;
                $this->form->getUser()->flags &= ~User::OLD_EMAIL_CONFIRMED;
                if ($this->form->getUser()->save(false)) {
                    Yii::$app
                        ->session
                        ->setFlash(
                            'info',
                            Yii::t(
                                'usuario',
                                'We have sent confirmation links to both old and new email addresses. You must click both links to complete your request.'
                            )
                        );

                    return true;
                }
            }
        }

        return false;
    }
}
