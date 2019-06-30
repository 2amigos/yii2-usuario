<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\Usuario\Base\Model\Query;

use yii\db\ActiveQuery;

class UserQuery extends ActiveQuery
{
    /**
     * @param string $usernameOrEmail
     *
     * @return $this
     */
    public function whereUsernameOrEmailIs(string $usernameOrEmail): self
    {
        return filter_var($usernameOrEmail, FILTER_VALIDATE_EMAIL)
            ? $this->whereEmailIs($usernameOrEmail)
            : $this->whereUsernameIs($usernameOrEmail);
    }

    /**
     * @param string $email
     *
     * @return $this
     */
    public function whereEmailIs(string $email): self
    {
        return $this->andWhere(['email' => $email]);
    }

    /**
     * @param string $username
     *
     * @return $this
     */
    public function whereUsernameIs(string $username): self
    {
        return $this->andWhere(['username' => $username]);
    }

    /**
     * @param int $id
     *
     * @return $this
     */
    public function whereIdIs(int $id): self
    {
        return $this->andWhere(['id' => $id]);
    }

    /**
     * @param int $id
     *
     * @return $this
     */
    public function whereIdIsNot($id): self
    {
        return $this->andWhere(['<>', 'id', $id]);
    }
}
