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

use Da\User\Model\UsuarioToken;
use Da\Usuario\Base\Event\MailEvent;
use Da\Usuario\Base\Event\MailProcessEvent;
use Da\Usuario\Base\Model\Usuario;
use Da\Usuario\Base\Service\MailService;
use Yii;
use yii\base\InvalidConfigException;
use yii\di\NotInstantiableException;

class DefaultMailChange implements MailChangeStrategy
{
    /**
     * @var Usuario
     */
    protected $user;

    /**
     * DefaultMailChange constructor.
     * @param Usuario $user
     * @param string  $unconfirmed New email
     */
    public function __construct(Usuario $user, string $unconfirmed)
    {
        $this->user = $user;
        $this->user->unconfirmed_email = $unconfirmed;
    }

    /**
     * @throws InvalidConfigException
     * @return bool
     */
    public function run(): bool
    {
        $token = $this->createConfirmationToken($this->user->id);

        $mailService = $this->createReconfirmationMailerService($token);

        if ($mailService->run()) {
            Yii::$app
                ->session
                ->setFlash('info', Yii::t('usuario', 'A confirmation message has been sent to your new email address'));

            return $this->user->save();
        }

        return false;
    }

    /**
     * @param int $id
     *
     * @throws \yii\base\InvalidConfigException
     * @return UsuarioToken
     *
     */
    private function createConfirmationToken(int $id): UsuarioToken
    {
        $token = Yii::createObject([
            'class' => UsuarioToken::class,
            'user_id' => $id,
            'type' => MailEvent::CONFIRM_EMAIL,
        ]);

        $token->save(false);

        return $token;
    }

    /**
     * @param UsuarioToken $token
     *
     * @throws NotInstantiableException
     * @throws InvalidConfigException
     * @return MailService
     *
     */
    private function createReconfirmationMailerService(UsuarioToken $token): MailService
    {
        $config = Yii::$app->user->parameters['mail'];

        $from = $config['from'];
        $to = $this->user->unconfirmed_email;
        $subject = $config['subject']['reconfirmation'];
        $params = [
            'user' => $token->user ?? null,
            'token' => $token,
        ];

        /** @var MailService $mailer */
        $mailer = Yii::$container->get(
            MailService::class,
            [
                MailProcessEvent::RECONFIRM_EMAIL,
                $from,
                $to,
                $subject,
                'reconfirmation',
                $params,
            ]
        );

        return $mailer;
    }
}
