<?php
namespace Da\User\Service;


use Da\User\Contracts\ServiceInterface;
use Da\User\Factory\TokenFactory;
use Da\User\Helper\SecurityHelper;
use Da\User\Model\Token;
use Da\User\Model\User;
use Da\User\Query\UserQuery;
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
        if($this->model && !$this->model->getIsConfirmed()) {
            $token = TokenFactory::makeConfirmationToken($this->model->id);
            $this->mailService->setViewParam('token', $token);

            return $this->mailService->run();
        }

        return false;
    }

}
