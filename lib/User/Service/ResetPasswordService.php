<?php
namespace Da\User\Service;


use Da\User\Contracts\ServiceInterface;
use Da\User\Helper\SecurityHelper;
use Da\User\Model\User;

class ResetPasswordService implements ServiceInterface
{
    protected $password;
    protected $model;
    protected $securityHelper;

    public function __construct($password, User $model, SecurityHelper $securityHelper)
    {
        $this->password;
        $this->model = $model;
        $this->securityHelper = $securityHelper;
    }

    public function run()
    {

        return $this->model && (bool)$this->model->updateAttributes(
                [
                    'password_hash' => $this->securityHelper->generatePasswordHash($this->password)
                ]
            );
    }

}
