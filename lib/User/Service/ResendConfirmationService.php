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
use yii\log\Logger;

class ResendConfirmationService implements ServiceInterface
{
    protected $model;
    protected $mailService;
    protected $logger;

    public function __construct(User $model, MailService $mailService, Logger $logger)
    {
        $this->model = $model;
        $this->mailService = $mailService;
        $this->logger = $logger;
    }

    public function run()
    {
        if ($this->model && !$this->model->getIsConfirmed()) {
            $token = TokenFactory::makeConfirmationToken($this->model->id);
            $this->mailService->setViewParam('token', $token);

            return $this->mailService->run();
        }

        return false;
    }
}
