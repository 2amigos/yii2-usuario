<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\User\Validator;

use Da\TwoFA\Exception\InvalidSecretKeyException;
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
     * @throws InvalidSecretKeyException
     * @return bool|int
     *
     */
    public function validate()
    {
        $manager = new Manager();
        return $manager->setCycles($this->cycles)->verify($this->code, $this->user->auth_tf_key);
    }
}
