<?php

namespace Da\User\Controller\api\v1\models;

use Da\User\Model\User;
use yii\base\Model;

class ApiUser extends Model
{
    public string $username;
    public string $email;
    public int $confirmed_at;
    public int $updated_at;
    public int $created_at;
    public int $last_login_at;
    public string $last_login_ip;

    /**
     * @inheritdoc
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->username = $user->username;
        $this->email = $user->email;

        $this->confirmed_at = $user->confirmed_at;
        $this->updated_at = $user->updated_at;
        $this->created_at = $user->created_at;
        $this->last_login_at = $user->last_login_at;
        $this->last_login_ip = $user->last_login_ip;
    }
}
