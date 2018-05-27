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

use Yii;
use Da\User\Contracts\ServiceInterface;
use Da\User\Model\User;

class PasswordExpireService implements ServiceInterface
{
    protected $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function run()
    {
        return $this->model->updateAttributes([
            'last_login_at' => time(),
            'last_login_ip' => Yii::$app->request->getUserIP(),
        ]);
    }
}