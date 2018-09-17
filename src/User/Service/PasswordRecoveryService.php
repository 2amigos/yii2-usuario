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
use Da\User\Traits\ModuleAwareTrait;
use Exception;
use Yii;

class PasswordRecoveryService implements ServiceInterface
{
    use MailAwareTrait;
    use ModuleAwareTrait;

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
            if ($this->getModule()->enableFlashMessages == true) {
                Yii::$app->session->setFlash(
                    'info',
                    Yii::t('usuario', 'An email with instructions to create a new password has been sent to {email} if it is associated with an {appName} account. Your existing password has not been changed.', ['email' => $this->email, 'appName' => Yii::$app->name])
                );
            }

            /** @var User $user */
            $user = $this->query->whereEmail($this->email)->one();

            if ($user === null) {
                throw new \RuntimeException('User not found.');
            }

            $token = TokenFactory::makeRecoveryToken($user->id);

            if (!$token) {
                return false;
            }

            $this->mailService->setViewParam('user', $user);
            $this->mailService->setViewParam('token', $token);
            if (!$this->sendMail($user)) {
                return false;
            }

            return true;
        } catch (Exception $e) {
            Yii::error($e->getMessage(), 'usuario');

            return false;
        }
    }
}
