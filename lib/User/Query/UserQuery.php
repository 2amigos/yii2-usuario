<?php

namespace Da\User\Query;

use yii\db\ActiveQuery;

class UserQuery extends ActiveQuery
{
    public function whereUsernameOrEmail($usernameOrEmail)
    {
        return filter_var($usernameOrEmail, FILTER_VALIDATE_EMAIL)
            ? $this->whereEmail($usernameOrEmail)
            : $this->whereUsername($usernameOrEmail);
    }

    public function whereEmail($email)
    {
        return $this->andWhere(['email' => $email]);
    }

    public function whereUsername($username)
    {
        return $this->andWhere(['username' => $username]);
    }
}
