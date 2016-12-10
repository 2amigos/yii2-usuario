<?php
namespace Da\User\Service;


use Da\User\Contracts\ServiceInterface;
use Da\User\Event\UserEvent;
use Da\User\Model\Token;
use Da\User\Model\User;
use Da\User\Query\TokenQuery;

class AccountConfirmationService implements ServiceInterface
{
    protected $model;
    protected $code;
    protected $tokenQuery;
    protected $userConfirmationService;

    public function __construct(
        $code,
        User $model,
        TokenQuery $tokenQuery,
        UserConfirmationService $userConfirmationService
    ) {
        $this->code = $code;
        $this->model = $model;
        $this->tokenQuery = $tokenQuery;
        $this->userConfirmationService = $userConfirmationService;
    }

    public function run()
    {
        $token = $this->tokenQuery
            ->whereUserId($this->model->id)
            ->whereCode($this->code)
            ->whereIsConfirmationType()
            ->one();

        if ($token instanceof Token && !$token->getIsExpired()) {
            $token->delete();

            return $this->userConfirmationService->run();
        }

        return false;
    }

}
