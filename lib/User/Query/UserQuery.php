<?php

namespace Da\User\Query;

use yii\db\ActiveQuery;

class UserQuery extends ActiveQuery
{
    /**
     * @param $usernameOrEmail
     *
     * @return $this
     */
    public function whereUsernameOrEmail($usernameOrEmail)
    {
        return filter_var($usernameOrEmail, FILTER_VALIDATE_EMAIL)
            ? $this->whereEmail($usernameOrEmail)
            : $this->whereUsername($usernameOrEmail);
    }

    /**
     * @param $email
     *
     * @return $this
     */
    public function whereEmail($email)
    {
        return $this->andWhere(['email' => $email]);
    }

    /**
     * @param $username
     *
     * @return $this
     */
    public function whereUsername($username)
    {
        return $this->andWhere(['username' => $username]);
    }

    /**
     * @param $id
     *
     * @return $this
     */
    public function whereId($id)
    {
        return $this->andWhere(['id' => $id]);
    }


    /**
     * @param $id
     *
     * @return $this
     */
    public function whereNotId($id)
    {
        return $this->andWhere(['<>', 'id', $id]);
    }
}
