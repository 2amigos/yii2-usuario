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
use Da\User\Traits\MailAwareTrait;

class ResendConfirmationService implements ServiceInterface
{
    use MailAwareTrait;

    protected $model;
    protected $mailService;

    public function __construct(User $model, MailService $mailService)
    {
        $this->model = $model;
        $this->mailService = $mailService;
    }

    public function run()
    {
        if ($this->model && !$this->model->getIsConfirmed()) {
            $token = TokenFactory::makeConfirmationToken($this->model->id);
            $this->mailService->setViewParam('token', $token);

            return $this->sendMail($this->model);
        }

        return false;
    }
}
