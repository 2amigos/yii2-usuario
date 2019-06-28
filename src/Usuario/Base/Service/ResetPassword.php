<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\Usuario\Base\Service;

use Da\User\Contracts\ServiceInterface;
use Da\User\Helper\Security;
use Da\User\Model\User;

class ResetPassword implements ServiceInterface
{
    protected $password;
    protected $model;
    protected $securityHelper;

    public function __construct($password, User $model, Security $securityHelper)
    {
        $this->password = $password;
        $this->model = $model;
        $this->securityHelper = $securityHelper;
    }

    public function run()
    {
        $this->model->password = $this->password;
        return (bool)$this->model->save(false, ['password_hash']);
    }
}
