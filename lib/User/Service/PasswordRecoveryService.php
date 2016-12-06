<?php
namespace Da\User\Service;

use Da\User\Contracts\ServiceInterface;
use Da\User\Model\Token;
use Da\User\Model\User;
use Da\User\Query\UserQuery;
use Exception;
use Yii;
use yii\log\Logger;

class PasswordRecoveryService implements ServiceInterface
{
    protected $query;

    protected $email;
    protected $mailService;
    protected $securityHelper;
    protected $logger;

    public function __construct($email, MailService $mailService, UserQuery $query, Logger $logger)
    {
        $this->email = $email;
        $this->mailService = $mailService;
        $this->query = $query;
        $this->logger = $logger;
    }

    public function run()
    {
        try {
            /** @var User $user */
            $user = $this->query->whereEmail($this->email)->one();
            /** @var Token $token */
            $token = Yii::createObject(
                [
                    'class' => Token::class,
                    'user_id' => $user->id,
                    'type' => Token::TYPE_RECOVERY
                ]
            );

            if (!$token->save(false)) {
                return false;
            }

            $this->mailService->setViewParam('user', $user);
            $this->mailService->setViewParam('token', $token);

            if (!$this->mailService->run()) {
                return false;
            }

            return true;

        } catch (Exception $e) {
            $this->logger->log($e->getMessage(), Logger::LEVEL_ERROR);

            return false;
        }
    }

}
