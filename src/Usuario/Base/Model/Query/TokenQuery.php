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

use Da\User\Model\Token;
use Da\User\Model\UsuarioToken;
use yii\db\ActiveQuery;

class TokenQuery extends ActiveQuery
{
    /**
     * @param int $id
     *
     * @return $this
     */
    public function whereUserIdIs(int $id): self
    {
        return $this->andWhere(['user_id' => $id]);
    }

    /**
     * @param string $code
     *
     * @return $this
     */
    public function whereCodeIs(string $code): self
    {
        return $this->andWhere(['code' => $code]);
    }

    /**
     * @return $this
     */
    public function whereIsOfRecoveryType(): self
    {
        return $this->andWhere(['type' => UsuarioToken::TYPE_RECOVERY]);
    }

    /**
     * @return $this
     */
    public function whereIsOfConfirmationType(): self
    {
        return $this->andWhere(['type' => UsuarioToken::TYPE_CONFIRMATION]);
    }

    /**
     * @param array $types
     *
     * @return $this
     */
    public function whereInTypes(array $types): self
    {
        return $this->andWhere(['in', 'type', $types]);
    }
}
