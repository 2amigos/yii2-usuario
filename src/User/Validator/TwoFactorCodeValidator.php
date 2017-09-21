<?php

namespace Da\User\Validator;

use Da\TwoFA\Manager;
use Da\User\Contracts\ValidatorInterface;
use Da\User\Model\User;

class TwoFactorCodeValidator implements ValidatorInterface
{
    protected $user;
    protected $code;
    protected $cycles;

    /**
     * TwoFactorCodeValidator constructor.
     *
     * @param User $user
     * @param $code
     * @param int $cycles
     */
    public function __construct(User $user, $code, $cycles = 0)
    {
        $this->user = $user;
        $this->code = $code;
        $this->cycles = $cycles;
    }

    /**
     * @return bool|int
     */
    public function validate()
    {
        $manager = new Manager();
        return $manager->setCycles($this->cycles)->verify($this->code, $this->user->auth_tf_key);
    }
}
