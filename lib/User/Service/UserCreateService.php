<?php
namespace Da\User\Service;

use Da\User\Contracts\ServiceInterface;
use Da\User\Helper\SecurityHelper;
use Da\User\Model\User;
use yii\base\InvalidCallException;
use Exception;
use yii\db\ActiveRecord;
use yii\log\Logger;

class UserCreateService implements ServiceInterface
{
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

    /**
     * @return bool
     */
    public function run()
    {
        $model = $this->model;

        if ($model->getIsNewRecord() === false) {
            throw new InvalidCallException('Cannot create a new user from an existing one.');
        }

        $transaction = $model->getDb()->beginTransaction();

        try {
            $model->confirmed_at = time();
            $model->password = $model->password !== null
                ? $model->password
                : $this->securityHelper->generatePassword(8);

            $model->trigger(ActiveRecord::EVENT_BEFORE_INSERT);

            if (!$model->save()) {
                $transaction->rollBack();

                return false;
            }

            $model->trigger(ActiveRecord::EVENT_AFTER_INSERT);

            $this->mailService->run();
            $transaction->commit();

            return true;

        } catch (Exception $e) {
            $transaction->rollBack();
            $this->logger->log($e->getMessage(), Logger::LEVEL_ERROR);

            return false;
        }
    }

}
