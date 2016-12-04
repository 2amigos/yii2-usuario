<?php
namespace Da\User\Service;

use Da\User\Contracts\ServiceInterface;
use Da\User\Helper\SecurityHelper;
use Da\User\Model\User;
use yii\base\InvalidCallException;
use Exception;
use yii\log\Logger;

/**
 *
 * UserCreateService.php
 *
 * Date: 4/12/16
 * Time: 2:55
 * @author Antonio Ramirez <hola@2amigos.us>
 */
class UserCreateService implements ServiceInterface
{
    protected $model;
    protected $securityHelper;
    protected $logger;

    public function __construct(User $model, SecurityHelper $securityHelper, Logger $logger)
    {
        $this->model = $model;
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

            // TODO: Trigger BEFORE CREATE EVENT

            if (!$model->save()) {
                $transaction->rollBack();

                return false;
            }

            // TODO: Send welcome message

            $transaction->commit();

            return true;

        } catch (Exception $e) {
            $transaction->rollBack();
            $this->logger->log($e->getMessage(), Logger::LEVEL_WARNING);

            return false;
        }
    }

}
