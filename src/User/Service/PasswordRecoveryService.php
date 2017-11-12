<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\User\Service;

use Da\User\Contracts\ServiceInterface;
use Da\User\Factory\TokenFactory;
use Da\User\Model\User;
use Da\User\Query\UserQuery;
use Da\User\Traits\MailAwareTrait;
use Exception;
use Yii;

class PasswordRecoveryService implements ServiceInterface
{
    use MailAwareTrait;

    protected $query;

    protected $email;
    protected $mailService;
    protected $securityHelper;

    public function __construct($email, MailService $mailService, UserQuery $query)
    {
        $this->email = $email;
        $this->mailService = $mailService;
        $this->query = $query;
    }

    public function run()
    {
        try {
            /** @var User $user */
            $user = $this->query->whereEmail($this->email)->one();

            $token = TokenFactory::makeRecoveryToken($user->id);

            if (!$token) {
                return false;
            }

            $this->mailService->setViewParam('user', $user);
            $this->mailService->setViewParam('token', $token);
            if (!$this->sendMail($user)) {
                return false;
            }

            Yii::$app->session->setFlash(
                'info',
                Yii::t('usuario', 'An email has been sent with instructions for resetting your password')
            );

            return true;
        } catch (Exception $e) {
            Yii::error($e->getMessage(), 'usuario');

            return false;
        }
    }
}
