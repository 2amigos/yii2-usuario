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
use Da\User\Helper\SecurityHelper;
use Da\User\Model\User;

class ResetPasswordService implements ServiceInterface
{
    protected $password;
    protected $model;
    protected $securityHelper;

    public function __construct($password, User $model, SecurityHelper $securityHelper)
    {
        $this->password = $password;
        $this->model = $model;
        $this->securityHelper = $securityHelper;
    }

    public function run()
    {
        $this->model->password = $this->password;
        return (bool)$this->model->save(false, ['password_hash','password_changed_at']);
    }
}
