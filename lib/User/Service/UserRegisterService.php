<?php
namespace Da\User\Service;

use Da\User\Contracts\ServiceInterface;
use Da\User\Event\UserEvent;
use Da\User\Factory\TokenFactory;
use Da\User\Helper\SecurityHelper;
use Da\User\Model\Token;
use Da\User\Model\User;
use Da\User\Traits\ModuleTrait;
use yii\base\InvalidCallException;
use yii\log\Logger;
use Exception;

class UserRegisterService implements ServiceInterface
{
    use ModuleTrait;

    protected $model;
    protected $securityHelper;
    protected $mailService;
    protected $logger;

    public function __construct(User $model, MailService $mailService, SecurityHelper $securityHelper, Logger $logger)
    {
        $this->model = $model;
        $this->mailService = $mailService;
        $this->securityHelper = $securityHelper;
        $this->logger = $logger;
    }

    public function run()
    {
        $model = $this->model;

        if ($model->getIsNewRecord() === false) {
            throw new InvalidCallException('Cannot register user from an existing one.');
        }

        $transaction = $model->getDb()->beginTransaction();

        try {
            $model->confirmed_at = $this->getModule()->enableEmailConfirmation ? null : time();
            $model->password = $this->getModule()->generatePasswords
                ? $this->securityHelper->generatePassword(8)
                : $model->password;

            $model->trigger(UserEvent::EVENT_BEFORE_REGISTER);

            if(!$model->save()) {
                $transaction->rollBack();
                return false;
            }

            if($this->getModule()->enableEmailConfirmation) {
                $token = TokenFactory::makeConfirmationToken($model->id);
            }

            if(isset($token)) {
                $this->mailService->setViewParam('token', $token);
            }
            $this->mailService->run();

            $model->trigger(UserEvent::EVENT_AFTER_REGISTER);

            $transaction->commit();

            return true;

        } catch(Exception $e) {
            $transaction->rollBack();
            $this->logger->log($e->getMessage(), Logger::LEVEL_WARNING);

            return false;
        }
    }

}
